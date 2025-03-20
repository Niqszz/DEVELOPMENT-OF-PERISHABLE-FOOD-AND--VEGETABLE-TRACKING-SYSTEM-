<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\SpoiledgeReading;
use Illuminate\Http\Request;

class pageNavigator extends Controller
{

    public function dashboard(){
        return view("system.dashboard");
    }

    public function profileManagement(){
        $products = Product::with('device', 'category')->paginate(10); // Fetch all products initially
        return view("system.profile-management");
    }
    public function environmentMonitoring(){

        return view("system.environment-monitoring");
    }
    public function spoiledgeDetector(){
        $connectedDevice = SpoiledgeReading::where('userId', Auth::id())->first();

        return view("system.spoiledge-detector", [
            'connectedDevice' => $connectedDevice,
        ]);
    }
    public function productManagement(){
        $userId = Auth::user()->id; // Dapatkan ID pengguna yang sedang log masuk
        $products = Product::where('userId', $userId)->with(['device', 'category'])->get(); // Filter berdasarkan userId dan ambil data yang berkaitan dengan device dan category
        return view("system.product-management",compact('products'));
    }
    public function notification(){

        return view("system.notification");
    }
    public function report(){

        return view("system.report");
    }

}
