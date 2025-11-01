<?php

namespace App\Http\Controllers;

use App\Models\PickupAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PickupAddressController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Auth::user()->pickupAddresses;
        return view('profile.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('profile.addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ]);

        Auth::user()->pickupAddresses()->create($request->all());

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse ajoutée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PickupAddress $pickupAddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PickupAddress $pickupAddress)
    {
        $this->authorize('update', $pickupAddress);
        return view('profile.addresses.edit', ['address' => $pickupAddress]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PickupAddress $pickupAddress)
    {
        $this->authorize('update', $pickupAddress);

        $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ]);

        $pickupAddress->update($request->all());

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse mise à jour avec succès.');
    }

}
