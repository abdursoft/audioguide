<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use DOMDocument;
use Illuminate\Support\Facades\Storage;

class Helper extends Controller {
    public static function descriptionSanitize( $description ) {
        try {
            $speech = null;
            $files  = '';
            $dom    = new DOMDocument();
            $dom->loadHTML( $description );
            $head = $dom->createElement( 'head' );
            $meta = $dom->createElement( 'meta' );
            $meta->setAttribute( 'http-equiv', 'Content-Type' );
            $meta->setAttribute( 'content', 'text/html; charset=utf-8' );
            $head->appendChild( $meta );
            $dom->appendChild($head);
            // $dom->removeChild($dom->getElementsByTagName('head'));

            $images = $dom->getElementsByTagName( 'img' );
            foreach ( $images as $key => $img ) {
                if ( strpos( $img->getAttribute( 'src' ), ';base64,' ) ) {
                    $data = base64_decode( explode( ',', explode( ';', $img->getAttribute( 'src' ) )[1] )[1] );
                    $name = Storage::disk( 'public' )->put( 'notes/files', $data );
                    $files .= $name . ",";
                    file_put_contents( $name, $data );
                    $img->removeAttribute( 'src' );
                    $img->setAttribute( 'src', "/" . $name );
                }
                $img->removeAttribute( 'style' );
            }

            $speech = $dom->saveHTML();
            $speech = str_replace('</html>','',$speech);
            $speech = $speech."</html>";
            
            $files  = trim( $files, ',' );
            return [
                "description" => $speech,
                "files"       => $files,
            ];
        } catch ( \Throwable $th ) {
            return false;
        }
    }
}
