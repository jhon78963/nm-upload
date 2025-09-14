<?php

namespace App\Shared\Services;

use App\Shared\Requests\FileMultipleUploadRequest;
use App\Shared\Requests\FileUploadRequest;
use Storage;

class FileService
{
    public function upload(FileUploadRequest $request, String $filePath): ?string
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $path = Storage::disk('public')->putFileAs($filePath, $file, $originalName);
            return $path;
        }

        return null;
    }

    public function multipleUpload(FileMultipleUploadRequest $request, String $filePath): array
    {
        $uploadedPaths = [];
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $originalName = $file->getClientOriginalName();
                $uploadedPaths[] = Storage::disk('public')->putFileAs($filePath,  $file, $originalName);

            }
        }
        return $uploadedPaths;
    }

    public function get(string $filePath): ?string
    {
        return Storage::disk('public')->exists($filePath)
            ? Storage::disk('public')->path($filePath)
            : NULL;
    }

    public function delete(string $filePath): ?string
    {
        return Storage::disk('public')->exists($filePath)
            ? Storage::disk('public')->delete($filePath)
            : NULL;
    }
}
