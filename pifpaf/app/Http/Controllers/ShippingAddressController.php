<?php

namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ShippingAddressController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('profile.shipping_addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
        ]);

        Auth::user()->shippingAddresses()->create($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse de livraison ajoutée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingAddress $shippingAddress)
    {
        $this->authorize('update', $shippingAddress);
        return view('profile.shipping_addresses.edit', compact('shippingAddress'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingAddress $shippingAddress)
    {
        $this->authorize('update', $shippingAddress);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
        ]);

        $shippingAddress->update($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse de livraison mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingAddress $shippingAddress)
    {
        $this->authorize('delete', $shippingAddress);
        $shippingAddress->delete();

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse de livraison supprimée avec succès.');
    }
}
