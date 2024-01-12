<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooCommerceController extends Controller
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $storeUrl;

    public function __construct()
    {
        $this->consumerKey = env('WOOCOMMERCE_CONSUMER_KEY');
        $this->consumerSecret = env('WOOCOMMERCE_CONSUMER_SECRET');
        $this->storeUrl = env('WOOCOMMERCE_STORE_URL');
    }

    protected function woocommerceClient()
    {
        return Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                   ->baseUrl($this->storeUrl)
                   ->asJson();
    }


    public function importProductFromJson($productJson)
    {
        $productData = json_decode($productJson, true);
        $wooCommerceProduct = $this->mapProductToWooCommerce($productData);
        return $this->createProduct($wooCommerceProduct);
    }

    private function mapProductToWooCommerce($productData)
    {
        // No need to decode JSON, as $productData is already an array
        // Ensure $productData is an array and has the expected structure
        if (!is_array($productData) || !isset($productData['title'], $productData['description'], $productData['options'], $productData['images'], $productData['tags'])) {
            throw new \Exception("Invalid product data structure");
        }

        $attributes = $this->mapAttributes($productData['options']);
        $variations = $this->mapVariations($productData['variants'], $attributes);

        return [
            'name' => $productData['title'],
            'type' => 'variable',
            'description' => $productData['description'],
            'images' => $this->mapImages($productData['images']),
            'tags' => array_map(function ($tag) {
                return ['name' => $tag];
            }, $productData['tags']),
            'attributes' => $attributes,
            'default_attributes' => [], // Set default attributes if necessary
        ];
    }


    private function mapAttributes($options)
    {
        $attributes = [];
        foreach ($options as $option) {
            $attribute = [
                'name' => $option['name'],
                'options' => array_map(function ($value) {
                    return $value['title'];
                }, $option['values'])
            ];
            $attributes[] = $attribute;
        }
        return $attributes;
    }

    private function mapVariations($variants, $attributes)
    {
        $mappedVariants = [];
        foreach ($variants as $variant) {
            $mappedVariant = [
                'sku' => $variant['sku'],
                'regular_price' => (string)($variant['price'] / 100), // Assuming price is in cents
                'attributes' => $this->getVariantAttributes($variant, $attributes),
                // Add other variant details as needed
            ];
            $mappedVariants[] = $mappedVariant;
        }
        return $mappedVariants;
    }

    private function getVariantAttributes($variant, $attributes)
    {
        $variantAttributes = [];
        foreach ($variant['options'] as $optionId) {
            foreach ($attributes as $attribute) {
                foreach ($attribute['options'] as $option) {
                    if ($option['id'] == $optionId) {
                        $variantAttributes[] = [
                            'name' => $attribute['name'],
                            'option' => $option['title']
                        ];
                    }
                }
            }
        }
        return $variantAttributes;
    }

    private function mapImages($images)
    {
        return array_map(function ($image) {
            return ['src' => $image['src']];
        }, $images);
    }

    public function createProduct($productData)
    {
        $response = $this->woocommerceClient()->post('/wp-json/wc/v3/products', $productData);
    
        if ($response->successful()) {
            
            return $response->json();
        } else {
            Log::error('Failed to create product in WooCommerce', [
                'response_status' => $response->status(),
                'response_body' => $response->body(),
                'sent_product_data' => $productData
            ]);
            throw new \Exception('Failed to create product: ' . $response->body());
        }
    }
    

    // Other methods...
}
