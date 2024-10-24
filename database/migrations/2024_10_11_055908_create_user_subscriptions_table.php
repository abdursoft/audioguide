<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create( 'user_subscriptions', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'payment_id', 300 )->nullable();
            $table->double( 'paid_amount' )->default( 0 );
            $table->string( 'currency', 40 )->default( 'usd' );

            // make the relation with user table
            $table->unsignedBigInteger( 'user_id' );
            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )->cascadeOnUpdate()->restrictOnDelete();

            // make the relation with user table
            $table->unsignedBigInteger( 'subscription_id' );
            $table->foreign( 'subscription_id' )->references( 'id' )->on( 'subscriptions' )->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp( 'created_at' )->useCurrent();
            $table->timestamp( 'updated_at' )->useCurrent()->useCurrentOnUpdate();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists( 'user_subscriptions' );
    }
};
