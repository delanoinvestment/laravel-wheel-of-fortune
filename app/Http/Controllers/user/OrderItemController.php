<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;

use Midtrans\Config;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $transactionCode, $id
     * @return \Illuminate\Http\Response
     */
    public function show($transactionCode, $id)
    {
        $transaction = InvoiceTransaction::where('code', $code)->firstOrFail();
        $invoice = $transaction->invoice;
        $orders = $invoice->orders;
        $address = $transaction->shippingAddress;
        // dd($orders);
        // dd($invoice->MidtransTransactionDetail($orders, $invoice));
        $transaction_details = $invoice->MidtransTransactionDetail($orders, $invoice);
        $expiry = $invoice->MidtransExpiredAt($invoice);
        $customer_details = Auth()->user()->getMidtransCustomerDetails();
        // dd("terst");
        // dd($expiry);
        // dd($customer_details);


        // Set your Merchant Server Key
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;

        Config::$appendNotifUrl = "http://d6729a79bf87.ngrok.io/midtrans";
        Config::$overrideNotifUrl = "http://d6729a79bf87.ngrok.io/midtrans";

        $params = array(
            'transaction_details' => $transaction_details,
            'expiry' => $expiry,
            "customer_details" => $customer_details,
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return view('user.invoice.transaction.show', compact([
            'invoice',
            'transaction',
            'orders',
            'address',
            'snapToken',
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
