<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorBuyerLink;
use App\Notifications\VendorAccessApproved;
use Illuminate\Http\Request;

class VendorBuyerController extends Controller
{
    public function index(Request $request)
    {
        $vendor = $request->user();

        $links = VendorBuyerLink::with(['buyer'])
            ->where('vendor_id', $vendor->id)
            ->orderByDesc('created_at')
            ->get();

        return view('vendor.buyers.index', compact('links'));
    }

    public function approve(Request $request, VendorBuyerLink $link)
    {
        $vendor = $request->user();
        $this->assertVendorOwner($link, $vendor->id);

        $request->validate([
            'legajo' => 'required|string',
        ]);

        if (!$link->buyer?->legajo || $link->buyer->legajo !== $request->legajo) {
            return back()->with('error', 'El legajo no coincide con el registrado del comprador.');
        }

        $link->status = 'approved';
        $link->approved_at = now();
        $link->legajo = $request->legajo;
        $link->save();

        $link->buyer?->notify(new VendorAccessApproved($vendor));

        return back()->with('ok', 'Comprador aprobado.');
    }

    public function reject(Request $request, VendorBuyerLink $link)
    {
        $vendor = $request->user();
        $this->assertVendorOwner($link, $vendor->id);

        $link->status = 'rejected';
        $link->approved_at = null;
        $link->save();

        return back()->with('ok', 'Solicitud rechazada.');
    }

    private function assertVendorOwner(VendorBuyerLink $link, int $vendorId): void
    {
        if ($link->vendor_id !== $vendorId) {
            abort(403);
        }
    }
}
