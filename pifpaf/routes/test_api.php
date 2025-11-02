<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/test/analyze-image', function (Request $request) {
    return response()->json([
        [
            'title' => 'T-shirt vintage',
            'description' => 'Un super t-shirt des années 90.',
            'category' => 'Vêtements',
            'price' => 15.5,
            'box' => ['x1' => 0.1, 'y1' => 0.1, 'x2' => 0.4, 'y2' => 0.5],
        ],
        [
            'title' => 'Casquette de baseball',
            'description' => 'Une casquette pour les fans.',
            'category' => 'Vêtements',
            'price' => 10.0,
            'box' => ['x1' => 0.5, 'y1' => 0.2, 'x2' => 0.8, 'y2' => 0.4],
        ],
    ]);
});
