<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Traits\BX;
use Illuminate\Support\Facades\DB;

class IndexAPIController extends Controller
{
  public function __invoke()
  {
    try {
      // $ticket_id = isset($_REQUEST['PLACEMENT_OPTIONS']) && isset(json_decode($_REQUEST['PLACEMENT_OPTIONS'])->id)
      //   ? intval(json_decode($_REQUEST['PLACEMENT_OPTIONS'])->id)
      //   : 0;
      // $token = '';
      $check = BX::setDataE($_REQUEST);
      $data = BX::call('user.current')['result'];
      $user = [
        'crm_id' => $data['ID'] ?? 0,
        'name' => trim($data['LAST_NAME'] . " " . $data['NAME'] . " " . $data['SECOND_NAME']) ?? 'Неопределённый пользователь',
        'avatar' => $data['PERSONAL_PHOTO'] ?? null,
        'post' => trim($data['WORK_POSITION'] ?? null),
        'departments' => $data['UF_DEPARTMENT'] ?? 0,
        'inner_phone' => $data['UF_PHONE_INNER'] ?? 0,
      ];

      $user['is_admin'] = BX::call('user.admin')['result'];
      $is_manager = \App\Models\Manager::where('crm_id', $user['crm_id'])->exists();
      $user['is_manager'] = $is_manager;

      // dd($user, $manager);
      return view('welcome', compact('user'));
    } catch (\Exception $er) {
      return $er->getMessage();
    }
  }
}