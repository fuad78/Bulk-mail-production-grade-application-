<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sender extends Model
{
    protected $fillable = ['name', 'email', 'type', 'configuration', 'is_active'];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
    ];
}
