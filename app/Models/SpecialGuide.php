<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialGuide extends Model {
    use HasFactory;

    protected $fillable = ['continente', 'nazione', 'città', 'quartiere', 'museo', 'latitudine', 'longitudine', 'categoria', 'tag_title', 'tag_description', 'file_mp3', 'artista', 'movimento', 'luogo', 'anno_di_realizzazione', 'dimensione', 'tecnica', 'anno_di_nascita', 'anno_di_morte', 'città_di_nascita', 'città_di_morte', 'calciatore', 'ruolo', 'squadra', 'nazionalità', 'numero_di_maglia', 'stadio', 'anno_di_fondazione_squadra', 'palmarès_squadra', 'campionato', 'gol_segnati', 'assist', 'presenze', 'minuti_giocati', 'allenatore', 'modulo_di_gioco', 'anno_di_inizio_carriera_giocatore', 'anno_di_ritiro_giocatore', 'partite_giocate_internazionalmente', 'competizione_partecipata', 'numero_di_trofei_giocati', 'sponsor_ufficiale_squadra', 'valore_di_mercato_giocatore', 'vittima', 'anni_di_carcere', 'giudizio', 'indumento', 'accesorio', 'epoca', 'uomo', 'donna', 'animale', 'ingrediente', 'spezia', 'ricetta', 'durata', 'preparazione', 'difficoltà', 'quantità', 'coltura', 'professione', 'popolazione', 'superficie', 'biblioteca', 'argomento', 'autore', 'descrizione', 'editore', 'privacy', 'mp3_file', 'paragrafo', 'link', 'dipinto', 'scultura', 'movimento_artistico', 'data/periodo', 'libro', 'magazine', 'attore/attrice', 'film', 'regista', 'produttore', 'continente_di_nascita', 'nazione_di_nascita', 'titolo', 'isbn', 'archteipo', 'capitolo', 'coppa', 'giocatore', 'personaggio', 'personaggi_coinvolti', 'reale_fittizio_mitologico', 'serie_tv', 'videogame', "titolo_dell'evento", 'tag_title', 'status_value', 'single_price', 'total_price', 'affiliate_platforms', 'affiliate_links', 'affiliate_price']
    ;
}
