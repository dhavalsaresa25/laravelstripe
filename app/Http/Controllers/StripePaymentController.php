<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use App\Models\User;
use App\Models\UserCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\StripeClient;
use Config;
use Illuminate\Support\Facades\Validator;

class StripePaymentController extends Controller
{
    public function stripe()
    {
        return view('stripe');
    }

    public function stripePost(Request $request)
    {
        try {
            $rules = array(
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'email' => 'required|email',
                'name_on_card' => 'required',
                'card_number' => 'required',
                'CVC' => 'required',
                'expiration_month' => 'required',
                'expiration_year' => 'required',
            );

            $validatorMessages = array(
                'first_name.required'=> "First name required",
                'last_name.required'=> "Last name required",
                'email.required'=> "Email required",
                'name_on_card.required'=> "Name on card required",
                'card_number.required'=> "Card number required",
                'CVC.required'=> "CVC required",
                'expiration_month.required'=> "Expiration month required",
                'expiration_year.required'=> "Expiration year required",
            );

            $validator = Validator::make($request->all(), $rules, $validatorMessages);
            if ($validator->fails()) {
                $error=json_decode($validator->errors());
                return response()->json(['status' => 401,'error1' => $error]);
            }

            // Stripe flow start
            $key = \config('services.stripe.secret');
            $stripe = new StripeClient($key);

            //create customer
            $customer = $stripe->customers->create([
                'name' => $request->name_on_card,
                'email' => $request->email,
            ]);

            //Add card
            $token = $stripe->tokens->create([
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->expiration_month,
                    'exp_year' => $request->expiration_year,
                    'cvc' => $request->CVC,
                ],
            ]);

            //add card with customer
            $stripe->customers->createSource(
                $customer->id,
                ['source' => $token->id]
            );

            // Create site user
            $user = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'stripe_customer_id' => $customer->id,
                'stripe_card_id' => $token->id,
            );
            $userId = User::create($user);

            $card = array(
                'user_id' => $userId->id,
                'name_on_card' => $request->name_on_card,
                'card_number' => $request->card_number,
                'CVC' => $request->CVC,
                'expiration_month' => $request->expiration_month,
                'expiration_year' => $request->expiration_year,
            );
            $cardId = UserCard::create($card);

            //Cut Payment
            // $stripePayment = $stripe->charges->create([
            //     'amount' => $pay,
            //     'currency' => 'usd',
            //     'customer' => $customer->id,
            //     // 'payment_method' => $userCard->stripe_card_id,
            //     'description' => 'My First Test Charge',
            // ]);

            $pay = (number_format(10, 2, '.', '') * 100);
            $stripePayment = $stripe->paymentIntents->create(
                [
                    'amount' => $pay,
                    'currency' => 'usd',
                    'customer' => $customer->id,
                    'description' => 'Dynamic flow testing',
                ]
            );

            $payment = array(
                'user_id' => $userId->id,
                'order_id' => 'od-123',
                'refund_id' => $stripePayment->id,
            );
            Payments::create($payment);

            return response()->json(['payment successfully debited']);

        } catch (\Throwable $th) {
            dd($th);
        }
        // return response()->json(['status' => true, 'message' => $payment->id]);
    }
}
