<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddMorePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'expense-list',
            'expense-create',
            'expense-edit',
            'expense-delete',
            'invoice-list',
            'invoice-create',
            'invoice-edit',
            'invoice-delete',
            'lead-list',
            'lead-create',
            'lead-edit',
            'lead-delete',
            'product-category-list',
            'product-category-create',
            'product-category-edit',
            'product-category-delete',
            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',
            'brand-list',
            'brand-create',
            'brand-edit',
            'brand-delete'
        ];

        foreach($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
