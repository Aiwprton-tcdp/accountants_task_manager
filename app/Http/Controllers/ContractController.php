<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Action;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Manager;
use App\Traits\FileTrait;
use Illuminate\Support\Carbon;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = intval(htmlspecialchars(trim(request('dep'))));
        $user_id = intval(htmlspecialchars(trim(request('user_id'))));
        $limit = intval(htmlspecialchars(trim(request('limit'))));

        // dd($user_id, Manager::all());
        $data = \Illuminate\Support\Facades\DB::table('contracts')
            ->join('contract_types AS ct', 'ct.id', 'contracts.contract_type_id')
            ->join('departments AS d', 'd.id', 'contracts.department_id')
            ->join('l_l_c_s AS l', 'l.id', 'd.l_l_c_s_id')
            ->when($id != 0, fn($q) => $q->where('department_id', $id))
            ->when($user_id != 0, fn($q) => $q->where('l.manager_id', $user_id))
            ->select('contracts.*', 'l.name AS llc_name', 'ct.value AS contract_type_name')
            ->orderBy('contracts.next_payment_date')
            ->paginate($limit < 1 ? 100 : $limit);

        return response()->json([
            'status' => true,
            'data' => ContractResource::collection($data)->response()->getData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContractRequest $request)
    {
        $validated = $request->validated();
        $contract = Contract::firstOrNew([
            'name' => $validated['file']->getClientOriginalName(),
            'department_id' => $validated['department_id'],
        ]);
        if ($contract->exists) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => 'В данном отделе договор с таким названием уже существует',
            ]);
        }

        $dep = \App\Models\Department::findOrFail($validated['department_id']);
        $ct = ContractType::findOrFail($validated['contract_type_id']);

        $dwnld_result = FileTrait::addFileToCrm($dep->crm_folder_id, $contract->name, $validated['file']);
        if ($dwnld_result['status'] == false) {
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $dwnld_result['data'],
            ]);
        }

        $date = Carbon::createFromFormat('d.m.Y', $validated['next_payment_date'])
            ->format('Y-m-d');
        $contract->fill($validated);
        $contract->next_payment_date = $date;
        $contract->link = $dwnld_result['data'];

        $contract->save();
        $contract->contract_type_name = $ct->value;

        Action::create([
            'content' => "Добавлен {$ct->value}",
            'department_id' => $validated['department_id'],
        ]);
        return response()->json([
            'status' => true,
            'data' => ContractResource::make($contract),
            'message' => "Договор {$contract->name} добавлен",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractRequest $request, $id)
    {
        $validated = $request->validated();
        $contract = Contract::findOrFail($id);

        $date = Carbon::parse($contract->next_payment_date);

        $c = $contract->payment_period_count;
        switch ($contract->payment_period_type) {
            case 'd': $res_date = $date->addDays($c); break;
            case 'w': $res_date = $date->addWeeks($c); break;
            case 'm': $res_date = $date->addMonths($c); break;
            case 'q': $res_date = $date->addQuarters($c); break;
            case 'y': $res_date = $date->addYears($c); break;
            default: $res_date = $date; break;
        }

        $contract->next_payment_date = $res_date;
        $contract->notified = false;
        $contract->save();

        $ct = ContractType::findOrFail($contract->contract_type_id);
        Action::create([
            'content' => "Была произведена оплата по {$contract->name} ({$ct->value})",
            'department_id' => $contract->department_id,
        ]);
        return response()->json([
            'status' => true,
            'data' => ContractResource::make($contract),
            'message' => "Информация об оплате зафиксирована",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $department_id = $contract->department_id;
        $result = $contract->delete();

        $ct = ContractType::findOrFail($contract->contract_type_id);
        Action::create([
            'content' => "{$ct->value} удалён",
            'department_id' => $department_id,
        ]);

        return response()->json([
            'status' => true,
            'data' => $result,
            'message' => 'Договор успешно удалён'
        ]);
    }
}