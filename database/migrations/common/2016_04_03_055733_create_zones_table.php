<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {

        Schema::connection('common')->create('zones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('city_id');
            $table->integer('company_id');
            $table->enum('user_type', [
                'SHOP',
                'PROVIDER',
                'FLEET'
            ])->default('SHOP');
            $table->enum('status', [
                'ACTIVE',
                'INACTIVE'
            ])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
