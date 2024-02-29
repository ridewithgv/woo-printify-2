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

    protected $products;
    /**
     * Create a new job instance.
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $wooCommerceController = new WooCommerceControllerNew();
        foreach ($this->products as $index => $product) {
            // Convert the product to a JSON string


            Log::info("products----------".json_encode($product));

            try {
                // Import the product into WooCommerce
                $wooCommerceController->importProductFromJson($product);
            } catch (\Exception $e) {
                Log::info("Exception".$e->getMessage());
                continue;
            }
        }
    }
}
