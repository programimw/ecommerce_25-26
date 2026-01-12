<?php
error_reporting(0);
session_start();
require_once "connect.php";
require_once "functions.php";
require_once "stripe_initialization.php";


if ($_POST['action'] == "setup_intents") {

    try {
        $intent = \Stripe\SetupIntent::create();
        echo json_encode(array("status" => 200, "message" => "Setup Intent created successfully", "setup_intent" => $intent));
        exit;
    } catch (Exception $e) {
        echo json_encode(array("status" => 404, "message" => "Setup Intent not created " . $e, "setup_intent" => null));
        exit;
    }
}
elseif ($_POST['action'] == "create_customer_and_pay") {

    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $cardholder_name = trim(mysqli_real_escape_string($conn, $_POST['cardholder_name']));
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $user_id = $_SESSION['id'];
    $amount = 300;
    $ip = getUserIp();

    try {

        /**
         * Krijojme ne stripe fillimisht nje customer me kete payment method
         */
        $customer = \Stripe\Customer::create([
            'payment_method' => $payment_method,
            'description' => "This is a payment method for " . $cardholder_name,
            'email' => $_SESSION['email'],
            'name' => $cardholder_name,
            "preferred_locales" => array("en"),
            'metadata' => [
                'product_id' => $product_id,
                'user_id' => $user_id,
                'name' => $_SESSION['name'],
                "ip" => $ip,
                "created_at" => date("Y-m-d H:i:s")
            ],
        ]);

        $customer_id = $customer->id;

        /**
         * Ruajme Stripe Logs krijimin e Costumer
         */
        $action = $_POST['action'] . "=> new_customer";
        $data = json_encode($customer);
        include "logs.php";
        /**
         * Pasi krijohet customer me nje payment method, marrim detajet per kete payment method
         */
        $payment_details = \Stripe\PaymentMethod::Retrieve($payment_method);

        /**
         * Shtimi i te dhenave ne databaze
         */
        $query_save_card = "INSERT INTO stripe_cards
                                         SET user_id = '" . mysqli_real_escape_string($conn, $user_id) . "',
                                             customer_id = '" . mysqli_real_escape_string($conn, $customer_id) . "', 
                                             cardholder_name = '" . mysqli_real_escape_string($conn, $cardholder_name) . "', 
                                             active = 'Yes',
                                             payment_method = '" . mysqli_real_escape_string($conn, $payment_method) . "', 
                                             card_country = '" . mysqli_real_escape_string($conn, $payment_details['card']->country) . "', 
                                             card_brand = '" . mysqli_real_escape_string($conn, $payment_details['card']->brand) . "', 
                                             card_last4 = '" . mysqli_real_escape_string($conn, $payment_details['card']->last4) . "', 
                                             card_exp_month = '" . mysqli_real_escape_string($conn, $payment_details['card']->exp_month) . "', 
                                             card_exp_year = '" . mysqli_real_escape_string($conn, $payment_details['card']->exp_year) . "', 
                                             default_card = 'Yes',
                                             testing = 'Yes', 
                                             ip = '" . mysqli_real_escape_string($conn, $ip) . "', 
                                             created_at = '" . date("Y-m-d H:i:s") . "',
                                             updated_at = '" . date("Y-m-d H:i:s") . "'
                                         ";

        $result_save_card = mysqli_query($conn, $query_save_card);

        if (!$result_save_card) {
            echo json_encode(array("status" => 404, "message" => "Internal Server Error." . __LINE__));
            exit;
        }

        /**
         * Ruajme Stripe Logs krijimin e Kartes
         */
        $action = $_POST['action'] . "=> new_card";
        $data = json_encode($query_save_card);
        include "logs.php";

        /**
         * Realizi i pageses ne stripe
         */
        $payment_value = $amount * 100;
        $currency = "EUR";
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $payment_value,
            'currency' => $currency,
            'payment_method_types' => ['card'],
            'customer' => $customer_id,
            'payment_method' => $payment_method,
            'off_session' => true,
            'confirm' => true,
            'metadata' => [
                'product_id' => $product_id,
                'user_id' => $user_id,
                'name' => $_SESSION['name'],
                "ip" => $ip,
                "created_at" => date("Y-m-d H:i:s")
            ],
            'expand' => ['latest_charge.balance_transaction'],
        ]);

        /**
         * Ruajme Stripe Logs per intent
         */
        $action = $_POST['action'] . "=> payment_intent";
        $data = json_encode($intent);
        include "logs.php";
        /**
         * Marrja e detajeve te pageses per te kuptuar sa ka qene fee per kete pagese
         */
         $transaction_details = $intent->latest_charge->balance_transaction;

        /**
         * Ruajme Stripe Logs per intent
         */
        $action = $_POST['action'] . "_transaction_details";
        $data = json_encode($transaction_details);
        include "logs.php";

        /**
         * Ruatja e detajeve te transaksionit transaksionit
         */
        $save_amount = round($intent->amount / 100, 2);
        $payment_fee = round($transaction_details->fee / 100, 2);
        $payment_net = round($transaction_details->net / 100, 2);
        $converted_amount = round($transaction_details->amount / 100, 2);
        $converted_currency = $transaction_details->currency;
        $balance_description = $transaction_details->description;
        $exchange_rate = $transaction_details->exchange_rate;
        $available_on = date("Y-m-d H:i:s", $transaction_details->available_on);

        $query_insert_transaction = "INSERT INTO transactions_details
                                     SET product_id = '" . mysqli_real_escape_string($conn, $product_id) . "',
                                         user_id = '" . mysqli_real_escape_string($conn, $user_id) . "',
                                         customer_id = '" . mysqli_real_escape_string($conn, $customer_id) . "',
                                         payment_method = '" . mysqli_real_escape_string($conn, $payment_method) . "',
                                         payment_intent_id = '" . mysqli_real_escape_string($conn, $intent->id) . "',
                                         transaction_id = '" . mysqli_real_escape_string($conn, $intent->latest_charge->balance_transaction->id) . "',
                                         charge_id = '" . mysqli_real_escape_string($conn, $intent->latest_charge->id) . "',
                                         amount = '" . mysqli_real_escape_string($conn, $save_amount) . "',
                                         currency = '" . mysqli_real_escape_string($conn, $currency) . "',
                                         converted_amount = '" . mysqli_real_escape_string($conn, $converted_amount) . "',
										 converted_currency = '" . mysqli_real_escape_string($conn, $converted_currency) . "',
										 balance_description = '" . mysqli_real_escape_string($conn, $balance_description) . "',
										 exchange_rate = '" . mysqli_real_escape_string($conn, $exchange_rate) . "',
										 available_on = '" . mysqli_real_escape_string($conn, $available_on) . "',
                                         payment_fee = '" . mysqli_real_escape_string($conn, $payment_fee) . "',
                                         payment_net = '" . mysqli_real_escape_string($conn, $payment_net) . "',
                                         status = 'Success',
                                         created_at = '" . date("Y-m-d H:i:s") . "'
                                      ";

        $result_insert_transaction = mysqli_query($conn, $query_insert_transaction);
        if (!$result_insert_transaction) {

            echo json_encode(array("status" => 404, "message" => "Internal Server Error." . __LINE__));
            exit;
        }

        /**
         * Ruajme Stripe Logs per krijimin e transaksionit
         */
        $action = $_POST['action'] . "_transactions";
        $data = json_encode($query_insert_transaction);
        include "logs.php";


        /**
         * Ruatja e te dhenave te telefonates tek chia_inter_urgenti
         */
        $query_update_prod = "UPDATE products
                              SET status = 'paid',
                                  updated_at = '" . date("Y-m-d H:i:s") . "'
                              WHERE id = '" . mysqli_real_escape_string($conn, $product_id) . "'
                             ";

        $result_update_prod = mysqli_query($conn, $query_update_prod);
        if (!$result_update_prod) {
            echo json_encode(array("status" => 404, "message" => "Internal Server Error." . __LINE__, "error" => mysqli_error($conn)));
            exit;
        }


        echo json_encode(array("status" => 200, "message" => "Payment completed successfully!", "customer_id" => $customer_id));
        exit;

    } catch (Exception $e) {
        /**
         * Ruajme Stripe Logs per Exceptions
         */
        $action = $_POST['action'] . "_exceptions";
        $data = json_encode($e);
        $exception_object = json_encode(array(
            "status" => 404,
            "message" => $e->getError()->message,
            "error_code" => $e->getError()->code,
            "error_type" => $e->getError()->type,
            "httpStatus" => $e->getHttpStatus(),
            "payment_method" => $payment_method,
            "payment_intent" => $e->getError()->payment_intent
        ));

        include "logs.php";
        echo json_encode(array("status" => 404, "message" => $e->getError()->message,
            "error_code" => $e->getError()->code,
            "error_type" => $e->getError()->type,
            "httpStatus" => $e->getHttpStatus(),
            "payment_method" => $payment_method,
            "payment_intent" => $e->getError()->payment_intent
        ));
        exit;
    }
}