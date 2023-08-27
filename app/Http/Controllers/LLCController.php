<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLLCRequest;
use App\Http\Requests\UpdateLLCRequest;
use App\Http\Resources\LLC_Resource;
use App\Models\LLC;
use App\Models\Manager;

class LLCController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $name = \Illuminate\Support\Str::lower(htmlspecialchars(trim(request('name'))));
        $id = intval(htmlspecialchars(trim(request('id'))));

        $data = \Illuminate\Support\Facades\DB::table('l_l_c_s')
            ->join('managers', 'managers.crm_id', 'l_l_c_s.manager_id')
            ->when(
                !empty($id) || !empty($name),
                fn($q) => $q
                    ->whereId($id)->orWhereRaw('LOWER(managers.name) LIKE ?', ["%{$name}%"])
            )
            ->select('l_l_c_s.*', 'managers.name AS user_name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => LLC_Resource::collection($data)->response()->getData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLLCRequest $request)
    {
        $validated = $request->validated();
        $llc = LLC::firstOrNew(['name' => $validated['name']]);
        if ($llc->exists) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => 'Организация с таким названием уже существует',
            ]);
        }
        $llc['manager_id'] = $validated['manager_id'];
        $llc->save();

        $llc['user_name'] = Manager::firstWhere('crm_id', $llc['manager_id'])->name;

        return response()->json([
            'status' => true,
            'data' => LLC_Resource::make($llc),
            'message' => 'Организация добавлена',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(LLC $lLC)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLLCRequest $request, $id)
    {
        $llc = LLC::findOrFail($id);
        $name = $llc->name;
        $llc->fill($request->validated());
        $llc->save();

        $llc->user_name = Manager::firstWhere('crm_id', $llc->manager_id)->name;
        $message = $name != $llc->name
            ? "Название организации `{$name}` изменено на `{$llc->name}`"
            : 'Ответственный за организацию сменился';

        return response()->json([
            'status' => true,
            'data' => LLC_Resource::make($llc),
            'message' => $message
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LLC $lLC)
    {
        //
    }
}