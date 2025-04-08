<?php

namespace App\Permission\Services;

use App\Permission\Models\Permission;
use App\Shared\Services\ModelService;

class PermissionRoleService
{
    protected ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function attach(Permission $permission, int $roleId): void
    {
        $this->modelService->attach(
            $permission,
            'role_permission',
            [$roleId],
        );
    }

    public function detach(Permission $permission, int $roleId): void
    {
        $this->modelService->detach(
            $permission,
            'role_permission',
            $roleId,
        );
    }
}
