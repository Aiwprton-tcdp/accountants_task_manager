<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = \Illuminate\Support\Facades\DB::table('departments')
            ->rightJoin('l_l_c_s', 'l_l_c_s.id', 'departments.l_l_c_s_id')
            ->select('departments.*', 'l_l_c_s.name AS llc_name', 'l_l_c_s.id AS l_l_c_s_id')
            ->orderBy('llc_name')
            ->orderBy('departments.name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => DepartmentResource::collection($data)->response()->getData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();
        $department = Department::firstOrNew(['name' => $validated['name']]);
        if ($department->exists) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => 'Отдел с таким названием уже существует',
            ]);
        }
        $department->l_l_c_s_id = $validated['l_l_c_s_id'];

        $llc = \App\Models\LLC::find($department->l_l_c_s_id);
        $llc_name = @$llc->name;
        $response = \App\Traits\BX::call('disk.folder.addsubfolder', [
            'id' => env('CRM_MAIN_FOLDER_ID'),
            'data' => [
                'NAME' => "{$department->name} [{$llc_name}]"
            ],
        ]);

        if (isset($response['error_description'])) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $response['error_description'],
            ]);
        }

        $department->crm_folder_id = @$response['result']['ID'];
        $department->save();

        return response()->json([
            'status' => true,
            'data' => DepartmentResource::make($department),
            'message' => 'Отдел добавлен',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Department::rightJoin('l_l_c_s', 'l_l_c_s.id', 'departments.l_l_c_s_id')
            ->select('departments.*', 'l_l_c_s.name AS llc_name', 'l_l_c_s.id AS l_l_c_s_id')
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => DepartmentResource::make($data)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        //
    }
}