<?php

namespace App\Http\Controllers;

use App\Models\PickupAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PickupAddressController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $pickupAddresses = $user->pickupAddresses;
        $shippingAddresses = $user->shippingAddresses;

        return view('profile.addresses.index', compact('pickupAddresses', 'shippingAddresses'));
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
        ]);

        // Geocoding
        $addressString = "{$validatedData['street']}, {$validatedData['postal_code']} {$validatedData['city']}, Belgium";
        $response = Http::get('https://geocode.maps.co/search', [
            'q' => $addressString,
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            $geocodedData = $response->json()[0];
            $validatedData['latitude'] = $geocodedData['lat'];
            $validatedData['longitude'] = $geocodedData['lon'];
        }

        Auth::user()->pickupAddresses()->create($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse ajoutée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PickupAddress $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PickupAddress $address)
    {
        $this->authorize('update', $address);
        return view('profile.addresses.edit', ['address' => $address]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PickupAddress $address)
    {
        $this->authorize('update', $address);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ]);

        // Geocoding
        $addressString = "{$validatedData['street']}, {$validatedData['postal_code']} {$validatedData['city']}, Belgium";
        $response = Http::get('https://geocode.maps.co/search', [
            'q' => $addressString,
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            $geocodedData = $response->json()[0];
            $validatedData['latitude'] = $geocodedData['lat'];
            $validatedData['longitude'] = $geocodedData['lon'];
        } else {
            // En cas d'échec du géocodage, on ne met pas à jour les coordonnées
            // pour ne pas écraser d'anciennes valeurs correctes.
            $validatedData['latitude'] = $address->latitude;
            $validatedData['longitude'] = $address->longitude;
        }


        $address->update($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse mise à jour avec succès.');
    }

}
