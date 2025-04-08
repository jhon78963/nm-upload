<?php

namespace App\Permission\Services;

use App\Permission\Models\Permission;
use App\Shared\Services\ModelService;

class PermissionService
{
    protected ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function create(array $newPermission): void
    {
        $this->modelService->create(new Permission(), $newPermission);
    }

    public function delete(Permission $permission): void
    {
        $this->modelService->delete($permission);
    }

    public function update(Permission $permission, array $editPermission): void
    {
        $this->modelService->update($permission, $editPermission);
    }

    public function validate(Permission $permission, string $modelName): Permission
    {
        return $this->modelService->validate($permission, $modelName);
    }
}
