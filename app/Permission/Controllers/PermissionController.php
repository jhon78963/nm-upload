<?php

namespace App\Permission\Controllers;

use App\Permission\Models\Permission;
use App\Permission\Requests\PermissionCreateRequest;
use App\Permission\Requests\PermissionUpdateRequest;
use App\Permission\Resources\PermissionResource;
use App\Permission\Services\PermissionService;
use App\Shared\Controllers\Controller;
use App\Shared\Requests\GetAllRequest;
use App\Shared\Resources\GetAllCollection;
use App\Shared\Services\SharedService;
use Illuminate\Http\JsonResponse;
use DB;

class PermissionController extends Controller
{

    protected PermissionService $permissionService;
    protected SharedService $sharedService;

    public function __construct(PermissionService $permissionService, SharedService $sharedService)
    {
        $this->permissionService = $permissionService;
        $this->sharedService = $sharedService;
    }

    public function create(PermissionCreateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $newPermission = $this->sharedService->convertCamelToSnake($request->validated());
            $this->permissionService->create($newPermission);
            DB::commit();
            return response()->json(['message' => 'Permission created.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function delete(Permission $permission): JsonResponse
    {
        DB::beginTransaction();
        try {
            $permissionValidated = $this->permissionService->validate($permission, 'Permission');
            $this->permissionService->delete($permissionValidated);
            DB::commit();
            return response()->json(['message' => 'Permission deleted.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function get(Permission $permission): JsonResponse
    {
        $permissionValidated = $this->permissionService->validate($permission, 'Permission');
        return response()->json(new PermissionResource($permissionValidated));
    }

    public function getAll(GetAllRequest $request): JsonResponse
    {
        $query = $this->sharedService->query(
            $request,
            'Permission',
            'Permission',
            'name'
        );

        return response()->json(new GetAllCollection(
            PermissionResource::collection($query['collection']),
            $query['total'],
            $query['pages'],
        ));
    }

    public function update(PermissionUpdateRequest $request, Permission $permission): JsonResponse
    {
        DB::beginTransaction();
        try {
            $editPermission = $this->sharedService->convertCamelToSnake($request->validated());
            $permissionValidated = $this->permissionService->validate($permission, 'Permission');
            $this->permissionService->update($permissionValidated, $editPermission);
            DB::commit();
            return response()->json(['message' => 'Permission updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }
}
