<?php

/**
<<<<<<< HEAD
 * Flutterwave WHMCS Payment Gateway Module - Callback v1.2.1
=======
 * Flutterwave WHMCS Payment Gateway Module - Callback v2.0
>>>>>>> 27c235b (Updated Flutterwave WHMCS module with latest fixes and improvements)
 *
 * This Payment Gateway module allows you to integrate Flutterwave payment solutions with the
 * WHMCS platform.
 *
 * For more information, please refer to the online documentation: 
 * https://developer.flutterwave.com/docs
 * 
 * 
 * @author Joseph Chuks <info@josephchuks.com>
 *
 * @copyright Copyright (c) Joseph Chuks 2025
 */


// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active.
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

<<<<<<< HEAD

=======
>>>>>>> 27c235b (Updated Flutterwave WHMCS module with latest fixes and improvements)
$invoiceId = $_GET["tx_ref"];
$trxStatus = $_GET["status"];
$transactionId = $_GET["transaction_id"];
$secretKey = $gatewayParams['secretKey'];

$systemUrl = $gatewayParams['systemurl'];
$invoice_url = $systemUrl . 'viewinvoice.php?id=' . $invoiceId;

// Check invoice
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['invoiceid']);

<<<<<<< HEAD
// Check Transaction
checkCbTransID($transactionId);

// Verify Transaction
$curl = curl_init();
$api = "https://api.flutterwave.com/v3/transactions/$transactionId/verify";

curl_setopt($curl, CURLOPT_URL, $api);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPGET, true);

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $secretKey,
];
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $result = new stdClass();
    $result->status = 'error';
    $result->message = 'cURL error: ' . curl_error($curl);
} else {
    $result = json_decode($response);
}

curl_close($curl);


if ($result->status == 'success') {

    // Convert to base currency
    $paymentAmount = $result->data->amount;
    if ($gatewayParams['convertto']) {
        $result = select_query("tblclients", "tblinvoices.invoicenum,tblclients.currency,tblcurrencies.code", array("tblinvoices.id" => $invoiceId), "", "", "", "tblinvoices ON tblinvoices.userid=tblclients.id INNER JOIN tblcurrencies ON tblcurrencies.id=tblclients.currency");
        $data = mysql_fetch_array($result);
        $invoice_currency_id = $data['currency'];

        $converto_amount = convertCurrency($paymentAmount, $gatewayParams['convertto'], $invoice_currency_id);
        $amount = format_as_currency($converto_amount);
    } else {
        $amount = number_format(floatval($paymentAmount), 2, '.', '');
    }
=======
// If transaction status is cancelled
if ($trxStatus === "cancelled") {
    if ($gatewayParams['gatewayLogs'] == 'on') {
        $output = "Transaction ref: " . $transactionId
            . "\r\nInvoice ID: " . $invoiceId
            . "\r\nStatus: cancelled";
        logTransaction($gatewayModuleName, $output, "Payment cancelled by customer");
    }
    header("Location: $systemUrl/clientarea.php?action=invoices");
    exit;
}

// Check Transaction
checkCbTransID($transactionId);

// If transaction status is failed
if ($trxStatus === "failed") {
    if ($gatewayParams['gatewayLogs'] == 'on') {
        $output = "Transaction ref: " . $transactionId
            . "\r\nInvoice ID: " . $invoiceId
            . "\r\nStatus: failed";
        logTransaction($gatewayModuleName, $output, "Payment failed");
    }
    header("Location: $systemUrl/clientarea.php?action=invoices");
    exit;
}


// Verify Transaction
$curl = curl_init();
$api = "https://api.flutterwave.com/v3/transactions/$transactionId/verify";

curl_setopt($curl, CURLOPT_URL, $api);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPGET, true);

$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $secretKey,
];
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $result = new stdClass();
    $result->status = 'error';
    $result->message = 'cURL error: ' . curl_error($curl);
} else {
    $result = json_decode($response);
}

curl_close($curl);


if ($result->status == 'success') {

    // Convert to base currency
    $paymentAmount = $result->data->amount;
    if ($gatewayParams['convertto']) {
        $result = select_query("tblclients", "tblinvoices.invoicenum,tblclients.currency,tblcurrencies.code", array("tblinvoices.id" => $invoiceId), "", "", "", "tblinvoices ON tblinvoices.userid=tblclients.id INNER JOIN tblcurrencies ON tblcurrencies.id=tblclients.currency");
        $data = mysql_fetch_array($result);
        $invoice_currency_id = $data['currency'];

        $converto_amount = convertCurrency($paymentAmount, $gatewayParams['convertto'], $invoice_currency_id);
        $amount = format_as_currency($converto_amount);
    } else {
        $amount = number_format(floatval($paymentAmount), 2, '.', '');
    }
>>>>>>> 27c235b (Updated Flutterwave WHMCS module with latest fixes and improvements)

    // Log Payment
    if ($gatewayParams['gatewayLogs'] == 'on') {
        $output = "Transaction ref: " . $transactionId
            . "\r\nInvoice ID: " . $invoiceId
            . "\r\nStatus: succeeded";
        logTransaction($gatewayModuleName, $output, "Success");
    }

    // Add Invoice
    addInvoicePayment(
        $invoiceId,
        $transactionId,
        $amount,
        0,
        $gatewayModuleName
    );

    header('Location: ' . $invoice_url);
<<<<<<< HEAD
} else {

    if ($gatewayParams['gatewayLogs'] == 'on') {
        $output = "Transaction ref: " . $transactionId
            . "\r\nInvoice ID: " . $invoiceId
            . "\r\nStatus: failed";
        logTransaction($gatewayModuleName, $output, "Failed");
    }

    echo
    '<script type="text/javascript">
    alert("Transaction failed. Please try again");
    window.location.href = "' . $invoice_url . '";
    </script>';
=======
    exit;
>>>>>>> 27c235b (Updated Flutterwave WHMCS module with latest fixes and improvements)
}
