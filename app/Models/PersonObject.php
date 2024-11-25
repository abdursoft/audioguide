<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonObject extends Model
{
    protected $fillable = [
        'titolo',
        'italiano',
        'categoria',
        'tag_title',
        'tag_description',
        'file_mp3',
        'persone',
        'eventi',
        'luoghi',
        'citta',
        'anno_di_realizzazione',
        'dimensione',
        'tecnica',
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
        'anno_di_nascita',
        'anno_di_morte',
        'continente_di_nascita',
        'nazione_di_nascita',
        'citta_di_nascita',
        'isbn',
        'archteipo',
        'argomento',
        'biblioteca',
        'capitolo',
        'continente',
        'nazione',
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
        'image',
        'audio_guide_id',
        'person_id',
        'event_id',
        'location_id'
    ];

    public function audioGuide(){
        return  $this->belongsTo(AudioGuide::class);
    }

    public function person(){
        return $this->belongsTo(Person::class);
    }

    public function personEvent(){
        return $this->belongsTo(PersonEvent::class);
    }

    public function personLocation(){
        return $this->belongsTo(PersonLocation::class);
    }
}
