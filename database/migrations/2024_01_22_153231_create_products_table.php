<?php

use App\Http\Controllers\ImagesController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\TagsController;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->longText('description');
            $table->string('sku');
            // $table->string('sku')->unique();
            $table->integer('blueprint_id');
            $table->integer('print_provider_id');
            $table->integer('user_id');
            $table->integer('shop_id');
            $table->boolean('visible');
            $table->boolean('is_locked');
            $table->boolean('is_printify_express_eligible');
            $table->boolean('is_printify_express_enabled');
            $table->integer('default_variant')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
