<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use App\Http\Resources\ManagerResource;
use App\Models\Manager;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $name = Str::lower(htmlspecialchars(trim(request('name'))));
        // $id = intval(htmlspecialchars(trim(request('id'))));

        $data = Manager::get();
        // ->when(!empty($id) || !empty($name), function ($q) use ($id, $name) {
        //     $q->whereId($id)->orWhereRaw('LOWER(users.name) LIKE ?', ["%{$name}%"]);
        // })
        // ->get();
        // $all_users = \App\Traits\UserTrait::search();
        // // dd($data);
        // $mapped_users = array_map(fn($u) => $u->crm_id, $all_users);

        // foreach ($data as $key => $d) {
        //     if (in_array($d, $mapped_users)) {
        //         $keys = array_column($all_users, 'crm_id');
        //         $index = array_search($d, $keys);

        //         if ($index != false) {
        //             $all_users[$index]->is_manager = true;
        //         }
        //         // $all_users->where('crm_id', $d)->is_manager = true;
        //     } else {
        //         $user = [
        //             'crm_id' => $d,
        //             'name' => 'Noname',
        //             'is_manager' => true,
        //         ];
        //         // $d->is_manager = true;
        //         array_push($all_users, $user);
        //     }
        // }

        return response()->json([
            'status' => true,
            'data' => ManagerResource::collection($data)->response()->getData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManagerRequest $request)
    {
        $manager = Manager::create($request->validated());
        // $manager->save();

        return response()->json([
            'status' => true,
            'data' => ManagerResource::make($manager),
            'message' => 'Данный пользователь стал участником',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Manager $manager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManagerRequest $request, Manager $manager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $manager = Manager::firstWhere('crm_id', $id);

        $has_llc = \App\Models\LLC::whereManagerId($id)->exists();
        if ($has_llc) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => 'Данный пользователь уже является ответственным за ООО',
            ]);
        }

        $result = $manager->delete();

        return response()->json([
            'status' => true,
            'data' => $result,
            'message' => 'Участник успешно удалён'
        ]);
    }
}