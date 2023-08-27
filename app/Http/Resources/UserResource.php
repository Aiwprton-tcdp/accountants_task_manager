<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            'crm_id' => $this->crm_id,
            'name' => $this->name,
            'post' => $this->post ?? null,
            'is_manager' => $this->is_manager ?? false,
            // 'created_at' => $this->created_at,
        ];
    }
}
