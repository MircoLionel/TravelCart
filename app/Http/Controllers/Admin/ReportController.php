<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function ordersCsv(): StreamedResponse
    {
        $fileName = 'orders_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () {
            $out = fopen('php://output', 'w');

            // BOM UTF-8
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($out, ['ID','Código','Usuario','Email','Total','Descuento','Estado','Creado']);

            Order::with('user')->orderByDesc('id')->chunk(500, function($chunk) use ($out) {
                foreach ($chunk as $o) {
                    fputcsv($out, [
                        $o->id,
                        $o->code ?? '',
                        $o->user?->name ?? '',
                        $o->user?->email ?? '',
                        $o->total,
                        $o->discount_total ?? 0,
                        $o->status ?? '',
                        optional($o->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($out);
        }, 200, $headers);
    }
}
