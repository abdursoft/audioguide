<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    private $pay;
    private $stripe;

    public function __construct()
    {
        $this->pay = new \Stripe\StripeClient(env("STRIPE_API_KEY"));
        $this->stripe = \Stripe\Stripe::setApiKey(env('STRIPE_API_KEY'));
    }


    public function customerCreate($name, $email){
        $customer = $this->pay->customers->create([
            'name' => $name,
            'email' => $email,
          ]);
          return $customer;
    }

    public function customerList(){
        return $this->pay->customers->all(['limit' => 3]);
    }

    public function customerRetrieve($customer){
        return $this->pay->customers->retrieve($customer, []);
    }

    public function customerDelete($customer){
        return $this->pay->customers->delete($customer, []);
    }

    public function productCreate($title, $description, $price)
    {
        $product = $this->pay->products->create([
            'name' => "$title $price",
            'description' => $description
        ]);
        return $product;
    }

    public function productPrice(string $id, int|float $price, string $currency,$interval='year')
    {
        $setPrice = $this->pay->prices->create(
            [
                'product' => $id,
                'unit_amount' => $price * 100,
                'currency' => $currency,
                'recurring' => ['interval' => $interval],
            ]
        );
        return $setPrice;
    }

    public function productRetrieve(string $id)
    {
        $retrieve = $this->pay->products->retrieve(
            $id,
            []
        );
        return $retrieve;
    }

    public function productDelete($product){
        $this->pay->products->delete($product, []);
    }

    public function subscriptionCreate(string $customer_id, array $items){
        return $this->pay->subscriptions->create([
            'customer' => $customer_id,
            'items' => [$items],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'], 
            'expand' => ['latest_invoice.payment_intent'], 
          ]);
    }


    public function payment(string $email,int|float $amount, string $currency, string $name,array $data,$trans_id)
    {
        $checkout_session = $this->pay->checkout->sessions->create([
            'success_url' => env("APP_URL")."/payment/stripe/success?trans=$trans_id",
            'cancel_url' => env("APP_URL")."/payment/stripe/cancel?trans=$trans_id",
            'customer_email' => $email,
            'submit_type' => 'pay',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $amount * 100,
                    'product_data' => [
                        'name' => $name,
                        'images' => ['https://i.imgur.com/EHyR2nP.png'],
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => $data,
            'mode' => 'payment',
            'billing_address_collection' => 'required'
        ]);
        return $checkout_session;
    }

    public function paymentRetrieve(string $paymentID){
        return $this->pay->checkout->sessions->retrieve(
            $paymentID,
            []
          );
    }

    public function paymentRetrieveAll(int $limit){
        return $this->pay->checkout->sessions->all(['limit' => $limit]);
    }

    public function refund(string $charge_id){
        return $this->pay->refunds->create(['charge' => $charge_id]);
    }

    public function refundUpdate(string $refund_id, array $data){
        return $this->pay->refunds->update(
            $refund_id,
            ['metadata' => $data]
          );
    }

    public function refundRetrieve(string $id){
        return $this->pay->refunds->retrieve($id, []);
    }


    public function refundRetrieveAll(int $limit){
        return $this->pay->refunds->all(['limit' => $limit]);
    }

    public function refundCancel(string $id){
        return $this->pay->refunds->cancel($id, []);
    }


    public function createPayout(int|float $amount, string $id, string $currency)
    {
        $payout = $this->pay->transfers->create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'destination' => $id,
            'transfer_group' => 'payout_from_'.env('SITE_NAME'),
        ]);
        return $payout;
    }
}
