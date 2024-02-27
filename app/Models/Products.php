<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'type',
        'description',
        'sku',
        'blueprint_id',
        'print_provider_id',
        'user_id',
        'shop_id',
        'visible',
        'is_locked',
        'is_printify_express_eligible',
        'is_printify_express_enabled',
    ];

    public function options() 
    {
        return $this->belongsToMany(Options::class, 'product_options');
    }

    public function tags()
    {
        return $this->belongsToMany(Tags::class, 'product_tags');
    }

    public function images()
    {
        return $this->hasMany(Images::class);
    }
}
