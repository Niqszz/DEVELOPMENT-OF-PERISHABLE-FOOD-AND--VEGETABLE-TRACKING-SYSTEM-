<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCondition;
use App\Models\Product;

class ProductConditionController extends Controller
{
    public function storeOrUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'methane_average_ppm' => 'required|numeric',
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'cummulativeDurationHum' => 'required|numeric',
            'cummulativeDurationTemp' => 'required|numeric',
            'status' => 'required|string',
        ]);

        // Check if a product condition already exists for the given product_id
        $productCondition = ProductCondition::firstOrNew(['product_id' => $validatedData['product_id']]);
        $product = Product::where('id',$validatedData['product_id'])->first();

        // Update or create with the provided data
        $productCondition->averageMethaneReading = $validatedData['methane_average_ppm'];
        $productCondition->temperature = $validatedData['temperature'];
        $productCondition->humidity = $validatedData['humidity'];
        $productCondition->cumulative_duration_humidity = $validatedData['cummulativeDurationHum'];
        $productCondition->cumulative_duration_temperature = $validatedData['cummulativeDurationTemp'];
        $productCondition->status = $validatedData['status'];
        $product->status = $validatedData['status'];

        // Save the product condition to the database
        $productCondition->save();
        $product->save();

        return response()->json([
            'message' => 'Product condition saved successfully!',
            'data' => $product,
        ], 200);
    }
}
