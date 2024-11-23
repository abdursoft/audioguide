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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->text('nome_e_cognome')->nullable();
            $table->text('anno_di_nascita')->nullable();
            $table->text('anno_di_morte')->nullable();
            $table->text('citta_di_nascita')->nullable();
            $table->text('citta_di_morte')->nullable();
            $table->text('nazione_di_nascita')->nullable();
            $table->text('continente_di_nascita')->nullable();
            $table->text('categoria')->nullable();
            $table->text('italiano')->nullable();
            $table->text('tag_title')->nullable();
            $table->text('tag_description')->nullable();
            $table->text('file_mp3')->nullable();
            $table->boolean('free')->default(false);
            $table->boolean('privacy')->default(false);
            $table->text('status_value')->nullable();
            $table->decimal('single_price', 8, 2)->nullable();
            $table->decimal('total_price', 8, 2)->nullable();
            $table->text('affiliate_platforms')->nullable();
            $table->text('affiliate_links')->nullable();
            $table->decimal('affiliate_price', 8, 2)->nullable();
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
            $table->text('anno_di_realizzazione')->nullable();
            $table->text('titolo')->nullable();
            $table->text('isbn')->nullable();
            $table->text('archteipo')->nullable();
            $table->text('argomento')->nullable();
            $table->text('biblioteca')->nullable();
            $table->text('capitolo')->nullable();
            $table->text('continente')->nullable();
            $table->text('nazione')->nullable();
            $table->text('citta')->nullable();
            $table->text('quartiere')->nullable();
            $table->text('luogo')->nullable();
            $table->decimal('latitudine', 10, 8)->nullable();
            $table->decimal('longitudine', 11, 8)->nullable();
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
            $table->text('person_name');

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
        Schema::dropIfExists('people');
    }
};
