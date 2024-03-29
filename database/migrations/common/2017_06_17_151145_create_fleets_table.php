<?php

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
        Schema::create('fleets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('company')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile')->nullable();
            $table->string('logo')->nullable();
            $table->rememberToken();
            $table->double('commission', 5, 2)->default(0);
            $table->double('wallet_balance', 10, 2)->default(0);
            $table->string('stripe_cust_id')->nullable();
            $table->string('language', 10)->nullable();
            $table->tinyInteger('status')->default('1');
            $table->enum('created_type', ['ADMIN'])->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->enum('modified_type', ['ADMIN'])->nullable();
            $table->unsignedInteger('modified_by')->nullable();
            $table->enum('deleted_type', ['ADMIN'])->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
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
        Schema::drop('fleets');
    }
};
