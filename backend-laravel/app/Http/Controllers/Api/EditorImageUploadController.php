<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditorImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,gif,bmp,webp,svg', 'max:5120'],
        ]);

        $file = $request->file('image');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('editor/images', $filename, 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
