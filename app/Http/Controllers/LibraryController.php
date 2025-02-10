<?php

namespace App\Http\Controllers;

use App\Models\PublicImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LibraryController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();

        $startIndex = $request['loadingCount'] * 20;
        $searchTags = $request['searchTags'];

        $privateImages = $user->privateImages()->with('privateTags')->orderBy('created_at', 'desc');

        if ($searchTags) {
            $privateImages = $privateImages->whereHas('privateTags', function($query) use ($searchTags) {
                $query->whereIn('name', $searchTags);
            });
        }

        $privateImages = $privateImages->skip($startIndex)
            ->take(20)
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->publicImage->url,
                    'tags' => $image->privateTags->pluck('name')->toArray(),
                ];
            });

        return response()->json([
            'success' => true,
            'images' => $privateImages,
        ], 200);
    }

    public function store(Request $request) {
        $user = Auth::user();
        $image = $request['image'];

        try {
            DB::transaction(function () use ($image, $user) {
                $publicImage = PublicImage::find($image["id"]);
                $privateImage = $publicImage->privateImages()->create([
                    'user_id' => $user->id,
                ]);
                $privateImage->privateTags()->createMany(collect($image["tags"])->map(function ($tag) use ($privateImage) {
                    return [
                        'name' => $tag,
                    ];
                })->toArray());
            });
        } catch (Exception $e) {
            Log::warning($e);
            return response()->json([
                'success' => false,
                'messages' => ["マイライブラリの登録に失敗しました。"],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'messages' => ["マイライブラリに登録しました。"],
        ], 200);
    }

    public function tags() {
        $user = Auth::user();
        $privateImages = $user->privateImages()
            ->with('privateTags')
            ->get()
            ->toArray();

        $privateTags = [];
        foreach ($privateImages as $privateImage) {
            foreach ($privateImage['private_tags'] as $privateTag) {
                $addPrivateTag = [
                    "value" => $privateTag['name'],
                    "label" => $privateTag['name'],
                ];
                if (!in_array($addPrivateTag, $privateTags)) {
                    $privateTags[] = $addPrivateTag;
                }
            }
        }

        return response()->json([
            'success' => true,
            'tags' => $privateTags,
        ]);
    }
}
