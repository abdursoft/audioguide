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
        Schema::create('audio_contents', function (Blueprint $table) {
            $table->id();
            $table->string('materia',300)->nullable();
            $table->string('argomento',300)->nullable();
            $table->string('capitolo',300)->nullable();
            $table->longText('titolo')->nullable();
            $table->string('short_description',300)->nullable();
            $table->longText('descrizione')->nullable();
            $table->string('file_mp3',300)->nullable();
            $table->string('dipinto',300)->nullable();
            $table->string('artista',300)->nullable();
            $table->string('movimento',300)->nullable();
            $table->string('luogo',300)->nullable();
            $table->string('città',300)->nullable();
            $table->string('anno_di_realizzazione',300)->nullable();
            $table->string('dimensione',300)->nullable();
            $table->string('tecnica',300)->nullable();
            $table->string('anno_di_nascita',300)->nullable();
            $table->string('anno_di_morte',300)->nullable();
            $table->string('nazione_di_nascita',300)->nullable();
            $table->string('latitudine',300)->nullable();
            $table->string('continente',300)->nullable();
            $table->string('nazione',300)->nullable();
            $table->string('quartiere',300)->nullable();
            $table->longText('categoria')->nullable();
            $table->string('movimento_artistico',300)->nullable();
            $table->string('paragrafo',300)->nullable();
            $table->longText('descrizione_breve')->nullable();
            $table->string('film',300)->nullable();
            $table->string('serie_tv',300)->nullable();
            $table->string('libro',300)->nullable();
            $table->string('autore',300)->nullable();
            $table->string('editore',300)->nullable();
            $table->string('link',300)->nullable();
            $table->string('personaggio',300)->nullable();
            $table->string('actor_ctress',300)->nullable();
            $table->string('videogame',300)->nullable();
            $table->string('produttore',300)->nullable();
            $table->string('isbn',300)->nullable();
            $table->string('magazine',300)->nullable();
            $table->string('reale_fittizio_mitologico',300)->nullable();
            $table->string('titolo_dell_evento',300)->nullable();
            $table->string('data_periodo',300)->nullable();
            $table->string('giocatore',300)->nullable();
            $table->string('squadra',300)->nullable();
            $table->string('campionato',300)->nullable();
            $table->string('coppa',300)->nullable();
            $table->string('biblioteca',300)->nullable();
            $table->string('tag_title',300)->nullable();
            $table->string('tag_description',300)->nullable();
            $table->string('status_value',300)->nullable();
            $table->string('single_price',300)->nullable();
            $table->string('free',20)->nullable();

            // make the relation with audio_guide table
            $table->unsignedBigInteger('audio_guide_id');
            $table->foreign('audio_guide_id')->references('id')->on('audio_guides')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_contents');
    }
};
