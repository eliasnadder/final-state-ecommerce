<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadImagesTrait
{
    public function uploadImage($file, $folderName)
    {
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs($folderName, $fileName, 's3');
            return Storage::disk('s3')->url($path);
        }

        return null;
    }

    public function uploadDocument($file, $folderName)
    {
        if ($file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs($folderName, $fileName, 's3');
            return Storage::disk('s3')->url($path);
        }

        return null;
    }

    public function uploadVideo($file, $folderName)
    {
        if ($file) {
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($folderName, $fileName, 's3');  // تخزين الفيديو في الـ public
            return Storage::disk('s3')->url($path);  // إرجاع رابط الفيديو
        }

        return null;
    }
}
