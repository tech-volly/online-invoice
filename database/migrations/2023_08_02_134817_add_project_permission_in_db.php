<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectPermissionInDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('db', function (Blueprint $table) {
            //
            $data = [
                ['name' => 'project-list', 'guard_name' =>'web','created_at' => now(), 'updated_at' => now()],
                ['name' => 'project-create', 'guard_name' => 'web','created_at' => now(), 'updated_at' => now()],
                ['name' => 'project-edit', 'guard_name' => 'web','created_at' => now(), 'updated_at' => now()],
                ['name' => 'project-delete', 'guard_name' => 'web','created_at' => now(), 'updated_at' => now()],
                // Add more data as needed
            ];
    
            // Insert the data into the table
            DB::table('permissions')->insert($data);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('db', function (Blueprint $table) {
            //
        });
    }
}
