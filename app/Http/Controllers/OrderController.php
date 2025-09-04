<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Historial de órdenes del usuario autenticado.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['items.tour', 'items.tourDate'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(12);

        return view('orders.index', compact('orders'));
    }

    /**
     * Detalle de una orden.
     */
    public function show(Order $order)
    {
        $this->authorizeView($order);

        $order->loadMissing(['items.tour', 'items.tourDate', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Voucher imprimible (HTML). Si ?pdf=1 y tenés mpdf instalado,
     * lo renderiza como PDF inline.
     *
     * GET /orders/{order}/voucher
     * GET /orders/{order}/voucher?pdf=1
     */
    public function voucher(Request $request, Order $order)
    {
        $this->authorizeView($order);

        $order->loadMissing(['items.tour', 'items.tourDate', 'user']);

        // ¿Quieren PDF y existe la librería mpdf?
        if ($request->boolean('pdf') && class_exists(\Mpdf\Mpdf::class)) {
            $html = view('orders.voucher', compact('order'))->render();

            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);

            // inline (en el navegador); podés cambiar a 'D' para forzar descarga
            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="voucher-'.$order->id.'.pdf"',
            ]);
        }

        // Fallback: HTML imprimible
        return view('orders.voucher', compact('order'));
    }
  
    /**
     * Autoriza acceso a la orden: dueño o admin.
     * (Si ya tenés Policy, podés reemplazar por $this->authorize('view', $order);)
     */
    protected function authorizeView(Order $order): void
    {
        $user = auth()->user();
        $isAdmin = $user?->can('admin'); // usa tu Gate 'admin'

        if ($order->user_id !== $user?->id && !$isAdmin) {
            abort(403);
        }
    }
}
