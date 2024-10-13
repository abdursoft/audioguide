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
        Schema::create('audio_guides', function (Blueprint $table) {
            $table->id();
            $table->string('name',400);
            $table->string('title',400);
            $table->enum('status', ['active','inactive'])->default('active');
            $table->decimal('price')->default(0);
            $table->string('remark',200)->nullable();
            $table->decimal('discount')->default(0);
            $table->string('cover',300)->nullable();
            $table->longText('short_description')->nullable();

            // make the relation with category table 
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_guides');
    }
};
