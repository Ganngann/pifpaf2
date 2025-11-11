<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AddressController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->get();

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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
        ]);

        $isForPickup = $request->boolean('is_for_pickup');
        $isForDelivery = $request->boolean('is_for_delivery');

        if (!$isForPickup && !$isForDelivery) {
            return back()->withErrors(['type' => 'Vous devez sélectionner au moins un type d\'adresse (retrait ou livraison).'])->withInput();
        }

        $validatedData['is_for_pickup'] = $isForPickup;
        $validatedData['is_for_delivery'] = $isForDelivery;

        if ($isForPickup) {
            $addressString = "{$validatedData['street']}, {$validatedData['postal_code']} {$validatedData['city']}, Belgium";
            $response = Http::get('https://geocode.maps.co/search', ['q' => $addressString]);

            if ($response->successful() && count($response->json()) > 0) {
                $geocodedData = $response->json()[0];
                $validatedData['latitude'] = $geocodedData['lat'];
                $validatedData['longitude'] = $geocodedData['lon'];
            }
        }

        Auth::user()->addresses()->create($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse ajoutée avec succès.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        $this->authorize('update', $address);
        return view('profile.addresses.edit', ['address' => $address]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
        ]);

        $isForPickup = $request->boolean('is_for_pickup');
        $isForDelivery = $request->boolean('is_for_delivery');

        if (!$isForPickup && !$isForDelivery) {
            return back()->withErrors(['type' => 'Vous devez sélectionner au moins un type d\'adresse (retrait ou livraison).'])->withInput();
        }

        $validatedData['is_for_pickup'] = $isForPickup;
        $validatedData['is_for_delivery'] = $isForDelivery;

        if ($isForPickup) {
            $addressString = "{$validatedData['street']}, {$validatedData['postal_code']} {$validatedData['city']}, Belgium";
            $response = Http::get('https://geocode.maps.co/search', ['q' => $addressString]);

            if ($response->successful() && count($response->json()) > 0) {
                $geocodedData = $response->json()[0];
                $validatedData['latitude'] = $geocodedData['lat'];
                $validatedData['longitude'] = $geocodedData['lon'];
            } else {
                $validatedData['latitude'] = $address->latitude;
                $validatedData['longitude'] = $address->longitude;
            }
        } else {
            $validatedData['latitude'] = null;
            $validatedData['longitude'] = null;
        }

        $address->update($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse supprimée avec succès.');
    }
}
