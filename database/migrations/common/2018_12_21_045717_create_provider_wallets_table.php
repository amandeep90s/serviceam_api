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
        Schema::create('provider_wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('provider_id');
            $table->unsignedInteger('company_id');
            $table->enum('admin_service', ['TRANSPORT', 'ORDER', 'SERVICE'])->nullable();
            $table->integer('transaction_id');
            $table->string('transaction_alias')->nullable();
            $table->string('transaction_desc')->nullable();
            $table->enum('type', ['C', 'D']);
            $table->double('amount', 15, 8)->default(0);
            $table->double('open_balance', 15, 8)->default(0);
            $table->double('close_balance', 15, 8)->default(0);
            $table->enum('created_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->enum('modified_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('modified_by')->nullable();
            $table->enum('deleted_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('provider_id')->references('id')->on('providers')
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
        Schema::dropIfExists('provider_wallets');
    }
};
