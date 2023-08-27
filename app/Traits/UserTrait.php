<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait UserTrait
{
  public static function search()
  {
    if (Cache::store('file')->has('crm_users')) {
      return Cache::store('file')->get('crm_users')->data;
    }

    $data = BX::firstBatch('user.search', [
      'USER_TYPE' => 'employee',
      'ACTIVE' => true,
    ]);
    $resource = \App\Http\Resources\CRM\UserResource::collection($data)->response()->getData();
    Cache::store('file')->forever('crm_users', $resource);

    return $resource->data;
  }

  public static function withFired($force = false)
  {
    if (!$force && Cache::store('file')->has('crm_all_users')) {
      return Cache::store('file')->get('crm_all_users');
    }

    $data = BX::firstBatch('user.get', [
      'USER_TYPE' => 'employee',
    ]);
    $resource = \App\Http\Resources\CRM\UserResource::collection($data)->response()->getData();
    Cache::store('file')->forever('crm_all_users', $resource);

    return $resource;
  }

  public static function hotPaymentsNotifyPreparing()
  {
    $contracts = \App\Models\Contract::join('departments AS d', 'd.id', 'contracts.department_id')
      ->join('l_l_c_s AS l', 'l.id', 'd.l_l_c_s_id')
      ->join('contract_types AS ct', 'ct.id', 'contracts.contract_type_id')
      ->where('contracts.notified', false)
      ->whereRaw('DATEDIFF(DATE(next_payment_date), NOW()) < 3')
      ->select('contracts.*', 'ct.value AS type_name', 'd.name AS dep_name', 'l.name AS llc_name', 'l.manager_id AS manager_id')
      ->get();

    foreach ($contracts as $c) {
      $message = "{$c->type_name} ({$c->dep_name} | {$c->llc_name}) ожидает оплаты, крайний срок: {$c->next_payment_date->format("d.m.Y")}";
      self::SendNotification($c->manager_id, $message);
      \Illuminate\Support\Facades\Log::info($c->manager_id);
      // return;
      // $c->notified = true;
      // $c->save();
    }
  }

  private static function SendNotification($recipient_id, $message)
  {
    $crm_id = env('CRM_URL');
    $webhook_id = env('WEBHOOK_ID');
    $WEB_HOOK_URL = "{$crm_id}rest/{$webhook_id}/im.message.add.json?USER_ID={$recipient_id}&MESSAGE={$message}&URL_PREVIEW=Y";

    \Illuminate\Support\Facades\Http::get($WEB_HOOK_URL);
  }
}