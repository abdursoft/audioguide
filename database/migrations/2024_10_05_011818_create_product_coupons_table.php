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
        Schema::create('product_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title',70);
            $table->string('sub_title',110)->nullable();
            $table->string('banner',400)->nullable();
            $table->string('coupon',100);
            $table->date('started_at');
            $table->date('expired_at');
            $table->double('amount');
            $table->enum('coupon_type',['percent','fixed'])->default('fixed');
            $table->bigInteger('limitation')->default(1);
            $table->double('min_purchase');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_coupons');
    }
};
