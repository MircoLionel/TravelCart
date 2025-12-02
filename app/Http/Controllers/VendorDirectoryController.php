<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorBuyerLink;
use App\Notifications\VendorAccessRequested;
use Illuminate\Http\Request;

class VendorDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeBuyer($request->user());

        $vendors = User::query()
            ->where('role', 'vendor')
            ->orderBy('name')
            ->get();

        $links = $request->user()->buyerLinks->keyBy('vendor_id');

        return view('vendors.index', compact('vendors', 'links'));
    }

    public function requestAccess(Request $request, User $vendor)
    {
        $buyer = $request->user();
        $this->authorizeBuyer($buyer);

        if (!$vendor->isVendor()) {
            abort(404);
        }

        $data = $request->validate([
            'legajo' => 'required|string|max:50',
        ]);

        $link = VendorBuyerLink::updateOrCreate(
            ['vendor_id' => $vendor->id, 'buyer_id' => $buyer->id],
            [
                'legajo'  => $data['legajo'] ?? $buyer->legajo,
                'status'  => 'pending',
                'approved_at' => null,
            ]
        );

        $vendor->notify(new VendorAccessRequested($buyer));

        return back()->with('ok', 'Solicitud enviada al proveedor.');
    }

    private function authorizeBuyer(User $user): void
    {
        if (!$user->isBuyer() && !$user->isAdmin()) {
            abort(403, 'Solo compradores pueden solicitar acceso.');
        }
    }
}
