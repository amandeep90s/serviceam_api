<?php

namespace Database\Seeders\Common;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($company = null): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('documents')->insert([
            [
                'company_id' => $company,
                'service' => 'SERVICE',
                'name' => 'License',
                'type' => 'SERVICE',
                'file_type' => 'image',
                'is_backside' => 1,
                'is_expire' => 1,
                'status' => 1
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
