<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'department_id',
        'contract_type_id',
        'payment_period_count',
        'payment_period_type',
        'next_payment_date',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'next_payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    protected $table = 'contracts';
}
