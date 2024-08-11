<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function getCounts(): JsonResponse
    {
        $productCount = Product::count();
        $orderCount = Order::count();
        $messageCount = Message::count();

        return response()->json([
            'products' => $productCount,
            'orders' => $orderCount,
            'messages' => $messageCount,
        ]);
    }
}
