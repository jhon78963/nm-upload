<?php

namespace App\Shared\Image\Controllers;

use App\Shared\Image\Models\Image;
use App\Shared\Image\Resources\ImageResource;
use App\Shared\Image\Services\ImageService;
use App\Shared\Foundation\Controllers\Controller;
use App\Shared\Foundation\Requests\FileMultipleUploadRequest;
use App\Shared\Foundation\Requests\FileUploadRequest;
use App\Shared\Foundation\Requests\GetAllRequest;
use App\Shared\Foundation\Resources\GetAllCollection;
use App\Shared\Foundation\Services\FileService;
use App\Shared\Foundation\Services\SharedService;
use Illuminate\Http\JsonResponse;
use DB;

class ImageController extends Controller
{
    private string $images_path = 'images/products';
    protected FileService $fileService;
    protected ImageService $imageService;
    protected SharedService $sharedService;

    public function __construct(
        FileService $fileService,
        ImageService $imageService,
        SharedService $sharedService,
    ) {
        $this->fileService = $fileService;
        $this->imageService = $imageService;
        $this->sharedService = $sharedService;
    }

    public function upload(FileUploadRequest $request)
    {
        DB::beginTransaction();
        try {
            $imagePath = $this->fileService->upload($request, $this->images_path);
            $imageName = $this->imageService->getFileName($imagePath);
            $image = $this->imageService->create([
                'name' => $imageName,
                'path' => $imagePath,
                'company' => 'Novedades Maritex',
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Image uploaded.',
                'image' => $image->path,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
    }

    public function multipleUpload(FileMultipleUploadRequest $request)
    {
        DB::beginTransaction();
        try {
            $uploadedImages = $this->fileService->multipleUpload($request, $this->images_path);
            $images = [];
            foreach ($uploadedImages as $imagePath) {
                $roomImageName = $this->imageService->getFileName($imagePath);
                $image = $this->imageService->create([
                    'name' => $roomImageName,
                    'path' =>$imagePath,
                    'company' => 'Novedades Maritex',
                ]);
                $images[] = $image->path;
            }
            DB::commit();
            return response()->json([
                'message' => 'Images uploaded.',
                'images' => $images,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
    }

    public function delete(string $path): JsonResponse
    {
        $this->fileService->delete($path);
        $this->imageService->delete($path);
        return response()->json([
            'message' => 'Image removed from system'
        ]);
    }

    public function get(Image $image): JsonResponse
    {
        $imageValidated = $this->imageService->validate($image, 'Image');
        return response()->json(new ImageResource($imageValidated));
    }

    public function getAll(GetAllRequest $request): JsonResponse
    {
        $query = $this->sharedService->query($request, 'Shared\\Image', 'Image', 'name');
        return response()->json(new GetAllCollection(
            ImageResource::collection($query['collection']),
            $query['total'],
            $query['pages'],
        ));
    }
}
