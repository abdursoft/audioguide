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
            $table->string('logo',400);
            $table->string('icon',400);
            $table->string('title',300);
            $table->string('short_description',400);
            $table->longText('description');
            $table->string('primary_color', 100);
            $table->string('secondary_color',100);
            $table->string('phone',200);
            $table->string('email',200);
            $table->string('address',300);

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
