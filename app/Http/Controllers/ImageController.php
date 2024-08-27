<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as InterventionImage;

class ImageController extends BaseController
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');

        $resizedImage = InterventionImage::make($image)->resize(300, 300);

        $imageName = time() . '.' . $image->getClientOriginalExtension();

        $resizedImage->save(storage_path('app/public/' . $imageName));

        $imageModel = new Image();
        $imageModel->image_name = $imageName;
        $imageModel->image_path = 'storage/' . $imageName;
        $imageModel->save();

        return response()->json(['message' => 'Image uploaded and saved successfully!', 'image_url' => asset($imageModel->image_path)]);
    }

    public function show($id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image not found!'], 404);
        }

        return response()->json([
            'id' => $image->id,
            'image_url' => asset($image->image_path)
        ]);
    }
}
