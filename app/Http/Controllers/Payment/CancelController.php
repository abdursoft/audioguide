<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CancelController extends Controller {
    /**
     * Stripe cancel controller
     */
    public function stripeCancel( Request $request ) {
        if ( !empty( $request->query( 'trans' ) ) ) {
            $invoice = Invoice::where( 'trans_id', $request->query( 'trans' ) )->first();
            if ( !empty( $invoice ) ) {
                $stripe  = new StripeController();
                $payment = $stripe->paymentRetrieve( $invoice->payment_id );
                if($payment->payment_status === 'unpaid' || $payment->payment_status === 'cancel'){
                    return view('payment_cancel');
                }
            }
        }
    }
}
