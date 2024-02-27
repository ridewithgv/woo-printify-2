<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OptionValues;

class Options extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    public function attributes()
    {
        $this->belongsToMany(OptionValues::class);
    }
}
