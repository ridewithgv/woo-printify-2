<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValues extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_id',
        'attribute_id',
        'value',
        'additional_info'
    ];

    protected $casts = [
        'additional_info' => 'array',
    ];
}
