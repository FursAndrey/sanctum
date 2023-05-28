<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Preview\StoreRequest;
use App\Http\Resources\PreviewResource;
use App\Models\Preview;
use Illuminate\Support\Facades\Storage;

class PreviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $preview = $request->validated();
        $path = Storage::disk('public')->put('/preview', $preview['image']);
        $storedPreview = Preview::create([
            'path' => $path,
            'user_id' => auth()->id(),
        ]);
        return new PreviewResource($storedPreview);
    }
}
