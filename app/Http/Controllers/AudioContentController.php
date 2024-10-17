<?php

namespace App\Http\Controllers;

use App\Models\AudioContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AudioContentController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio content successfully retrieved',
            'data'    => AudioContent::paginate(
                $perPage = 10,
                $column = ['*'],
                $pageName = 'page'
            ),
        ], 200 );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create( Request $request ) {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Request $request ) {
        $validate = Validator::make( $request->all(), [
            'audio_guide_id ' => 'required|exists:audio_guides,id',
        ] );

        if ( $validate->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio content couldn\'t create',
                'errors'  => $validate->errors(),
            ], 400 );
        }

        try {
            AudioContent::create( [
                "materia"                   => $request->input( "materia" ) ?? Null,
                "argomento"                 => $request->input( "argomento" ) ?? Null,
                "capitolo"                  => $request->input( "capitolo" ) ?? Null,
                "titolo"                    => $request->input( "titolo" ) ?? Null,
                "short_description"         => $request->input( "short_description" ) ?? Null,
                "descrizione"               => $request->input( "descrizione" ) ?? Null,
                "file_mp3"                  => $request->input( "file_mp3" ) ?? Null,
                "dipinto"                   => $request->input( "dipinto" ) ?? Null,
                "artista"                   => $request->input( "artista" ) ?? Null,
                "movimento"                 => $request->input( "movimento" ) ?? Null,
                "luogo"                     => $request->input( "luogo" ) ?? Null,
                "città"                     => $request->input( "città" ) ?? Null,
                "anno_di_realizzazione"     => $request->input( "anno_di_realizzazione" ) ?? Null,
                "dimensione"                => $request->input( "dimensione" ) ?? Null,
                "tecnica"                   => $request->input( "tecnica" ) ?? Null,
                "anno_di_nascita"           => $request->input( "anno_di_nascita" ) ?? Null,
                "anno_di_morte"             => $request->input( "anno_di_morte" ) ?? Null,
                "nazione_di_nascita"        => $request->input( "nazione_di_nascita" ) ?? Null,
                "latitudine"                => $request->input( "latitudine" ) ?? Null,
                "continente"                => $request->input( "continente" ) ?? Null,
                "nazione "                  => $request->input( "nazione" ) ?? Null,
                "quartiere"                 => $request->input( "quartiere" ) ?? Null,
                "categoria"                 => $request->input( "categoria" ) ?? Null,
                "movimento_artistico"       => $request->input( "movimento_artistico" ) ?? Null,
                "paragrafo"                 => $request->input( "paragrafo" ) ?? Null,
                "descrizione_breve"         => $request->input( "descrizione_breve" ) ?? Null,
                "film"                      => $request->input( "film" ) ?? Null,
                "serie_tv"                  => $request->input( "serie_tv" ) ?? Null,
                "libro"                     => $request->input( "libro" ) ?? Null,
                "autore"                    => $request->input( "autore" ) ?? Null,
                "editore"                   => $request->input( "editore" ) ?? Null,
                "link"                      => $request->input( "link" ) ?? Null,
                "personaggio"               => $request->input( "personaggio" ) ?? Null,
                "actor_ctress"              => $request->input( "actor_ctress" ) ?? Null,
                "videogame"                 => $request->input( "videogame" ) ?? Null,
                "produttore"                => $request->input( "produttore" ) ?? Null,
                "isbn"                      => $request->input( "isbn" ) ?? Null,
                "magazine"                  => $request->input( "magazine" ) ?? Null,
                "reale_fittizio_mitologico" => $request->input( "reale_fittizio_mitologico" ) ?? Null,
                "titolo_dell_evento "       => $request->input( "titolo_dell_evento" ) ?? Null,
                "data_periodo "             => $request->input( "data_periodo" ) ?? Null,
                "giocatore"                 => $request->input( "giocatore" ) ?? Null,
                "squadra"                   => $request->input( "squadra" ) ?? Null,
                "campionato"                => $request->input( "campionato" ) ?? Null,
                "coppa"                     => $request->input( "coppa" ) ?? Null,
                "biblioteca"                => $request->input( "biblioteca" ) ?? Null,
                "tag_title"                 => $request->input( "tag_title" ) ?? Null,
                "tag_description"           => $request->input( "tag_description" ) ?? Null,
                "status_value"              => $request->input( "status_value" ) ?? Null,
                "single_price"              => $request->input( "single_price" ) ?? Null,
                "audio_guide_id"            => $request->input( "audio_guide_id" ) ?? Null,
            ] );

            return response()->json([
                'status'=> true,
                'message' => 'Audio content successfully saved'
            ],201);
        } catch ( \Throwable $th ) {
            return response()->json([
                'status'=> false,
                'message' => 'Audio content couldn\'t save',
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( AudioContent $audioContent ) {
        return response()->json( [
            'status'  => true,
            'message' => 'Audio content successfully retrieved',
            'data'    => $audioContent,
        ], 200 );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( AudioContent $audioContent ) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, AudioContent $audioContent ) {
        $validate = Validator::make( $request->all(), [
            'audio_guide_id ' => 'required|exists:audio_guides,id',
        ] );

        if ( $validate->fails() ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio content couldn\'t update',
                'errors'  => $validate->errors(),
            ], 400 );
        }

        try {
           $audioContent->update( [
                "materia"                   => $request->input( "materia" ) ?? Null,
                "argomento"                 => $request->input( "argomento" ) ?? Null,
                "capitolo"                  => $request->input( "capitolo" ) ?? Null,
                "titolo"                    => $request->input( "titolo" ) ?? Null,
                "short_description"         => $request->input( "short_description" ) ?? Null,
                "descrizione"               => $request->input( "descrizione" ) ?? Null,
                "file_mp3"                  => $request->input( "file_mp3" ) ?? Null,
                "dipinto"                   => $request->input( "dipinto" ) ?? Null,
                "artista"                   => $request->input( "artista" ) ?? Null,
                "movimento"                 => $request->input( "movimento" ) ?? Null,
                "luogo"                     => $request->input( "luogo" ) ?? Null,
                "città"                     => $request->input( "città" ) ?? Null,
                "anno_di_realizzazione"     => $request->input( "anno_di_realizzazione" ) ?? Null,
                "dimensione"                => $request->input( "dimensione" ) ?? Null,
                "tecnica"                   => $request->input( "tecnica" ) ?? Null,
                "anno_di_nascita"           => $request->input( "anno_di_nascita" ) ?? Null,
                "anno_di_morte"             => $request->input( "anno_di_morte" ) ?? Null,
                "nazione_di_nascita"        => $request->input( "nazione_di_nascita" ) ?? Null,
                "latitudine"                => $request->input( "latitudine" ) ?? Null,
                "continente"                => $request->input( "continente" ) ?? Null,
                "nazione "                  => $request->input( "nazione" ) ?? Null,
                "quartiere"                 => $request->input( "quartiere" ) ?? Null,
                "categoria"                 => $request->input( "categoria" ) ?? Null,
                "movimento_artistico"       => $request->input( "movimento_artistico" ) ?? Null,
                "paragrafo"                 => $request->input( "paragrafo" ) ?? Null,
                "descrizione_breve"         => $request->input( "descrizione_breve" ) ?? Null,
                "film"                      => $request->input( "film" ) ?? Null,
                "serie_tv"                  => $request->input( "serie_tv" ) ?? Null,
                "libro"                     => $request->input( "libro" ) ?? Null,
                "autore"                    => $request->input( "autore" ) ?? Null,
                "editore"                   => $request->input( "editore" ) ?? Null,
                "link"                      => $request->input( "link" ) ?? Null,
                "personaggio"               => $request->input( "personaggio" ) ?? Null,
                "actor_ctress"              => $request->input( "actor_ctress" ) ?? Null,
                "videogame"                 => $request->input( "videogame" ) ?? Null,
                "produttore"                => $request->input( "produttore" ) ?? Null,
                "isbn"                      => $request->input( "isbn" ) ?? Null,
                "magazine"                  => $request->input( "magazine" ) ?? Null,
                "reale_fittizio_mitologico" => $request->input( "reale_fittizio_mitologico" ) ?? Null,
                "titolo_dell_evento "       => $request->input( "titolo_dell_evento" ) ?? Null,
                "data_periodo "             => $request->input( "data_periodo" ) ?? Null,
                "giocatore"                 => $request->input( "giocatore" ) ?? Null,
                "squadra"                   => $request->input( "squadra" ) ?? Null,
                "campionato"                => $request->input( "campionato" ) ?? Null,
                "coppa"                     => $request->input( "coppa" ) ?? Null,
                "biblioteca"                => $request->input( "biblioteca" ) ?? Null,
                "tag_title"                 => $request->input( "tag_title" ) ?? Null,
                "tag_description"           => $request->input( "tag_description" ) ?? Null,
                "status_value"              => $request->input( "status_value" ) ?? Null,
                "single_price"              => $request->input( "single_price" ) ?? Null,
                "audio_guide_id"            => $request->input( "audio_guide_id" ) ?? Null,
            ] );

            return response()->json([
                'status'=> true,
                'message' => 'Audio content successfully updated'
            ],200);
        } catch ( \Throwable $th ) {
            return response()->json([
                'status'=> false,
                'message' => 'Audio content couldn\'t update',
                'errors' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( AudioContent $audioContent ) {
        try {
            $audioContent->delete();
            return response()->json( [
                'status'  => true,
                'message' => 'Audio content successfully removed',
            ], 200 );
        } catch ( \Throwable $th ) {
            return response()->json( [
                'status'  => false,
                'message' => 'Audio content couldn\'t remove',
                'errors'  => $th->getMessage(),
            ], 400 );
        }
    }

    /**
     * Save audio content
     */
    public function saveContent( $data ) {
        try {
            AudioContent::create( $data );
            return true;
        } catch ( \Throwable $th ) {
            return false;
        }
    }

    /**
     * Single audio content
     */
    public function singleContent($id){
        return response()->json( [
            'status'  => true,
            'message' => 'Audio content successfully retrieved',
            'data'    => AudioContent::find($id),
        ], 200 );
    }
}

// materia, argomento, capitolo, titolo, short_description, descrizione, file_mp3, dipinto, artista, movimento, luogo, città, anno_di_realizzazione, dimensione, tecnica, anno_di_nascita, anno_di_morte, nazione_di_nascita, latitudine, continente, nazione, quartiere, categoria, movimento_artistico, paragrafo, descrizione_breve, film, serie_tv, libro, autore, editore, link, personaggio, actor_ctress, videogame, produttore, isbn, magazine, reale_fittizio_mitologico, titolo_dell_evento, data_periodo, giocatore, squadra, campionato, coppa, biblioteca, tag_title, tag_description, status_value, single_price, audio_guide_id