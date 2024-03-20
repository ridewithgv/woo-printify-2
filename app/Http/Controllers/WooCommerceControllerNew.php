<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Codexshaper\WooCommerce\Facades\Product;
use App\Http\Controllers\ProductsController;
use App\Jobs\StoreVariations;
use App\Models\Options;
use App\Models\OptionValues;
use App\Models\Products;
use App\Models\Tags;
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Variation;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class WooCommerceControllerNew extends Controller
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $storeUrl;

    public function __construct()
    {
        $this->consumerKey = env('WOO_COMMERCE_CONSUMER_KEY');
        $this->consumerSecret = env('WOO_COMMERCE_CONSUMER_SECRET');
        $this->storeUrl = env('WOO_COMMERCE_STORE_URL');
    }


    public function importProductFromJson($productData)
    {
        if (!is_array($productData)) {
            throw new \Exception("Product data must be an array");
        }

    
        $wooCommerceProduct = $this->mapProductToWooCommerce($productData);
        return $this->createProduct($wooCommerceProduct, $productData);
    }
    
    private function mapProductToWooCommerce($productData)
    {
        if (!is_array($productData) || !isset($productData['title'], $productData['description'], $productData['options'], $productData['images'], $productData['tags'])) {
            throw new \Exception("Invalid product data structure");
        }

        $attributes = $this->mapAttributesNew($productData['options']);
        $variations = $this->mapVariations($productData['variants'], $attributes);
        $stockQuantities = array_column($variations, 'stock_quantity');

        $totalStockQuantity = array_sum($stockQuantities);

        $productImport = [
            'name' => $productData['title'],
            'type' => 'variable',
            'manage_stock' => true,
            'stock_quantity' => $totalStockQuantity,
            'description' => $productData['description'],
            'sku' => $productData['id'],
            'blueprint_id' => $productData['blueprint_id'],
            'print_provider_id' => $productData['print_provider_id'],
            'user_id' => $productData['user_id'],
            'shop_id' => $productData['shop_id'],
            'visible' => $productData['visible'],
            'is_locked' => $productData['is_locked'],
            'is_printify_express_eligible' => $productData['is_printify_express_eligible'],
            'is_printify_express_enabled' => $productData['is_printify_express_enabled'],
            'images' => $this->mapImages($productData['images']),
            'tags' => array_map(function ($tag) {
                return ['name' => $tag];
            }, $productData['tags']),
            'attributes' => $attributes,
            'variations' => $variations,
            'default_attributes' => [], // Set default attributes if necessary
        ];

       $product =  Products::where('sku', $productImport['sku'])->first();
       if(!$product){
           $product =  Products::create($productImport);

       }

       if(isset($productData['options']) && is_array($productData['options'])){

           foreach($productData['options'] as $options)
            {
                $option = Options::where('name', $options['name'])->first();
                if(!$option){
                    $option = Options::create($options);
                    
                }
                $product->options()->attach($option);
                foreach($options['values'] as $values)
                {
                    $attribute = null;
                    
                    OptionValues::create([
                        'option_id' => $option['id'],
                        'attribute_id' => $values['id'],
                        'value' => $values['title'],
                        'additional_info' => $attribute,
                    ]);
                }
            }
        }
       if(isset($productData['tags']) && is_array($productData['tags'])){

            foreach($productData['tags'] as $tags) 
            {
                $tag = Tags::where('name', $tags)->first();
                if(!$tag){
                    $tag = Tags::create(['name' => $tags]);
                    
                }
                $product->tags()->attach($tag);
            }
        }
        if(isset($productData['images']) && is_array($productData['images'])){
            foreach($productData['images'] as $images)
            {
                $product->images()->create($images);
            }
        }
        return $productImport;
    }


    private function mapAttributesNew($options)
    {
        $attributes = [];
        $stored_attributes = Attribute::all();
        foreach ($options as $option) {
            # code...
            $existingAttribute = $stored_attributes->where('name', $option['name'])->first();
            if(!$existingAttribute){
                $newAttribute = Attribute::create([
                    'name' => $option['name'],
                    'type' => 'select',
                    'has_archives' => true,
                ]);
                // Add the newly created attribute to the stored attributes collection
                $stored_attributes->push([
                    'name' => $option['name'],
                    'type' => 'select',
                    'has_archives' => true,
                ]);
                $existingAttribute = $newAttribute;
            }

            foreach ($stored_attributes as $value) {
                if($value->name == $option['name']){
                    $attribute = [
                        'id' => $value->id,
                        'position' => 0,
                        'visible' => false,
                        'variation' => true,
                        'options' => array_map(function ($value) {
                            return $value['title'];
                        }, $option['values'])
                    ];

                }
            }
            $attributes[] = $attribute;

        }
        return $attributes;
    }

    private function mapVariations($variants, $attributes)
    {
        $mappedVariants = [];
        foreach ($variants as $variant) {
            if($variant['is_enabled']){
                $mappedVariant = [
                    'sku' => $variant['sku'],
                    'regular_price' => (string)($variant['price'] / 100), // Assuming price is in cents
                    'manage_stock' => true,
                    'stock_quantity' => $variant['quantity'],
                    'attributes' => $this->getVariantAttributesNew($variant, $attributes),
                    // Add other variant details as needed
                ];
                $mappedVariants[] = $mappedVariant;

            }
        }
        return $mappedVariants;
    }

    
    private function getVariantAttributesNew($variant, $attributes)
    {
        $components = explode(' / ', $variant['title']);
        $variantAttributes = [];
    
        // Assuming $variant['options'] is an array of option IDs
        if (!isset($components) || !is_array($components)) {
            throw new \Exception("Invalid variant options format");
        }
    
        foreach ($components as $variant_title) {
            // dd($variant_title);
            foreach ($attributes as $attribute) {
                if ( !isset($attribute['options'])) {
                    // Skip if the attribute format is not correct
                    continue;
                }
    
                foreach ($attribute['options'] as $option) {
                    // dd($option);
                    if ($variant_title == $option) {
                        $variantAttributes[] = [
                            'id' => $attribute['id'],
                            'option' => $option // Assuming 'title' is the correct key
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

    public function createProduct($productData, $productDataOfPrintifyAPI)
    {
        

        try {
            $product = Product::create($productData);

            // Check if product is variable and has variations
            Log::info("product created");
            if ($productData['type'] === 'variable' && !empty($productData['variations'])) {
                
                $jobs = [];
                foreach ($productData['variations'] as $variationData) {
                    $img = $this->getVariantImage($product, $productDataOfPrintifyAPI);

                    $variationData['image'] = $img;
                    
                    $jobs[] = new StoreVariations($variationData,  $product['id']);
                }

                $batch = Bus::batch($jobs)
                ->then(function (Batch $batch) {
                    // All jobs completed successfully...
                    Log::info('All Store Variations jobs completed successfully for batch ID: ' . $batch->id);
                })->catch(function (Batch $batch, Throwable $e) {
                    // First batch job failure detected...
                    Log::info('Exception: ' . $e->getMessage());
                })->finally(function (Batch $batch) {
                    // The batch has finished executing...
                    Log::info('The Store Variations batch has finished executing...');
                })->name('Import Variations')
                    ->onConnection('redis')
                    ->dispatch();
            
            }

        } catch (\Exception $e) {
            Log::error('Failed to create product in WooCommerce', [
                'exception_message' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    private function getVariantImage($product, $productDataOfPrintifyAPI)
    {
        $imageid = [];
        foreach($productDataOfPrintifyAPI['variants'] as $variant){
            if($variant['is_enabled']){
                foreach($productDataOfPrintifyAPI['images'] as $image){
                    if(in_array($variant['id'], $image['variant_ids'])){
                        $filename = basename(parse_url($image['src'], PHP_URL_PATH));
                        
                        foreach ($product['images'] as  $value) {
                            if($value->name == $filename){
                                $imageid['id'] = $value->id;
                            }
                        }
                    }


                }
            }
        }
        Log::info('image-id----'. json_encode($imageid));
        return $imageid;
    }

}
