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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo',400)->nullable();
            $table->string('brand_logo',400)->nullable();
            $table->string('mobile_logo',400)->nullable();
            $table->string('icon',400)->nullable();
            $table->string('title',300)->nullable();
            $table->string('short_description',400)->nullable();
            $table->longText('description')->nullable();
            $table->string('primary_color', 100)->nullable();
            $table->string('secondary_color',100)->nullable();
            $table->string('phone',200)->nullable();
            $table->string('email',200)->nullable();
            $table->string('address',300)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
