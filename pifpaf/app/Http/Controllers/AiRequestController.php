<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAiImage;
use App\Models\AiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AiRequestController extends Controller
{
    /**
     * Display a listing of the user's AI requests.
     */
    public function index()
    {
        $requests = Auth::user()->aiRequests()->latest()->get();
        return view('ai-requests.index', compact('requests'));
    }

    /**
     * Store a new AI request and dispatch the processing job.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $request->file('image')->store('ai_images', 'public');

        $aiRequest = AiRequest::create([
            'user_id' => Auth::id(),
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        ProcessAiImage::dispatch($aiRequest);

        return redirect()->route('ai-requests.index')->with('success', 'Votre demande a été ajoutée à la file d\'attente.');
    }

    /**
     * Crop a preview of a detected object from the original image.
     */
    public function cropPreview(Request $request)
    {
        $validated = $request->validate([
            'image_path' => 'required|string',
            'box' => 'required|string', // The box is now a JSON string
        ]);

        $box = json_decode($validated['box'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($box)) {
            return response('Invalid box format.', 400);
        }

        $originalPath = $validated['image_path'];

        if (!Storage::disk('public')->exists($originalPath)) {
            return response('Image not found.', 404);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read(Storage::disk('public')->path($originalPath));

        $x1 = ($box['x1'] ?? 0) / 1000.0;
        $y1 = ($box['y1'] ?? 0) / 1000.0;
        $x2 = ($box['x2'] ?? 0) / 1000.0;
        $y2 = ($box['y2'] ?? 0) / 1000.0;

        $width = ($x2 - $x1) * $image->width();
        $height = ($y2 - $y1) * $image->height();
        $x = $x1 * $image->width();
        $y = $y1 * $image->height();

        if ($width <= 0 || $height <= 0) {
            return response('Invalid crop dimensions.', 400);
        }

        $croppedImage = $image->crop((int)$width, (int)$height, (int)$x, (int)$y);

        $encodedImage = $croppedImage->encode();

        return response($encodedImage)->header('Content-Type', $encodedImage->mediaType());
    }
}
