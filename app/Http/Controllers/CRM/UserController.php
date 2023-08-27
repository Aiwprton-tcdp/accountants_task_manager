<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Traits\BX;
use App\Traits\UserTrait;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search()
    {
        // $name = Str::lower(htmlspecialchars(trim(request('name'))));
        // $id = intval(htmlspecialchars(trim(request('id'))));

        $data = \App\Models\Manager::pluck('crm_id'); //->get();
        // ->when(!empty($id) || !empty($name), function ($q) use ($id, $name) {
        //     $q->whereId($id)->orWhereRaw('LOWER(users.name) LIKE ?', ["%{$name}%"]);
        // })
        // ->get();
        $all_users = UserTrait::search();
        // dd($data);
        $mapped_users = array_map(fn($u) => $u->crm_id, $all_users);

        foreach ($data as $key => $d) {
            if (in_array($d, $mapped_users)) {
                $keys = array_column($all_users, 'crm_id');
                $index = array_search($d, $keys);

                if ($index != false) {
                    $all_users[$index]->is_manager = true;
                }
                // $all_users->where('crm_id', $d)->is_manager = true;
            } else {
                $user = [
                    'crm_id' => $d,
                    'name' => 'Noname',
                    'is_manager' => true,
                ];
                // $d->is_manager = true;
                array_push($all_users, $user);
            }
        }

        return response()->json([
            'status' => true,
            'data' => UserResource::collection($all_users)->response()->getData()
        ]);
        // return response()->json([
        //   'status' => true,
        //   'data' => UserTrait::search()
        // ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getParentFolderId()
    {
        $response = BX::call('disk.folder.get', [
            'id' => 10750614,
        ]);

        return response()->json([
            'status' => true,
            'data' => @$response['result']['PARENT_ID']
        ]);
    }
}