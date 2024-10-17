<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->decimal('vat')->default('0');
            $table->decimal('total');
            $table->decimal('discount');
            $table->decimal('sub_total');
            $table->decimal('payable');
            $table->string('trans_id',300);
            $table->string('val_id',10);
            $table->string('gateway',300)->nullable();
            $table->string('payment_id',400)->nullable();
            $table->enum('delivery_status',["pending","complete"])->default('pending');
            $table->string('payment_status',100)->default('pending');

            // make the relation with user and payment table
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp( 'created_at' )->useCurrent();
            $table->timestamp( 'updated_at' )->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
