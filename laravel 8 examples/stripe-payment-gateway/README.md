# Stripe payment gateway with laravel 8

[Originally published at - https://hackeradda.com](https://hackeradda.com/how-to-use-stripe-payment-gateway-with-laravel-8-0-6149b467f171d)

![Stripe payment](https://hackeradda.com/uploads/2021/09/hackeradda.com-6149b5d3db323.jpg)


Hello, Today I will discuss how to integrate the **Stripe payment gateway** with our laravel 8.0 project to start accepting payments. As we all know that the stripe is a globally known payment and accepting payment in almost all currencies and [countries](https://stripe.com/global). for more information please visit stripe's official website - [https://stripe.com](https://stripe.com)

#### Table of Contents

*   Create Laravel 8.0 project
*   Install stripe package for laravel
*   Setup Stripe Payment Gateway Keys
    *   Create Stripe account
    *   Get your Tokens
    *   Setup token in .env
*   Creating Routes
    *   routes/web.php
*   Creating Controller
    *   app/Http/Controllers/StripeCtrl.php
*   Creating the View
    *   resources/views/stripe.blade.php
*   Test card details
*   Summary

![Stripe payment countries](https://hackeradda.com/uploads/2021/09/hackeradda.com-61499686c0812.png)

So to integrate the **Stripe payment gateway** with laravel 8.0 we need a running laravel 8.0 project, so to create a new project we required a composer. If the composer is not installed please refer to the [link](https://getcomposer.org/download/ "Get Composer")

Create Laravel 8.0 project
--------------------------

To install laravel, we need to refer to its official documentation - [Laravel Installation](https://laravel.com/docs/8.x/installation "Laravel Installation")  or open your terminal and run the following command and this will create a fresh new laravel project in the chosen directory and you will see output on your terminal as screenshot attached

```bash
composer create-project --prefer-dist laravel/laravel stripe-payment-gateway "8.*"
```

![Install Laravel](https://hackeradda.com/uploads/2021/09/hackeradda.com-61499ae6ae911.png)

Install stripe package for laravel
----------------------------------

In this step we need to install the stripe-PHP package in the newly created project, to do that first we need to change our directory in the terminal by running cd stripe-payment-gateway/ and then need to run another command given below.

```bash
composer require stripe/stripe-php
```
![Stripe PHP](https://hackeradda.com/uploads/2021/09/hackeradda.com-61499c5a044a8.png)

Setup Stripe Payment Gateway Keys
---------------------------------

This step required setting up a stripe payment gateway, please follow the following steps to get your keys

* Create Stripe account
    

You can create your account by clicking on the following links (Log in if you already have an account) [Login](https://dashboard.stripe.com/login) or [Register](https://dashboard.stripe.com/register).

* Get your Tokens
    

You can get tokens by navigating to \`Developers -> API Keys\` from the sidebar of your dashboard.

* Setup token in .env
    

In this step, we need to add the following lines in the .env file 

```
STRIPE_KEY=pk_test_reFxwbsm9cdCKASdTfxAR
STRIPE_SECRET=sk_test_oQMFWteJiPd4wj4AtgApY
```
Creating Routes
---------------

Next, we need to create routes to interact with the application. to create routes we need to edit `routes/web.php` the file and need to add the following code.

### routes/web.php

```php
<?php
    
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\StripeCtrl;
    
    
    Route::get('/', function () {
        return redirect()->route('stripe');
    });
    
    Route::GET('stripe', [StripeCtrl::class, 'stripe'])->name('stripe');
    Route::POST('stripe', [StripeCtrl::class, 'stripePost'])->name('stripe.post');
```

Creating Controller
-------------------

As we have used StripeCtrl in our routes so, we need to create `StripeCtrl` in our application and copy and paste the following code in that file. to create a controller we need to run `php artisan make:controller StripeCtrl` and this will create `StripeCtrl.php` the file in `/app/Http/Controllers/` the directory. Next, we need to edit that file

### app/Http/Controllers/StripeCtrl.php

```php
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
```    

Creating the View
-----------------

To display and submit the payment request it is required to create a form from where we can generate the payment request.

### resources/views/stripe.blade.php

{% gist https://gist.github.com/harendra21/336ea8a40b716837b4033754af6b9577 %}

Now run your project by `php artisan serve` and open `localhost:8000` in your browser, you will see the following screen.

![Stripe Payment](https://hackeradda.com/uploads/2021/09/hackeradda.com-6149b0ee450f1.png)

Test card details
-----------------

You can use the below card number for test mode and for the expiry date you have entered any date greater than today’s date for CVV number use any 3 digit number it will work and in the zip code box, you have to enter your zip code.

Name on Card – Hackeradda

Card Number - 4242424242424242

CVC - 123

Expiration Month - 12

Expiration Year - 2030

[Click here to download the code](https://mega.nz/file/S3oGnbaZ#zJH92GcVVoF3OZpDdMxwHwXfitsaDk3cN07UbMIqbXE "Click here to download the code")

Summary
-------

So, We have successfully integrated the Stripe payment gateway in our laravel application with proper validation. It may help you if you are creating an application or learning stripe payment gateway with laravel. I also have [How to use Stripe Payments in Node.js?](https://hackeradda.com/how-to-use-stripe-payments-in-node-js-using-express) you can check it out.

Thank you for reading. Please feel free to ask any doubt.
