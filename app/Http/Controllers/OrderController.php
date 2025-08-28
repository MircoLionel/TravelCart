<?php
namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        $order->load(['items.tour','items.tourDate','reservation']);
        return view('orders.show', compact('order'));
    }
}
