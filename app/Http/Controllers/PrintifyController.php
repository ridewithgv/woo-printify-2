<?php

namespace App\Http\Controllers;

use App\Jobs\StoreProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Config;
use Illuminate\Support\Facades\Log;

class PrintifyController extends Controller
{
    protected $printifyApiKey;

    public function __construct()
    {
        $this->printifyApiKey = config('printify.printify_token');
    }

    protected function printifyClient()
    {
        return Http::baseUrl('https://api.printify.com/v1')
                   ->withHeaders([
                       'Authorization' => 'Bearer ' . $this->printifyApiKey,
                   ]);
    }


    public function getShopId()
    {
        $response = $this->printifyClient()->get('shops.json');
    
        if ($response->successful()) {
            $shops = $response->json();
            return $shops[0]['id'] ?? null;
        }
    
        return null;
    }
    
    public function importProducts()
    {
        $shopId = $this->getShopId(); // Assuming you have a method to get the shop ID
        if (!$shopId) {
            return response()->json(['error' => 'No shop ID found'], 404);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->printifyApiKey,
        ])->get("https://api.printify.com/v1/shops/{$shopId}/products.json?limit=2");

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to retrieve products'], $response->status());
        }

        $products = $response->json();
        $wooCommerceController = new WooCommerceController();
        foreach ($products['data'] as $index => $product) {
            // Convert the product to a JSON string

            ds($product);

            try {
                // Import the product into WooCommerce
                $wooCommerceController->importProductFromJson($product);
            } catch (\Exception $e) {
                // Handle exceptions for each product import
                // Log the error or take other appropriate actions
                // Continuing the loop to attempt to import other products
                continue;
            }
        }

        return response()->json(['success' => 'Products imported successfully.']);
    }

    public function newImportProducts()
    {

       
        $shopId = $this->getShopId(); 
        if (!$shopId) {
            $this->error('No shop ID found');
            return;
        }

        $limit = 20; 
       
        $currentPage = cache()->get('printify_current_page', 1);
       
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->printifyApiKey,
        ])->get("https://api.printify.com/v1/shops/{$shopId}/products.json?limit={$limit}&page={$currentPage}");
  
        if (!$response->successful()) {
            Log::error("https://api.printify.com/v1/shops/{$shopId}/products.json?limit={$limit}&page={$currentPage}");
            return response()->json(['error' => 'Failed to retrieve products'], $response->status());
        
        }

        $products = $response->json();
 
        dispatch(new StoreProducts($products["data"]));
        Log::info("https://api.printify.com/v1/shops/{$shopId}/products.json?limit={$limit}&page={$currentPage}");
    
        $currentPage++;

        if ($currentPage > $response->json()['last_page']) {
            $currentPage = 1;
        }

        cache()->put('printify_current_page', $currentPage, 60 * 24); 

        return response()->json(['success' => 'Products imported successfully.']);
    }
}
