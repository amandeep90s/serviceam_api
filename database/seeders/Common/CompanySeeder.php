<?php

namespace Database\Seeders\Common;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('companies')->truncate();
        DB::table('companies')->insert([
            [
                'company_name' => 'GoX',
                'domain' => '127.0.0.1',
                'base_url' => 'http://127.0.0.1:8001/api/v1',
                'socket_url' => 'http://127.0.0.1:8990',
                'access_key' => '123456',
                'expiry_date' => Carbon::now()->addYear(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
