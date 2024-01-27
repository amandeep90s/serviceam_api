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
        Schema::connection('service')->create('service_sub_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('service_category_id');
            $table->unsignedInteger('service_subcategory_id');
            $table->unsignedInteger('service_id');
            $table->unsignedInteger('company_id');
            $table->string('service_name');
            $table->string('picture')->nullable();
            $table->tinyInteger('allow_desc')->default(0)->comment = '1-Allow,0-Not Allow';
            $table->tinyInteger('allow_before_image')->default(0)->comment = '1-Allow,0-Not Allow';
            $table->tinyInteger('allow_after_image')->default(0)->comment = '1-Allow,0-Not Allow';
            $table->tinyInteger('is_professional')->default(0);
            $table->tinyInteger('service_status')->default(1);
            $table->enum('approved_status', ['APPROVED', 'PENDING'])->nullable();
            $table->enum('created_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->enum('modified_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('modified_by')->nullable();
            $table->enum('deleted_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->double('base_fare_other', 15, 8)->nullable();
            $table->double('hourly_rate', 15, 8)->nullable();
            $table->double('minimum_hours', 15, 8)->nullable();
            $table->string('experience')->nullable();
            $table->string('certification')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_category_id')->references('id')->on('service_categories')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('service_subcategory_id')->references('id')->on('service_subcategories')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_services');
    }
};
