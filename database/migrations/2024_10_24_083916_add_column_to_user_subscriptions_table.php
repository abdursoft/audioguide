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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->after('currency',function($table){
                $table->string( 'status', 40 )->default( 'pending' );
                $table->date( 'started_at');
                $table->date( 'ended_at');
                $table->string('invoice_url',400)->nullable();
                $table->string('stripe_subscription_id',400)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            //
        });
    }
};
