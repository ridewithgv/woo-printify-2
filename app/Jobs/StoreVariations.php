<?php

namespace App\Jobs;

use Codexshaper\WooCommerce\Facades\Variation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StoreVariations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $variation;
    protected $product_id;
    /**
     * Create a new job instance.
     */
    public function __construct($variation, $product_id)
    {
        $this->variation = $variation;
        $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $variation = Variation::create($this->product_id, $this->variation);
        
    }
}
