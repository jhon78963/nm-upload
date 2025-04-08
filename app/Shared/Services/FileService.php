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
            $path = Storage::disk('s3')->put($filePath, $file);
            return $path;
        }

        return null;
    }

    public function multipleUpload(FileMultipleUploadRequest $request, String $filePath): array
    {
        $uploadedPaths = [];
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $uploadedPaths[] = Storage::disk('s3')->put($filePath,  $file);

            }
        }
        return $uploadedPaths;
    }

    public function get(string $filePath): ?string
    {
        return Storage::disk('s3')->exists($filePath)
            ? Storage::disk('s3')->path($filePath)
            : NULL;
    }

    public function delete(string $filePath): ?string
    {
        return Storage::disk('s3')->exists($filePath)
            ? Storage::disk('s3')->delete($filePath)
            : NULL;
    }
}
