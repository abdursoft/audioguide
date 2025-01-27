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
            $dom->appendChild( $head );
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
            $speech = str_replace( '</html>', '', $speech );
            $speech = $speech . "</html>";

            $files = trim( $files, ',' );
            return [
                "description" => $speech,
                "files"       => $files,
            ];
        } catch ( \Throwable $th ) {
            return false;
        }
    }

    /**
     * Google event handler
     */
    public static function ga4( string $event, array $params, string $measurement_id, string $api_secret, string $client_id ) {
        $url            = "https://www.google-analytics.com/mp/collect?measurement_id=" . $measurement_id . "&api_secret=" . $api_secret;

        $data = array(
            'client_id' => $client_id,
            'events'    => array(
                array(
                    'name'   => $event,
                    'params' => $params,
                ),
            ),
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json",
                'method'  => 'POST',
                'content' => json_encode( $data ),
            ),
        );
        $context = stream_context_create( $options );
        $resp    = file_get_contents( $url, false, $context );
        return $resp;
    }

    /**
     * Return the price according coupon
     */
    public static function couponPrice( $coupon, $limit, $price ) {
        if ( $coupon->status == 'active' && $coupon->min_purchase <= $price ) {
            if ( strtotime( $coupon->expired_at ) > time() && time() > strtotime( $coupon->started_at ) ) {
                if ( $coupon->limitation == 0 ) {
                    if ( $coupon->coupon_type === 'percent' ) {
                        $price = $price - ( ( $price * $coupon->amount ) / 100 );
                    } else {
                        $price = $price - $coupon->amount;
                    }
                } elseif ( $coupon->limitation > 0 && $coupon->limitation > $limit ) {
                    if ( $coupon->coupon_type === 'percent' ) {
                        $price = $price - ( ( $price * $coupon->amount ) / 100 );
                    } else {
                        $price = $price - $coupon->amount;
                    }
                } else {
                    $price = $price;
                }
            }
            return $price;
        } else {
            return $price;
        }
    }
}
