<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::connection('service')->create('service_main_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('service_main_service_id');
            $table->unsignedInteger('company_id');
            $table->string('service_main_service_name');
            $table->string('picture')->nullable();
            $table->mediumInteger('service_main_service_order');
            $table->tinyInteger('service_main_service_status');
            $table->enum('created_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->enum('modified_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('modified_by')->nullable();
            $table->enum('deleted_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('service_main_services');
    }
};
