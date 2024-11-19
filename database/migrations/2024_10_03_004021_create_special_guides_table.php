<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create( 'special_guides', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'person_id', 300 )->nullable();
            $table->string( 'person_object_id', 300 )->nullable();
            $table->string( 'person_event_id', 300 )->nullable();
            $table->string( 'person_location_id', 300 )->nullable();

            $table->timestamp( 'created_at' )->useCurrent();
            $table->timestamp( 'updated_at' )->useCurrent()->useCurrentOnUpdate();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists( 'special_guides' );
    }
};
