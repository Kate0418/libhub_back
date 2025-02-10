<?php

namespace App\Http\Controllers;

use App\Models\PublicImage;
use App\Models\Transition;
use Illuminate\Http\Request;

class TransitionController extends Controller
{
    public function index(Request $request) {
        $imageId = $request->imageId;

        $destinationImages = PublicImage::find($imageId)
            ->transitions()
            ->orderBy('transition_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->with(['publicImage', 'publicImage.publicTags'])
            ->get()
            ->map(function ($transition) {
                return [
                    'id' => $transition->publicImage->id,
                    'url' => $transition->publicImage->url,
                    'tags' => $transition->publicImage->publicTags->pluck('name')->toArray(),
                ];
            })
            ->toArray();
        $destinationImageIds = array_column($destinationImages, 'id');

        $publicImages = PublicImage::limit(10 - count($destinationImages))
            ->orderBy('view_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->with('publicTags')
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'tags' => $image->publicTags->pluck('name')->toArray(),
                ];
            })
            ->toArray();

        $filteredPublicImages = array_filter($publicImages, function ($image) use ($destinationImageIds) {
            return !in_array($image['id'], $destinationImageIds);
        });

        $images = array_merge($destinationImages, $filteredPublicImages);

        return response()->json([
            'success' => true,
            'images' => $images,
        ], 200);
    }


    public function store(Request $request) {
        $sourceImageId = $request->sourceImageId;
        $destinationImageId = $request->destinationImageId;

        $transition = Transition::where('source_image_id', $sourceImageId)
            ->where('destination_image_id', $destinationImageId)
            ->first();

        if ($transition) {
            $transition->increment('transition_count');
        } else {
            Transition::create([
                'source_image_id' => $sourceImageId,
                'destination_image_id' => $destinationImageId,
                'transition_count' => 1,
            ]);
        }
    }
}
