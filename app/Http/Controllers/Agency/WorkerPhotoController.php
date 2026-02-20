<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkerPhotoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        try {
            $file = $request->file('photo');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destinationPath = storage_path('app/public/workers');
            
            // Manual file move to avoid storeAs issues
            $file->move($destinationPath, $filename);
            $path = 'workers/' . $filename;
            
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $path = $request->input('path');
            
            // Manual file deletion
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Photo removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
