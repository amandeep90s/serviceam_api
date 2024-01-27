<?php

namespace Database\Seeders\Common;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CommonClearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('menus')->truncate();

        DB::table('menu_cities')->truncate();

        DB::table('provider_services')->truncate();

        DB::table('documents')->truncate();

        DB::table('disputes')->truncate();

        DB::table('disputes')->truncate();

        DB::table('reasons')->truncate();

        DB::table('promocodes')->truncate();

        Schema::enableForeignKeyConstraints();
    }
}
