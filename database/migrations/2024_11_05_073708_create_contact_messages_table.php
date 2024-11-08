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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300);
            $table->string('email', 400);
            $table->string('subject', 400);
            $table->longText('message');
            $table->enum('seen', ['0', '1'])->default('0');
            $table->enum('replay', ['0', '1'])->default('0');
            $table->enum('is_admin',['0','1'])->default('0');
            $table->string('replay_id',400)->nullable();

            $table->string('seen_at', 60)->nullable();
            $table->string('replay_at', 60)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
