<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\EnvironmentSensor;
use App\Models\ProductCondition;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function create()
    {
        // Get devices only for the authenticated user
        $devices = EnvironmentSensor::where('userId', Auth::id())->pluck('deviceName', 'userId');
        // Get all categories
        $categories = Category::pluck('categoryName', 'id');
        return view('system.products.create', compact('devices', 'categories'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'productName' => 'required|string|max:255',
            'deviceId' => 'exists:environment_sensor,deviceId',
            'categoryId' => 'required|exists:product_category,id',
            'suitableTemp' => 'required|numeric',
            'suitableHumidity' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userId = Auth::id();
        $categoryId = $request->input('categoryId');

        // Create the product first to get its ID
        $product = Product::create([
            'userId' => $userId,
            'productName' => $request->input('productName'),
            'deviceId' => $request->input('deviceId'),
            'categoryId' => $categoryId,
            'suitableTemp' => $request->input('suitableTemp'),
            'suitableHumidity' => $request->input('suitableHumidity'),
            'status' => 'not check yet',
            'goodScore' => $request->input('goodScore'),
            'averageScore' => $request->input('averageScore'),
            'badScore' => $request->input('badScore'),
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            // Define the directory and new filename format
            $filePath = public_path("profile/{$userId}/product img/");
            $fileName = "product-{$userId}-{$categoryId}-{$product->id}." . $request->file('image')->getClientOriginalExtension();

            // Make sure the directory exists
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }

            // Move the uploaded file
            $request->file('image')->move($filePath, $fileName);

            // Store relative path to database
            $imagePath = "profile/{$userId}/product img/{$fileName}";

            // Update the product with the image path
            $product->update(['imagePath' => $imagePath]);
        }

        // Create a unique log file for the product
        $logFolderPath = public_path("profile/{$userId}/log/product log/");

        // Make sure the log directory exists
        if (!file_exists($logFolderPath)) {
            mkdir($logFolderPath, 0777, true);
        }

        // Define the log file path for this specific product
        $logFilePath = "{$logFolderPath}/product-{$product->id}-log.txt";

        // Create a log entry
        $logEntry = "Product Created - ID: {$product->id}, Name: {$product->productName}, Category: {$categoryId}, Timestamp: " . now() . PHP_EOL;

        // Write the log entry to the product-specific log file
        file_put_contents($logFilePath, $logEntry, FILE_APPEND);

        return redirect()->route('product-management')->with('success', 'Product added successfully.');
    }


    //Edit View
    public function edit($id)
    {
        // Get devices only for the authenticated user
        $devices = EnvironmentSensor::where('userId', Auth::id())->pluck('deviceName','deviceId');
        // Get all categories
        $categories = Category::pluck('categoryName', 'id');
        $product = Product::findOrFail($id);
        return view('system.products.edit', compact('product','devices', 'categories'));
    }

    public function update(Request $request, $id)
    {
        // Find the existing product
        $product = Product::findOrFail($id);
        $devices = EnvironmentSensor::pluck('deviceName', 'deviceId');

        Log::info('Submitted deviceId:', ['deviceId' => $request->input('deviceId')]);
        Log::info('Available devices:', ['devices' => $devices]);
        // Validate the request
        $request->validate([
            'productName' => 'required|string|max:255',
            'deviceId' => 'exists:environment_sensor,deviceId',
            'categoryId' => 'required|exists:product_category,id',
            'suitableTemp' => 'required|numeric',
            'suitableHumidity' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'goodScore' => 'nullable|integer|min:0|max:100',
            'averageScore' => 'nullable|integer|min:0|max:100',
            'badScore' => 'nullable|integer|min:0|max:100',
        ]);

        $userId = Auth::id();
        $categoryId = $request->input('categoryId');

        // Update product fields
        $product->update([
            'userId' => $userId,
            'productName' => $request->input('productName'),
            'deviceId' => $request->input('deviceId'),
            'categoryId' => $categoryId,
            'suitableTemp' => $request->input('suitableTemp'),
            'suitableHumidity' => $request->input('suitableHumidity'),
            'goodScore' => $request->input('goodScore'),
            'averageScore' => $request->input('averageScore'),
            'badScore' => $request->input('badScore'),
        ]);

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Define the directory and new filename format
            $filePath = public_path("profile/img/{$userId}/product img/");
            $fileName = "product-{$userId}-{$categoryId}-{$product->id}." . $request->file('image')->getClientOriginalExtension();

            // Ensure directory exists
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }

            // Delete the old image if it exists
            if ($product->imagePath && file_exists(public_path($product->imagePath))) {
                unlink(public_path($product->imagePath));
            }

            // Move the new uploaded file
            $request->file('image')->move($filePath, $fileName);

            // Store the new relative path to the database
            $imagePath = "profile/img/{$userId}/product img/{$fileName}";
            $product->update(['imagePath' => $imagePath]);
        }

        return redirect()->route('product-management')->with('success', 'Product updated successfully.');
    }



    //Delete product

    public function delete(Request $request)
    {
        $productIds = $request->input('selected_products');

        if ($productIds) {
            $products = Product::whereIn('id', $productIds)->get();

            foreach ($products as $product) {
                // Delete the image file if it exists
                if ($product->imagePath && file_exists(public_path($product->imagePath))) {
                    unlink(public_path($product->imagePath));
                }

                // Delete the log file if it exists
                $logFilePath = public_path("profile/{$product->userId}/log/product log/product-{$product->id}-log.txt");
                if (file_exists($logFilePath)) {
                    unlink($logFilePath);
                }

                // Delete the product record
                $product->delete();
            }

            return redirect()->route('product-management')->with('success', 'Selected products deleted successfully.');
        }

        return redirect()->route('product-management')->with('error', 'No products selected for deletion.');
    }


    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search products and paginate results
        $products = Product::with('device', 'category')
            ->where('productName', 'LIKE', '%' . $query . '%')
            ->paginate(10); // You can specify the number of products per page

        return response()->json($products);
    }



    public function getProductCondition($id)
    {
        // Fetch product condition along with the related product data
        $productCondition = ProductCondition::join('products', 'product_conditions.product_id', '=', 'products.id')
            ->where('product_conditions.product_id', $id)
            ->select('product_conditions.*', 'products.productName', 'products.categoryId', 'products.imagePath')
            ->first();

        if ($productCondition) {
            return response()->json([
                'success' => true,
                'data' => $productCondition
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product condition not found.'
            ]);
        }
    }
}
