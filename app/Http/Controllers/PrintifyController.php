<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrintifyController extends Controller
{
    protected $printifyApiKey;

    public function __construct()
    {
        $this->printifyApiKey = env('PRINTIFY_API_KEY');
    }

    protected function printifyClient()
    {
        return Http::baseUrl('https://api.printify.com/v1/')
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
        ])->get("https://api.printify.com/v1/shops/{$shopId}/products.json");

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to retrieve products'], $response->status());
        }

        $products = $response->json();
        $wooCommerceController = new WooCommerceController();
        foreach ($products['data'] as $product) {
            // Convert the product to a JSON string
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

    
}
