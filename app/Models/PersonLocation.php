<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonLocation extends Model
{
    protected $fillable = [
        'luoghi',
        'latitudine',
        'longitudine',
        'continente',
        'nazione',
        'città',
        'quartiere',
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
        'anno_di_nascita',
        'anno_di_morte',
        'continente_di_nascita',
        'nazione_di_nascita',
        'città_di_nascita',
        'anno_di_realizzazione',
        'titolo',
        'isbn',
        'archteipo',
        'argomento',
        'biblioteca',
        'capitolo',
        'luogo',
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
        'tag_title',
        'audio_guide_id',
        'image',
        'location_name'
    ];

    public function audioGuide(){
        return  $this->belongsTo(AudioGuide::class);
    }

    public function object(){
        return $this->hasMany(PersonObject::class);
    }
}
