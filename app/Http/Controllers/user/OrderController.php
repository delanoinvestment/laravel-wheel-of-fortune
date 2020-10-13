<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use PDF;

use App\Models\Order;
use App\Models\Invoice;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Auth()->user()->orders;

        return view('user.order.index', compact([
            'orders'
        ]));
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
        try {
            DB::beginTransaction();

            $user = Auth()->user();
            $order = $user->orders()->create([
                'transaction_code' => Str::random(16),
                'seller_confirmation_expired_at' => Carbon::now()->addDays(Order::SELLER_CONFIRMATION_PERIOD_DAY),
                'shipping_address_id' => $user->info->defaultShippingAddress->id,
            ]);

            $shoppingCarts = $user->getWantedShoppingCarts();
            $grossPrice = 0;
            foreach ($shoppingCarts as $cart) {
                $totalPrice = $cart->count * $cart->product->price;
                $grossPrice += $totalPrice;
                $product = $cart->product;
                $orderItem = $order->items()->create([
                    'name' => $product->name,
                    'count' => $cart->count,
                    'unit_price' => $product->price,
                    'total_price' => $totalPrice,
                    'unit_point' => $product->point,
                    'total_point' => $cart->count * $product->point,
                    'product_id' => $product->id,
                ]);

                $images = $product->images;
                foreach ($images as $image) {
                    $orderItem->images()->create([
                        'src' => $image->src,
                        'collection_name' => $image->collection_name,
                        'title' => $image->title,
                        'description' => $image->description,
                    ]);
                }

                $cart->delete();
            }

            $order->invoice()->create([
                'code' => Str::random(8),
                'expired_at' => Carbon::now()->addDays(Invoice::DUE_PERIOD_DAY),
                'gross_price' => $grossPrice
            ]);

            $shoppingCarts = $user->shoppingCarts;
            foreach ($shoppingCarts as $cart) {
                $cart->delete();
            }

            DB::commit();
            return redirect()->route('user.order.show', ['code' => $order->transaction_code]);
        } catch (\Exception $error) {
            DB::rollBack();
            alert()->error('Something went wrong! Please try again.');
            return redirect()->back();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function show($transactionCode)
    {
        $order = Order::where('transaction_code', $transactionCode)->firstOrFail();
        $invoice = $order->invoice;
        $orderItems = $order->items;
        $shippingAddress = $order->shippingAddress;
        return view('user.order.show', compact([
            'order',
            'orderItems',
            'invoice',
            'shippingAddress'
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

    public function pdf($transactionCode)
    {
        $order = Order::where('transaction_code', $transactionCode)->firstOrFail();
        $invoice = $order->invoice;
        $orderItems = $order->items;
        $shippingAddress = $order->shippingAddress;
        $pdf = PDF::loadview('user.invoice.show', compact([
            'order',
            'orderItems',
            'invoice',
            'shippingAddress'
        ]));
        return $pdf->download('inv');
    }
}
