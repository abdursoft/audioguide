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
            $table->string('luoghi')->nullable();
            $table->string('latitudine')->nullable();
            $table->string('longitudine')->nullable();
            $table->string('continente')->nullable();
            $table->string('nazione')->nullable();
            $table->string('città')->nullable();
            $table->string('quartiere')->nullable();
            $table->string('categoria')->nullable();
            $table->string('italiano')->nullable();
            $table->string('tag_title')->nullable();
            $table->string('tag_description')->nullable();
            $table->string('file_mp3')->nullable();
            $table->string('free')->nullable();
            $table->string('privacy')->nullable();
            $table->string('status_value')->nullable();
            $table->string('single_price')->nullable();
            $table->string('total_price')->nullable();
            $table->string('affiliate_platforms')->nullable();
            $table->string('affiliate_links')->nullable();
            $table->string('affiliate_price')->nullable();
            $table->string('paragrafo')->nullable();
            $table->string('link')->nullable();
            $table->string('artista')->nullable();
            $table->string('dipinto')->nullable();
            $table->string('scultura')->nullable();
            $table->string('dimensione')->nullable();
            $table->string('tecnica')->nullable();
            $table->string('movimento')->nullable();
            $table->string('museo')->nullable();
            $table->string('movimento_artistico')->nullable();
            $table->string('data_periodo')->nullable();
            $table->string('autore')->nullable();
            $table->string('libro')->nullable();
            $table->string('magazine')->nullable();
            $table->string('attore_attrice')->nullable();
            $table->string('film')->nullable();
            $table->string('regista')->nullable();
            $table->string('produttore')->nullable();
            $table->string('anno_di_nascita')->nullable();
            $table->string('anno_di_morte')->nullable();
            $table->string('continente_di_nascita')->nullable();
            $table->string('nazione_di_nascita')->nullable();
            $table->string('città_di_nascita')->nullable();
            $table->string('anno_di_realizzazione')->nullable();
            $table->string('titolo')->nullable();
            $table->string('isbn')->nullable();
            $table->string('archteipo')->nullable();
            $table->string('argomento')->nullable();
            $table->string('biblioteca')->nullable();
            $table->string('capitolo')->nullable();
            $table->string('continente_1')->nullable();
            $table->string('nazione_1')->nullable();
            $table->string('città_1')->nullable();
            $table->string('quartiere_1')->nullable();
            $table->string('luogo')->nullable();
            $table->string('latitudine_1')->nullable();
            $table->string('longitudine_1')->nullable();
            $table->string('campionato')->nullable();
            $table->string('coppa')->nullable();
            $table->string('squadra')->nullable();
            $table->string('giocatore')->nullable();
            $table->string('personaggio')->nullable();
            $table->string('personaggi_coinvolti')->nullable();
            $table->string('reale_fittizio_mitologico')->nullable();
            $table->string('serie_tv')->nullable();
            $table->string('produttore_1')->nullable();
            $table->string('videogame')->nullable();
            $table->string('titolo_dell_evento')->nullable();
            $table->string('tag_description_1')->nullable();
            $table->string('tag_title')->nullable();
            $table->string('free_1')->nullable();

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
