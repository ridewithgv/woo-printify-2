<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    protected $fillable = [
        'products_id',
        'src',
        'position',
        'is_default',
        'is_selected_for_publishing',
    ];
}
