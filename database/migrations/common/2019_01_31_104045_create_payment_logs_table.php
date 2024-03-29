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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('admin_service')->nullable();
            $table->integer('is_wallet')->default(0);
            $table->string('user_type')->nullable()->comment('user or provider');
            $table->string('payment_mode')->nullable();
            $table->integer('user_id')->comment('user id or provider id');
            $table->float('amount', 15, 2)->default(0);
            $table->string('transaction_code')->nullable()->comment('Random code generated during payment');
            $table->string('transaction_id')->nullable()->comment('Foreign key of the user request or wallet table');
            $table->text('response')->nullable();
            $table->enum('created_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->enum('modified_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
            $table->unsignedInteger('modified_by')->nullable();
            $table->enum('deleted_type', ['ADMIN', 'USER', 'PROVIDER', 'SHOP'])->nullable();
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
        Schema::dropIfExists('payment_logs');
    }
};
