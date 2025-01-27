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
        Schema::create('person_locations', function (Blueprint $table) {
            $table->id();
            $table->text('luoghi')->nullable();
            $table->text('latitudine')->nullable();
            $table->text('longitudine')->nullable();
            $table->text('continente')->nullable();
            $table->text('nazione')->nullable();
            $table->text('città')->nullable();
            $table->text('quartiere')->nullable();
            $table->text('categoria')->nullable();
            $table->text('italiano')->nullable();
            $table->text('tag_title')->nullable();
            $table->text('tag_description')->nullable();
            $table->text('file_mp3')->nullable();
            $table->text('free')->nullable();
            $table->text('privacy')->nullable();
            $table->text('status_value')->nullable();
            $table->text('single_price')->nullable();
            $table->text('total_price')->nullable();
            $table->text('affiliate_platforms')->nullable();
            $table->text('affiliate_links')->nullable();
            $table->text('affiliate_price')->nullable();
            $table->text('paragrafo')->nullable();
            $table->text('link')->nullable();
            $table->text('artista')->nullable();
            $table->text('dipinto')->nullable();
            $table->text('scultura')->nullable();
            $table->text('dimensione')->nullable();
            $table->text('tecnica')->nullable();
            $table->text('movimento')->nullable();
            $table->text('museo')->nullable();
            $table->text('movimento_artistico')->nullable();
            $table->text('data_periodo')->nullable();
            $table->text('autore')->nullable();
            $table->text('libro')->nullable();
            $table->text('magazine')->nullable();
            $table->text('attore_attrice')->nullable();
            $table->text('film')->nullable();
            $table->text('regista')->nullable();
            $table->text('produttore')->nullable();
            $table->text('anno_di_nascita')->nullable();
            $table->text('anno_di_morte')->nullable();
            $table->text('continente_di_nascita')->nullable();
            $table->text('nazione_di_nascita')->nullable();
            $table->text('città_di_nascita')->nullable();
            $table->text('anno_di_realizzazione')->nullable();
            $table->text('titolo')->nullable();
            $table->text('isbn')->nullable();
            $table->text('archteipo')->nullable();
            $table->text('argomento')->nullable();
            $table->text('biblioteca')->nullable();
            $table->text('capitolo')->nullable();
            $table->text('luogo')->nullable();
            $table->text('campionato')->nullable();
            $table->text('coppa')->nullable();
            $table->text('squadra')->nullable();
            $table->text('giocatore')->nullable();
            $table->text('personaggio')->nullable();
            $table->text('personaggi_coinvolti')->nullable();
            $table->text('reale_fittizio_mitologico')->nullable();
            $table->text('serie_tv')->nullable();
            $table->text('videogame')->nullable();
            $table->text('titolo_dell_evento')->nullable();
            $table->text('image')->nullable();
            $table->text('location_name');

            // make the relation
            $table->unsignedBigInteger('audio_guide_id');
            $table->foreign('audio_guide_id')->references('id')->on('audio_guides')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp( 'created_at' )->useCurrent();
            $table->timestamp( 'updated_at' )->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_locations');
    }
};
