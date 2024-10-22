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
            $table->string( 'continente', 300 )->nullable();
            $table->string( 'nazione', 300 )->nullable();
            $table->string( 'città', 300 )->nullable();
            $table->string( 'quartiere', 300 )->nullable();
            $table->string( 'museo', 300 )->nullable();
            $table->decimal( 'latitudine' )->nullable();
            $table->decimal( 'longitudine' )->nullable();
            $table->string( 'categoria' )->nullable();
            $table->string( 'tag_title', 300 )->nullable();
            $table->longText( 'tag_description' )->nullable();
            $table->string( 'file_mp3', 300 )->nullable();
            $table->string( 'artista', 300 )->nullable();
            $table->string( 'movimento', 300 )->nullable();
            $table->string( 'luogo', 300 )->nullable();
            $table->string( 'anno_di_realizzazione', 300 )->nullable();
            $table->string( 'dimensione', 300 )->nullable();
            $table->string( 'tecnica', 300 )->nullable();
            $table->string( 'anno_di_nascita', 300 )->nullable();
            $table->string( 'anno_di_morte', 300 )->nullable();
            $table->string( 'città_di_nascita', 300 )->nullable();
            $table->string( 'città_di_morte', 300 )->nullable();
            // $table->string( 'calciatore', 300 )->nullable();
            // $table->string( 'ruolo', 300 )->nullable();
            // $table->string( 'squadra', 300 )->nullable();
            // $table->string( 'nazionalità', 300 )->nullable();
            // $table->string( 'numero_di_maglia', 300 )->nullable();
            // $table->string( 'stadio', 300 )->nullable();
            // $table->string( 'anno_di_fondazione_squadra', 300 )->nullable();
            // $table->longText( 'palmarès_squadra' )->nullable();
            // $table->string( 'campionato', 300 )->nullable();
            // $table->string( 'gol_segnati', 300 )->nullable();
            // $table->string( 'assist', 300 )->nullable();
            // $table->string( 'presenze', 300 )->nullable();
            // $table->string( 'minuti_giocati', 300 )->nullable();
            // $table->string( 'allenatore', 300 )->nullable();
            // $table->string( 'modulo_di_gioco', 300 )->nullable();
            // $table->string( 'anno_di_inizio_carriera_giocatore', 300 )->nullable();
            // $table->string( 'anno_di_ritiro_giocatore', 300 )->nullable();
            // $table->string( 'partite_giocate_internazionalmente', 300 )->nullable();
            // $table->string( 'competizione_partecipata', 300 )->nullable();
            // $table->string( 'numero_di_trofei_giocati', 300 )->nullable();
            // $table->string( 'sponsor_ufficiale_squadra', 300 )->nullable();
            // $table->string( 'valore_di_mercato_giocatore', 300 )->nullable();
            // $table->string( 'vittima', 300 )->nullable();
            // $table->string( 'anni_di_carcere', 300 )->nullable();
            // $table->string( 'giudizio', 300 )->nullable();
            // $table->string( 'indumento', 300 )->nullable();
            // $table->string( 'accesorio', 300 )->nullable();
            // $table->string( 'epoca',300 )->nullable();
            // $table->string( 'uomo',300 )->nullable();
            // $table->string( 'donna',300 )->nullable();
            // $table->string( 'animale',300 )->nullable();
            // $table->string( 'ingrediente',300 )->nullable();
            // $table->string( 'spezia',300 )->nullable();
            // $table->string( 'ricetta',300 )->nullable();
            // $table->string( 'durata',300 )->nullable();
            // $table->string( 'preparazione',300 )->nullable();
            // $table->string( 'difficoltà',300 )->nullable();
            // $table->string( 'quantità',300 )->nullable();
            // $table->string( 'coltura',300 )->nullable();
            // $table->string( 'professione',300 )->nullable();
            // $table->string( 'popolazione',300 )->nullable();
            // $table->string( 'superficie',300 )->nullable();
            // $table->string( 'biblioteca',300 )->nullable();
            // $table->string( 'argomento',300 )->nullable();
            // $table->string( 'autore',300 )->nullable();
            // $table->longText( 'descrizione',300 )->nullable();
            // $table->string( 'editore',300 )->nullable();
            // $table->string( 'privacy',300 )->nullable();
            // $table->string( 'mp3_file',300 )->nullable();
            // $table->longText( 'paragrafo',300 )->nullable();
            // $table->string( 'link',300 )->nullable();
            // $table->string( 'dipinto',300 )->nullable();
            // $table->string( 'scultura',300 )->nullable();
            // $table->string( 'movimento_artistico',300 )->nullable();
            // $table->string( 'data_periodo',300 )->nullable();
            // $table->string( 'libro',300 )->nullable();
            // $table->string( 'magazine',300 )->nullable();
            // $table->string( 'attore_attrice',300 )->nullable();
            // $table->string( 'film',300 )->nullable();
            // $table->string( 'regista',300 )->nullable();
            // $table->string( 'produttore',300 )->nullable();
            // $table->string( 'continente_di_nascita',300 )->nullable();
            // $table->string( 'nazione_di_nascita',300 )->nullable();
            // $table->string( 'titolo',300 )->nullable();
            // $table->string( 'isbn',300 )->nullable();
            // $table->string( 'archteipo',300 )->nullable();
            // $table->string( 'capitolo',300 )->nullable();
            // $table->string( 'coppa',300 )->nullable();
            // $table->string( 'giocatore',300 )->nullable();
            // $table->string( 'personaggio',300 )->nullable();
            // $table->longText( 'personaggi_coinvolti',300 )->nullable();
            // $table->string( 'reale_fittizio_mitologico' )->nullable();
            // $table->string( 'serie_tv', 300 )->nullable();
            // $table->string( 'videogame', 300 )->nullable();
            // $table->string( "titolo_dell_evento", 300 )->nullable();
            // $table->string( 'status_value', 300 )->nullable();
            // $table->decimal( 'single_price', 300 )->nullable();
            // $table->decimal( 'total_price', 300 )->nullable();
            // $table->string( 'affiliate_platforms', 300 )->nullable();
            // $table->string( 'affiliate_links', 300 )->nullable();
            // $table->decimal( 'affiliate_price', 300 )->nullable();

            // make the relation with audio_guide table
            $table->unsignedBigInteger( 'audio_guide_id' );
            $table->foreign( 'audio_guide_id' )->references( 'id' )->on( 'audio_guides' )->cascadeOnUpdate()->restrictOnDelete();

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
