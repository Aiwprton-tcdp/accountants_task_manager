<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LLC extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    protected $table = 'l_l_c_s';
}
