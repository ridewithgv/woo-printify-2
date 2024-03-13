<?php

namespace App\Jobs;

use App\Http\Controllers\WooCommerceController;
use App\Http\Controllers\WooCommerceControllerNew;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StoreProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    /**
     * Create a new job instance.
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $wooCommerceController = new WooCommerceControllerNew();

        try {
            // Import the product into WooCommerce
            $wooCommerceController->importProductFromJson($this->product);
        } catch (\Exception $e) {
            Log::info("Exception".$e->getMessage());
        }
    }
}