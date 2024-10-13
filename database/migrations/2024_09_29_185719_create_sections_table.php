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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name',300);
            $table->string('title',300);
            $table->string('sub_title',300)->nullable();
            $table->string('short_description',600)->nullable();
            $table->longText('description')->nullable();
            $table->string('button_title',300)->nullable();
            $table->string('button_action',300)->nullable();
            $table->longText('image')->nullable();
            $table->string('mobile_image',300)->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
