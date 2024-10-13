<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create( 'audio_faqs', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'question', 300 );
            $table->longText( 'answer' );

            // make the relation with audio_guide table
            $table->unsignedBigInteger( 'audio_description_id' );
            $table->foreign( 'audio_description_id' )->references( 'id' )->on( 'audio_descriptions' )->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp( 'created_at' )->useCurrent();
            $table->timestamp( 'updated_at' )->useCurrent()->useCurrentOnUpdate();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists( 'audio_faqs' );
    }
};
