<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe;

class StripeCtrl extends Controller
{
    //
     /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('stripe');
    }
  
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => 500 * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Test payment from hackeradda.com." 
        ]);
  
        Session::flash('success', 'Payment successful!');
          
        return back();
    }
}
