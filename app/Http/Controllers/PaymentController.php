<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    //
    
      
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request): RedirectResponse
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    // Create a customer
    $customer = Stripe\Customer::create([
        'email' => $request->email, // assuming email is provided in the request
        'source' => $request->stripeToken,
    ]);


    return back()->with('success', 'Payment successful!');
        Stripe\Charge::create ([
                "amount" => 10 * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Test payment from itsolutionstuff.com." 
        ]);
                
        return back()
                ->with('success', 'Payment successful!');
    }   

}
