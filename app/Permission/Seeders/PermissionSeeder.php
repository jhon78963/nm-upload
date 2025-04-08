<?php

namespace App\Permission\Seeders;

use App\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = new Permission();
        $permission->name = 'get';
        $permission->save();
        $permission->roles()->attach(1);

        $permission = new Permission();
        $permission->name = 'modify';
        $permission->save();
        $permission->roles()->attach(1);

        $permission = new Permission();
        $permission->name = 'delete';
        $permission->save();

        $permission->roles()->attach(1);

        $permission = new Permission();
        $permission->name = 'factura';
        $permission->save();
        $permission->roles()->attach(1);

        $permission = new Permission();
        $permission->name = 'boleta';
        $permission->save();
        $permission->roles()->attach(1);

        $permission = new Permission();
        $permission->name = 'nota de crédito/débito';
        $permission->save();
        $permission->roles()->attach(1);

        $permission = new Permission();
        $permission->name = 'Guía de remisión';
        $permission->save();
        $permission->roles()->attach(1);
    }
}
