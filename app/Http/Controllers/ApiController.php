<?php

namespace App\Http\Controllers;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\StripeClient;

class ApiController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');

    }
    public function StripeCustomer(Request $request)
    {
        $stripe = new StripeClient('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');

        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone' => 'required|string',
            'description' => 'required|string',
            'email' => 'required|string',
        ]);
        $customer = $stripe->customers->create([
            'name' => $request['name'],
            'phone' => $request['phone'],
            'description' => $request['description'],
            'email' => $request['email'],
        ]);
        $user = User::where(['email' => $request['email']])->first();
        $user->update(['stripe_customer_id' => $customer->id]);

        if(!$customer){
            return response(array('status' => false, 'message' => 'Failed to create customer'));
        }else {
            return response(array('status' => true, 'message' => 'Customer created successfully', 'data' => $customer));
        }

    }
    public function StripeIntent(Request $request){
        $stripe = new StripeClient('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');
        $validate = Validator::make($request->all(), [
            'amount' => 'required|integer',
            'currency' => 'required|string',
        ]);
        $intents = $stripe->paymentIntents->create([
            'amount' => $request['amount'],
            'currency' => $request['currency'],
            'payment_method_types' => ['card']
        ]);
        dd($intents);
        if(!$intents){
            return response(array('status' => false, 'message' => 'Failed to Create Intent'));
        }else {
            return response(array('status' => true, 'message' => 'Intent Created Successfully', 'data' => $intents));
        }
    }

    public function StripePaymentMethod(Request $request)
    {
        $stripe = new StripeClient('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');

        $validate = Validator::make($request->all(), [
            'number' => 'required|integer',
            'exp_month' => 'required|integer',
            'exp_year' => 'required|integer',
            'cvc' => 'required|integer'
        ]);

       $pay =  $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => $request['number'],
                'exp_month' => $request['exp_month'],
                'exp_year' => $request['exp_year'],
                'cvc' => $request['cvc'],
            ],
        ]);
        if(!$pay){
            return response(array('status' => false, 'message' => 'Payment Successful'));
        }
        return response(array('status' => true, 'message' => 'Failed To Make Payment', 'data' => $pay));

    }

    public function StripeAttachCard(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'cutomer' => 'required|string',
            'payment_method' => 'required|string'
        ]);
        $stripe = new StripeClient('sk_test_51L3a7TSCROyfBwpuE7VyPyoayRcKLnOeYjlQ4LNSkmzNsz9YpvpEr0VtNCxq7MfALLx0Tt3oNXpLHflcTojkrNff00RVs2eI85');
        $check =   $stripe->paymentMethods->attach($request['payment_method'],
            ['customer' => $request['customer']]
        );
        if(!$check){
            return response(array('status' => false, 'message' => 'Attached Successful'));
        }
        return response(array('status' => true, 'message' => 'Failed To Attach', 'data' => $check));

    }
}
