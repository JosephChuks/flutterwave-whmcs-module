<?php

/**
 * Flutterwave WHMCS Payment Gateway Module v1.2.1
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
 * @copyright Copyright (c) Joseph Chuks 2024
 */



if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function flutterwave_MetaData()
{
    return array(
        'DisplayName' => 'Flutterwave',
        'APIVersion' => '3.0',
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

function flutterwave_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Flutterwave',
        ),

        'cBname' => array(
            'FriendlyName' => 'Flutterwave Business Name',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter business name here',
        ),

        'cBdescription' => array(
            'FriendlyName' => 'Business Description',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter business description here',
        ),

        'whmcsLogo' => array(
            'FriendlyName' => 'Icon',
            'Type' => 'text',
            'Size' => '80',
            'Default' => '',
            'Description' => 'Enter the link to your site icon (square)',
        ),

        'publicKey' => array(
            'FriendlyName' => 'Public Key',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter public key here',
        ),
        'secretKey' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter secret key here',
        ),

        'payButtonText' => array(
            'FriendlyName' => 'Pay Button Text',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'Pay Now',
            'Description' => 'Text to display on your payment button',
        ),

        'gatewayLogs' => array(
            'FriendlyName' => 'Gateway logs',
            'Type' => 'yesno',
            'Description' => 'Select to enable gateway logs',
            'Default' => '0'
        ),
    );
}


function flutterwave_link($params)
{

    // Gateway Configuration Parameters
    $publicKey = $params['publicKey'];
    $secretKey = $params['secretKey'];
    $companyName = $params['cBname'];
    $companyDescription = $params['cBdescription'];
    $logo = $params['whmcsLogo'];
    $payButtonText = $params['payButtonText'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleName = $params['paymentmethod'];
    $returnUrl = "'.$systemUrl.'viewinvoice.php?id='.$invoiceId.'";
    $redirectUrl = $systemUrl . 'modules/gateways/callback/' . $moduleName . '.php';


    $htmlOutput = '<script src="https://checkout.flutterwave.com/v3.js"></script>';
    $htmlOutput .= '<form>';
    $htmlOutput .= '<button type="button" id="start-payment-button" 
    style="cursor: pointer;
    position: relative;
    background-color: #ff9b00;
    color: #12122c;
    max-width: 100%;
    padding: 7.5px 16px;
    font-weight: 500;
    font-size: 14px;
    border-radius: 4px;
    border: none;
    transition: all .1s ease-in;
    vertical-align: middle;" onclick="makePayment()">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <rect x="3" y="6" width="18" height="12" rx="2" ry="2" />
  <line x1="3" y1="10" x2="21" y2="10" />
  <line x1="3" y1="14" x2="21" y2="14" />
    </svg>&nbsp;
' . $langPayNow . '
    </button>';
    $htmlOutput .= '</form>';
    $htmlOutput .= '<script>
          function makePayment() {
            FlutterwaveCheckout({
              public_key: "' . $publicKey . '",
              tx_ref: "' . $invoiceId . '",
              amount: ' . $amount . ',
              currency: "' . $currencyCode . '",
              payment_options: "card, ussd, banktransfer, account, internetbanking, nqr, applepay, googlepay, enaira, opay",
              redirect_url: "'.$redirectUrl.'",
              meta: {
                consumer_id: "' . $invoiceId . '",
                consumer_mac: "' . $_SERVER['REMOTE_ADDR'] . '",
              },
              customer: {
                email: "' . $email . '",
                phone_number: "' . $phone . '",
                name: "' . $firstname . ' ' . $lastname . '",
              },
              customizations: {
                title: "' . $companyName . '",
                description: "Online Payment: ' . $description . '",
                logo: "' . $logo . '",
              },
              onclose: function() {
                window.location.href = "' . $systemUrl . 'viewinvoice.php?id=' . $invoiceId . '";
              }
            });
          }
        </script>';


    return $htmlOutput;
}
