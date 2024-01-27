<?php

namespace Database\Seeders\Common;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('admin_services')->insert([

            [
                'admin_service' => 'SERVICE',
                'display_name' => 'SERVICE',
                'base_url' => 'http://127.0.0.1:8001/api/v1',
                'status' => '1',
                'company_id' => $company
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
