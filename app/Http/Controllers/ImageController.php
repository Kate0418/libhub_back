<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageStoreRequest;
use App\Models\PublicImage;
use App\Models\searchTag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function index(Request $request) {
        $startIndex = $request['loadingCount'] * 20;
        $searchTags = $request['searchTags'];

        $publicImages = PublicImage::with('publicTags')
            ->orderBy('view_count', 'desc')
            ->orderBy('created_at', 'desc');

        if ($searchTags) {
            $publicImages = $publicImages->whereHas('publicTags', function($query) use ($searchTags) {
                $query->whereIn('name', $searchTags);
            });
        }

        $publicImages = $publicImages->skip($startIndex)
            ->take(20)
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'tags' => $image->publicTags->pluck('name')->toArray(),
                ];
            });

        return response()->json([
            'success' => true,
            'images' => $publicImages,
        ], 200);
    }


    public function store(ImageStoreRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $image = $validated['image'];

        try {
            DB::transaction(function () use ($user, $image) {
                $path = Storage::disk('s3')->putFile('libhub', $image['file']);
                $imageUrl = config('filesystems.disks.s3.url') . '/' . $path;

                $createdPublicImage = PublicImage::create([
                    'url' => $imageUrl,
                ]);

                $createPrivateImage = $createdPublicImage->privateImages()
                    ->create([
                        "user_id" => $user->id,
                        "is_mine" => true,
                    ]);

                $tags = collect($image['tags'])->map(function ($tag) {
                    return ['name' => $tag];
                })->toArray();
                $createdPublicImage->publicTags()
                    ->createMany($tags);
                $createPrivateImage->privateTags()
                    ->createMany($tags);

                searchTag::insertOrIgnore($tags);
            });
        } catch (Exception $e) {
            Log::warning($e);
            return response()->json([
                'success' => false,
                'messages' => ['ライブラリの登録に失敗しました。'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'messages' => ['ライブラリの登録に成功しました。'],
        ], 200);
    }

    public function view(Request $request) {
        $imageId = $request->imageId;
        PublicImage::find($imageId)->increment('view_count');
    }
}
