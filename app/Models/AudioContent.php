<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioContent extends Model {
    use HasFactory;

    public function AudioGuide() {
        return $this->belongsTo( AudioGuide::class );
    }

    protected $fillable = [
        "materia",
        "argomento",
        "capitolo",
        "titolo",
        "short_description",
        "descrizione",
        "file_mp3",
        "dipinto",
        "artista",
        "movimento",
        "luogo",
        "città",
        "anno_di_realizzazione",
        "dimensione",
        "tecnica",
        "anno_di_nascita",
        "anno_di_morte",
        "nazione_di_nascita",
        "latitudine",
        "continente",
        "nazione",
        "quartiere",
        "categoria",
        "movimento_artistico",
        "paragrafo",
        "descrizione_breve",
        "film",
        "serie_tv",
        "libro",
        "autore",
        "editore",
        "link",
        "personaggio",
        "actor_ctress",
        "videogame",
        "produttore",
        "isbn",
        "magazine",
        "reale_fittizio_mitologico",
        "titolo_dell_evento",
        "data_periodo",
        "giocatore",
        "squadra",
        "campionato",
        "coppa",
        "biblioteca",
        "tag_title",
        "tag_description",
        "status_value",
        "single_price",
        "total_price",
        "audio_guide_id",
        "created_at",
        "free",
        "updated_at",
    ];

}
