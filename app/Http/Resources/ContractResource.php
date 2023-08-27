<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'link' => $this->link,
            'department_id' => $this->department_id,
            'contract_type_id' => $this->contract_type_id,
            'payment_period_count' => $this->payment_period_count,
            'payment_period_type' => $this->payment_period_type,
            'next_payment_date' => $this->next_payment_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'contract_type_name' => $this->contract_type_name,
            'llc_name' => @$this->llc_name,
        ];
    }
}
