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
            $table->string('nome_e_cognome')->nullable();
            $table->integer('anno_di_nascita')->nullable();
            $table->integer('anno_di_morte')->nullable();
            $table->string('citta_di_nascita')->nullable();
            $table->string('citta_di_morte')->nullable();
            $table->string('nazione_di_nascita')->nullable();
            $table->string('continente_di_nascita')->nullable();
            $table->string('categoria')->nullable();
            $table->text('italiano')->nullable();
            $table->string('tag_title')->nullable();
            $table->text('tag_description')->nullable();
            $table->string('file_mp3')->nullable();
            $table->boolean('free')->default(false);
            $table->boolean('privacy')->default(false);
            $table->string('status_value')->nullable();
            $table->decimal('single_price', 8, 2)->nullable();
            $table->decimal('total_price', 8, 2)->nullable();
            $table->text('affiliate_platforms')->nullable();
            $table->text('affiliate_links')->nullable();
            $table->decimal('affiliate_price', 8, 2)->nullable();
            $table->text('paragrafo')->nullable();
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
            $table->integer('anno_di_nascita_1')->nullable();
            $table->integer('anno_di_morte_1')->nullable();
            $table->string('continente_di_nascita_1')->nullable();
            $table->string('nazione_di_nascita_1')->nullable();
            $table->string('citta_di_nascita_1')->nullable();
            $table->string('anno_di_realizzazione')->nullable();
            $table->string('titolo')->nullable();
            $table->string('isbn')->nullable();
            $table->string('archteipo')->nullable();
            $table->text('argomento')->nullable();
            $table->string('biblioteca')->nullable();
            $table->string('capitolo')->nullable();
            $table->string('continente')->nullable();
            $table->string('nazione')->nullable();
            $table->string('citta')->nullable();
            $table->string('quartiere')->nullable();
            $table->string('luogo')->nullable();
            $table->decimal('latitudine', 10, 8)->nullable();
            $table->decimal('longitudine', 11, 8)->nullable();
            $table->string('campionato')->nullable();
            $table->string('coppa')->nullable();
            $table->string('squadra')->nullable();
            $table->string('giocatore')->nullable();
            $table->string('personaggio')->nullable();
            $table->text('personaggi_coinvolti')->nullable();
            $table->string('reale_fittizio_mitologico')->nullable();
            $table->string('serie_tv')->nullable();
            $table->string('produttore_1')->nullable();
            $table->string('videogame')->nullable();
            $table->string('titolo_dell_evento')->nullable();
            $table->text('tag_description_1')->nullable();
            $table->string('tag_title_1')->nullable();

            // make the relation 

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
