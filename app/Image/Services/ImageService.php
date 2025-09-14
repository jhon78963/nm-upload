<?php
namespace App\Image\Services;

use App\Image\Models\Image;
use App\Shared\Services\ModelService;

class ImageService {
    protected ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function create(array $newImage): Image
    {
        return $this->modelService->create(new Image(), $newImage);
    }

    public function delete(string $path): void
    {
        $image = $this->modelService->get(new Image(), 'path', $path);
        $this->modelService->delete($image);
    }

    public function getFileName(string $filePath): string
    {
        return basename($filePath);
    }

    public function validate(Image $image, string $modelName): Image
    {
        return $this->modelService->validate($image, $modelName);
    }
}
