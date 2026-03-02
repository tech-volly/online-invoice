<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'user-list',
           'user-create',
           'user-edit',
           'user-delete',
           'client-list',
           'client-create',
           'client-edit',
           'client-delete',
           'supplier-list',
           'supplier-create',
           'supplier-edit',
           'supplier-delete'
        ];
     
        foreach($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
