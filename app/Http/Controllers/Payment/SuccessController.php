<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SuccessController extends Controller
{
    /**
     * Stripe success controller
     */
    public function stripeSuccess(Request $request){
        if ( !empty( $request->query( 'trans' ) ) ) {
            $invoice = Invoice::where( 'trans_id', $request->query( 'trans' ) )->first();
            if ( !empty( $invoice ) ) {
                $stripe  = new StripeController();
                $payment = $stripe->paymentRetrieve( $invoice->payment_id );
                if($payment->payment_status === 'completed' || $payment->payment_status === 'paid'){
                    $invoice->update([
                        'payment_status' => $payment->payment_status
                    ]);
                    return view('payment_cancel');
                }
            }
        }
    }
}
