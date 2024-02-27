<?php

namespace App\Http\Controllers;

use Codexshaper\WooCommerce\Facades\Product;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Options;
use App\Models\Tags;
use App\Models\OptionValues;

class ProductsController extends Controller
{

    public function store(Products $products, Request $request) {

        return $products::create($request->all());
    }

    public function createTestProduct(Products $products)
    {
        // $data = [
        //     'name' => 'Test Product',
        //     'type' => 'simple',
        //     'regular_price' => '19.99',
        //     'description' => 'This is a test product description.',
        //     'short_description' => 'Short description of the test product.',
        //     'categories' => [
        //         ['id' => 9], // Assuming category ID 9 exists in WooCommerce
        //     ],
        //     'images' => [
        //         ['src' => 'http://example.com/image.jpg']
        //     ]
        // ];

        $data = [
            "sku" => "65bc350096c05a818e02b3df",
            "name" => "Animal Costumes Collection AC0104 Unisex Softstyle T-Shirt",
            "description" => "The unisex soft-style t-shirt puts a new spin on casual comfort...",
            "type" => 'variant',
            // Shortened description for brevity
            "tags" => ["Men's Clothing", "T-shirts", "DTG"],
            "options" => [
                [
                    "name" => "Colors",
                    "type" => "color",
                    "values" => [
                        ["id" => 521, "title" => "White", "colors" => ["#ffffff"]],
                        ["id" => 418, "title" => "Black", "colors" => ["#000000"]],
                        // One more color option omitted for simplicity
                    ],
                ],
                [
                    "name" => "Sizes",
                    "type" => "size",
                    "values" => [
                        ["id" => 14, "title" => "S"],
                        ["id" => 15, "title" => "M"],
                        // One more size option omitted for simplicity
                    ],
                ],
                [
                    "name" => "Stones",
                    "type" => "stone",
                    "values" => [
                        ["id" => 14, "title" => "S", "type" => ["Ruby"]],
                        ["id" => 15, "title" => "M", "type" => ["Jade"]],
                        // One more stone option omitted for simplicity
                    ],
                ],
            ],
            "images" => [
                [
                    "src" => "https://images-api.printify.com/mockup/65bc350096c05a818e02b3df/38191/97992/animal-costumes-collection-ac0104-unisex-softstyle-t-shirt.jpg?camera_label=front",
                    "variant_ids" => [38191, 38163, 38177],
                    "position" => "front",
                    "is_default" => true,
                    "is_selected_for_publishing" => true,
                ],
                // Two more images omitted for simplicity
            ],
            "created_at" => "2024-02-02 00:19:12+00:00",
            "updated_at" => "2024-02-02 00:21:14+00:00",
            "visible" => true,
            "is_locked" => false,
            "blueprint_id" => 145,
            "user_id" => 14366360,
            "shop_id" => 12901793,
            "print_provider_id" => 29,
            "print_areas" => [
                [
                    "variant_ids" => [38153, 38155, 38156],
                    // Omitting detailed placeholders for brevity
                ],
                // Two more print areas omitted for simplicity
            ], 
            "sales_channel_properties" => [],
            "is_printify_express_eligible" => false,
            "is_printify_express_enabled" => false,
        ];


        $product = $products::create($data);

        foreach($data['options'] as $options)
        {
            $option = Options::create($options);
            $product->options()->attach($option);
            foreach($options['values'] as $values)
            {
                $attribute = null;
                foreach($values as $key => $val) {
                    if(is_array($val))
                    {
                        $attribute = [$key => $val];
                    }
                }
                OptionValues::create([
                    'option_id' => $option['id'],
                    'attribute_id' => $values['id'],
                    'value' => $values['title'],
                    'additional_info' => $attribute,
                ]);
            }
        }

        foreach($data['tags'] as $tags) 
        {
            $tag = Tags::create(['name' => $tags]);
            $product->tags()->attach($tag);
        }

        foreach($data['images'] as $images)
        {
            $product->images()->create($images);
        }

        ds($product);
        return response()->json($product);

        // try {
        //     $product = Product::create($data);
        //     return response()->json($product, 201);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
    }
}
