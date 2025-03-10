<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileUpload
{
    public static function upload($file, $folder = 'image/uploads', $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'], $maxSize = 5 * 1024 * 1024)
    {
        if(!$file) {
            return null;
        }

        if($file->getSize() > $maxSize) {
            throw new \Exception("File size exceeds the maximum limit of " . ($maxSize / (1024 * 1024)) . " MB. Your file: " . round($file->getSize() / (1024 * 1024), 2) . " MB.");
        }
        $fileMimeType    = $file->getMimeType();
        $fileExtension   = strtolower($file->getClientOriginalExtension());
        $mimeToExtension = [
            'image/jpeg'                                                              => 'jpg',
            'image/png'                                                               => 'png',
            'application/pdf'                                                         => 'pdf',
            'application/zip'                                                         => 'zip',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];

        $allowedExtensions = array_map(fn($mime) => $mimeToExtension[$mime] ?? null, $allowedMimeTypes);
        $allowedExtensions = array_filter($allowedExtensions);
        if(!in_array($fileMimeType, $allowedMimeTypes) || !in_array($fileExtension, $allowedExtensions)) {
            throw new \Exception("Invalid file type. Allowed types: " . implode(', ', $allowedExtensions));
        }

        $filename = time() . '-' . uniqid() . '.' . $fileExtension;
        $path     = $folder . '/' . $filename;
        Storage::disk('public')->putFileAs($folder, $file, $filename);
        return $path;
    }

    public static function delete($path, $disk = 'public')
    {
        if(empty($path)) {
            return false;
        }
        if(Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        return false;
    }
}
