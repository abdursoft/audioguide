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
        Schema::create('front_sections', function (Blueprint $table) {
            $table->id();
            $table->string('pagename',200);
            $table->string('section_title',300);
            $table->string('section_title_two',300)->nullable();
            $table->string('heading',300)->nullable();
            $table->string('heading_part_two',300)->nullable();
            $table->string('subheading',300)->nullable();
            $table->string('subheading_part_two',300)->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image','400')->nullable();
            $table->longText('faqs')->nullable();
            
            $table->timestamp( 'created_at' )->useCurrent();
            $table->timestamp( 'updated_at' )->useCurrent()->useCurrentOnUpdate();
        });
    }
    // pagename,section_title,section_title-two,heading,heading_part_two,subheading,subheading_part_two,short_decscription, decscription, image, faqs[{qustion,answer}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_sections');
    }
};
