<?php

namespace Database\Seeders\Common;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            ['name' => 'ADMIN', 'guard_name' => 'admin', 'company_id' => null],
            ['name' => 'DISPATCHER', 'guard_name' => 'admin', 'company_id' => null],
            ['name' => 'DISPUTE', 'guard_name' => 'admin', 'company_id' => null],
            ['name' => 'ACCOUNT', 'guard_name' => 'admin', 'company_id' => null],
            ['name' => 'FLEET', 'guard_name' => 'admin', 'company_id' => null]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
