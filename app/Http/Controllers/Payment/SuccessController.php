<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\UserGuide;
use Illuminate\Http\Request;

class SuccessController extends Controller
{
    /**
     * Stripe success controller
     */
    public function stripeSuccess(Request $request){
        if ( !empty( $request->query( 'trans' ) ) ) {
            $invoice = Invoice::where( 'trans_id', $request->query( 'trans' ) )->first();
            $product = InvoiceProduct::where('invoice_id',$invoice->id)->get();
            if ( !empty( $invoice ) ) {
                $stripe  = new StripeController();
                $payment = $stripe->paymentRetrieve( $invoice->payment_id );
                if($payment->payment_status === 'completed' || $payment->payment_status === 'paid'){
                    $invoice->update([
                        'payment_status' => $payment->payment_status
                    ]);
                    foreach($product as $guide){
                        UserGuide::create([
                            'payment_type' => 'fixed',
                            'user_id' => $guide->user_id,
                            'audio_guide_id' => $guide->audio_guide_id
                        ]);
                    }
                    return view('success');
                }
            }
        }
    }
}
