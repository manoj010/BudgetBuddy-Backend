<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

trait UserImage
{ 
    public function imageUpload($image)
    {
        try {
            $resizedImage = InterventionImage::make($image)->resize(300, 300);

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $storagePath = 'public/' . $imageName;

            Storage::makeDirectory('public');

            $resizedImage->save(storage_path('app/' . $storagePath));

            return [
                'image_name' => $imageName,
                'image_path' => 'storage/' . $imageName
            ];

        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ];
        }
    }
}
