<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractTypeRequest;
use App\Http\Requests\UpdateContractTypeRequest;
use App\Http\Resources\ContractTypeResource;
use App\Models\ContractType;

class ContractTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = \Illuminate\Support\Facades\DB::table('contract_types')->get();

        return response()->json([
            'status' => true,
            'data' => ContractTypeResource::collection($data)->response()->getData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContractTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractType $contractType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractTypeRequest $request, ContractType $contractType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractType $contractType)
    {
        //
    }
}
