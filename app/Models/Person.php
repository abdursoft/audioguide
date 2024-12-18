<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'nome_e_cognome',
        'anno_di_nascita',
        'anno_di_morte',
        'citta_di_nascita',
        'citta_di_morte',
        'nazione_di_nascita',
        'continente_di_nascita',
        'categoria',
        'italiano',
        'tag_title',
        'tag_description',
        'file_mp3',
        'free',
        'privacy',
        'status_value',
        'single_price',
        'total_price',
        'affiliate_platforms',
        'affiliate_links',
        'affiliate_price',
        'paragrafo',
        'link',
        'artista',
        'dipinto',
        'scultura',
        'dimensione',
        'tecnica',
        'movimento',
        'museo',
        'movimento_artistico',
        'data_periodo',
        'autore',
        'libro',
        'magazine',
        'attore_attrice',
        'film',
        'regista',
        'produttore',
        'anno_di_realizzazione',
        'titolo',
        'isbn',
        'archteipo',
        'argomento',
        'biblioteca',
        'capitolo',
        'continente',
        'nazione',
        'citta',
        'quartiere',
        'luogo',
        'latitudine',
        'longitudine',
        'campionato',
        'coppa',
        'squadra',
        'giocatore',
        'personaggio',
        'personaggi_coinvolti',
        'reale_fittizio_mitologico',
        'serie_tv',
        'videogame',
        'titolo_dell_evento',
        'audio_guide_id',
        'image',
        'person_name',
    ];

    public function audioGuide(){
        return  $this->belongsTo(AudioGuide::class);
    }

    public function object(){
        return $this->hasMany(PersonObject::class);
    }
}
