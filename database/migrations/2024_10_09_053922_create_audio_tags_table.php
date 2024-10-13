<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create( 'audio_tags', function ( Blueprint $table ) {
            $table->id();
            $table->longText( 'tags' )->nullable();

            // make the relation with audio_guide table
            $table->unsignedBigInteger( 'audio_guide_id' );
            $table->foreign( 'audio_guide_id' )->references( 'id' )->on( 'audio_guides' )->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp( 'created_at' )->useCurrent();
            $table->timestamp( 'updated_at' )->useCurrent()->useCurrentOnUpdate();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists( 'audio_tags' );
    }
};
