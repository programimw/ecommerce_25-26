<?php
require_once('vendor/autoload.php');

$private_key = "";
$public_key = "";
$stripe_account = "Test";
$businessName = "Test";
$company_name = "Test";

/**
 * Inicializimi i Stripe
 */
\Stripe\Stripe::setApiKey($private_key);
// \Stripe\Stripe::setMaxNetworkRetries(2);

$stripe = new \Stripe\StripeClient($private_key);