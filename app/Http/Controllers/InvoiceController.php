<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\ProductCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $carts = ProductCart::where('user_id', $request->header('id'))->get();
        $total = ProductCart::where('user_id', $request->header('id'))->sum('price');

        DB::beginTransaction();
        $transID = uniqid();
        $invoice = Invoice::create([
            'total' => $total,
            'sub_total' => $total,
            'payable' => $total,
            'trans_id' => $transID,
            'val_id' => 0,
            'delivery_status' => 'pending',
            'payment_status' => 'pending',
            'profile_id'
        ]);

        foreach ($carts as $cart){
            InvoiceProduct::create([
                'quantity' => $cart->quantity,
                'sale_price' => $cart->price,
                'user_id' => $request->header('id'),
                'invoice_id' => $invoice->id,
                'audio_guide_id' => $cart->audio_guide_id
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
