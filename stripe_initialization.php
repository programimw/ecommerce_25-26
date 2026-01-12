<?php
require_once('vendor/autoload.php');

$private_key = "sk_test_51QdZaOIA6j8Agjdo5GJTLy0SpIDPNae1bPpzug7GTqcDIUlNR7CKK1ojTH9gGYRe0uEHUjbnpIDGSKukUVTONETg00wfXvfRpk";
$public_key = "pk_test_51QdZaOIA6j8AgjdoONN2YmHKTojcogE82ZcF8ntm0l1YwdZNUKNnlDgxb62vZ7IBVbS1NfyGQoRNxjWn6o0bvJxE00alHhiENc";
$stripe_account = "Test";
$businessName = "Test";
$company_name = "Test";

/**
 * Inicializimi i Stripe
 */
\Stripe\Stripe::setApiKey($private_key);
// \Stripe\Stripe::setMaxNetworkRetries(2);

$stripe = new \Stripe\StripeClient($private_key);