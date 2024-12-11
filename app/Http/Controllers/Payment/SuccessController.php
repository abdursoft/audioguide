<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\ProductCart;
use App\Models\PurchaseMail;
use App\Models\Subscription;
use App\Models\UserGuide;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuccessController extends Controller
{
    /**
     * Stripe success controller
     */
    public function stripeSuccess(Request $request)
    {
        if (!empty($request->query('trans'))) {
            $invoice = Invoice::where('trans_id', $request->query('trans'))->first();
            $product = InvoiceProduct::where('invoice_id', $invoice->id)->get();

            if (!empty($invoice)) {
                $stripe = new StripeController();
                $payment = $stripe->paymentRetrieve($invoice->payment_id);
                if ($payment->payment_status === 'completed' || $payment->payment_status === 'paid') {
                    try {
                        DB::beginTransaction();
                        $invoice->update([
                            'payment_status' => $payment->payment_status,
                        ]);

                        UserSubscription::create([
                            'payment_id' => $payment->id,
                            'paid_amount' => ($payment->amount ?? $payment->amount_total) / 100,
                            'currency' => $payment->currency,
                            'user_id' => $invoice->user_id,
                            'status' => $payment->payment_status,
                            'started_at' => date('Y-m-d'),
                            'ended_at' => date('Y-m-d', strtotime("+1 year")),
                            'invoice_url' => $payment->url,
                            'guide_type' => $invoice->type === 'onetime' ? 'single' : 'all',
                            'guide_id' => $invoice->type === 'onetime' ? $product[0]['audio_guide_id'] : 'all',
                            'type' => $invoice->type,
                            'stripe_subscription_id' => $payment->id,
                        ]);

                        if(!empty($product)){
                            foreach ($product as $guide) {
                                UserGuide::create([
                                    'payment_type' => 'fixed',
                                    'user_id' => $guide->user_id,
                                    'audio_guide_id' => $guide->audio_guide_id,
                                ]);
                            }
                            ProductCart::where('user_id', $invoice->user_id)->delete();
                        }
                        PurchaseMail::updateOrCreate([
                            'user_id' => $invoice->user_id
                        ],[
                            'user_id' => $invoice->user_id,
                            'mail' => 0
                        ]);
                        DB::commit();
                        return redirect()->away(env('FRONT_END').'user/cart?status=success');
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => $th->getMessage()
                        ], 400);
                    }
                    return view('success');
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => "Payment unsuccessful"
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Invoice not found"
                ], 400);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => "Invalid transaction id"
            ], 400);
        }
    }
}
