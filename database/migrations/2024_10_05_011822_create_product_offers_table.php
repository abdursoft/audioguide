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
        Schema::create('product_offers', function (Blueprint $table) {
            $table->id();
            $table->enum('offer_type',['fixed','percent'])->default('fixed');
            $table->double('offer_amount');
            $table->double('price_amount');
            $table->enum('status', ['active','inactive'])->default('active');

            // make the relation with audio_guide table
            $table->unsignedBigInteger( 'audio_guide_id' );
            $table->foreign( 'audio_guide_id' )->references( 'id' )->on( 'audio_guides' )->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_offers');
    }
};
