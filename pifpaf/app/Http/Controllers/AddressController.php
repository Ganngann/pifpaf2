<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AddressController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $pickupAddresses = $user->pickupAddresses;
        $shippingAddresses = $user->shippingAddresses;

        return view('profile.addresses.index', compact('pickupAddresses', 'shippingAddresses'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type');
        if (!in_array($type, ['pickup', 'shipping'])) {
            abort(400, 'Invalid address type.');
        }
        return view('profile.addresses.create', ['type' => $type]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:pickup,shipping',
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:255',
        ]);

        if ($validatedData['type'] === 'pickup') {
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

    public function edit(Address $address)
    {
        $this->authorize('update', $address);
        return view('profile.addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:255',
        ]);

        if ($address->type === 'pickup') {
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
        }

        $address->update($validatedData);

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse mise à jour avec succès.');
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return redirect()->route('profile.addresses.index')->with('success', 'Adresse supprimée avec succès.');
    }
}
