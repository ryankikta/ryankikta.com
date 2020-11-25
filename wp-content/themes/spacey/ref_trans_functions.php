<?php
$SandboxFlag = false;
$API_UserName = "billing_api1.ryankikta.com";
$API_Password = "9VXSWWGEQDX82U2M";
$API_Signature = "AFcWxV21C7fd0v3bYYYRCpSSRl31AJU.41eud0U3XWhf7Lfdprw97sa4";

// BN Code     is only applicable for partners
//$sBNCode = "PP-ECWizard";
if ($SandboxFlag) {
    $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
    $PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
} else {
    $API_Endpoint = "https://api-3t.paypal.com/nvp";
    $PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
}

$USE_PROXY = false;
$version = "86";

if (session_id() == "") {
    session_start();
}

function CallSetExpressCheckout($currencyCodeType, $paymentType, $returnURL, $cancelURL, $message = "Authorize automatic payments of orders at RyanKikta.com", $custom = "")
{
    $nvpstr = "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_AMT=0"; // should be diffrent to 0
    $nvpstr = $nvpstr . "&L_BILLINGTYPE0=MerchantInitiatedBilling";
    $nvpstr = $nvpstr . "&L_BILLINGAGREEMENTDESCRIPTION0=$message";
    $nvpstr = $nvpstr . "&RETURNURL=" . $returnURL;
    $nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
    if ($custom != "") {
        $nvpstr = $nvpstr . "&CUSTOM=" . $custom;
    }

    //  wp_mail('team@ryankikta.com','url payment',$nvpstr);
    $_SESSION["currencyCodeType"] = $currencyCodeType;

    $resArray = hash_call("SetExpressCheckout", $nvpstr);
    $_SESSION['resArray'] = $resArray;
    $ack = strtoupper($resArray["ACK"]);
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        $token = urldecode($resArray["TOKEN"]);
        $_SESSION['TOKEN'] = $token;
    }

    return $resArray;
}

function GetShippingDetails($token)
{
    $nvpstr = "&TOKEN=" . $token;
    $resArray = hash_call("GetExpressCheckoutDetails", $nvpstr);
    $ack = strtoupper($resArray["ACK"]);

    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        $_SESSION['payer_id'] = $resArray['PAYERID'];
        $_SESSION['email'] = $resArray['EMAIL'];
        $_SESSION['firstName'] = $resArray["FIRSTNAME"];
        $_SESSION['lastName'] = $resArray["LASTNAME"];
        $_SESSION['shipToName'] = $resArray["SHIPTONAME"];
        $_SESSION['shipToStreet'] = $resArray["SHIPTOSTREET"];
        $_SESSION['shipToCity'] = $resArray["SHIPTOCITY"];
        $_SESSION['shipToState'] = $resArray["SHIPTOSTATE"];
        $_SESSION['shipToZip'] = $resArray["SHIPTOZIP"];
        $_SESSION['shipToCountry'] = $resArray["SHIPTOCOUNTRYCODE"];
    }
    return $resArray;
}

function hash_call($methodName, $nvpStr)
{
    //declaring of global variables
    global $API_Endpoint, $version, $API_UserName, $API_Password, $API_Signature;
    global $USE_PROXY, $PROXY_HOST, $PROXY_PORT;
    global $gv_ApiErrorURL;
    global $sBNCode;

    //setting the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    //turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    //NVPRequest for submitting to server
    $nvpreq = "METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($version) . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpStr . "&BUTTONSOURCE=" . urlencode($sBNCode);

    //setting the nvpreq as POST FIELD to curl
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    //getting response from server
    $response = curl_exec($ch);

    //convrting NVPResponse to an Associative Array
    $nvpResArray = deformatNVP2($response);
    $nvpReqArray = deformatNVP2($nvpreq);
    $_SESSION['nvpReqArray'] = $nvpReqArray;
    if (curl_errno($ch)) {
        // moving to display page to display curl errors
        $_SESSION['curl_error_no'] = curl_errno($ch);
        $_SESSION['curl_error_msg'] = curl_error($ch);

        //Execute the Error handling module to display errors.
    } else {
        //closing the curl
        curl_close($ch);
    }

    return $nvpResArray;
}

/*'----------------------------------------------------------------------------------
 Purpose: Redirects to PayPal.com site.
 Inputs:  NVP string.
 Returns:
----------------------------------------------------------------------------------
*/
function RedirectToPayPal_RT($token)
{
    global $PAYPAL_URL;

    // Redirect to paypal.com here
    $payPalURL = $PAYPAL_URL . $token;
    //debug($PAYPAL_URL);
    wp_redirect($payPalURL);
    exit();
    header("Location: " . $payPalURL);
    exit();
}

function get_redirect_url_express($token)
{
    global $PAYPAL_URL;

    // Redirect to paypal.com here
    return $PAYPAL_URL . $token;
}

function deformatNVP2($nvpstr)
{
    $intial = 0;
    $nvpArray = array();

    while (strlen($nvpstr)) {
        //postion of Key
        $keypos = strpos($nvpstr, '=');
        //position of value
        $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

        /*getting the Key and Value values and storing in a Associative Array*/
        $keyval = substr($nvpstr, $intial, $keypos);
        $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
        //decoding the respose
        $nvpArray[urldecode($keyval)] = urldecode($valval);
        $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
    }
    return $nvpArray;
}

function create_billing_agreement($token)
{
    $nvpstr = "&TOKEN=" . $token;
    $resArray = hash_call("CreateBillingAgreement", $nvpstr);
    $_SESSION['resArray'] = $resArray;
    $ack = strtoupper($resArray["ACK"]);
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        $token = urldecode($resArray["TOKEN"]);
        $_SESSION['TOKEN'] = $token;
    }

    return $resArray;
}

function callRefenceTransaction($referenceid, $paymentType, $AMT, $description = '', $shipAdrInf = array())
{
    $nvpstr = "&AMT=" . $AMT;
    $nvpstr = $nvpstr . "&REFERENCEID=" . $referenceid;
    $nvpstr = $nvpstr . "&PAYMENTACTION=" . $paymentType;
    if ($description != '') {
        $nvpstr = $nvpstr . "&DESC=" . $description;
    }
    /**/
    if (is_array($shipAdrInf) && !empty($shipAdrInf)) {
        $nvpstr = $nvpstr . "&REQCONFIRMSHIPPING=0";
        if (strlen($shipAdrInf['name']) > 32) {
            $nvpstr = $nvpstr . "&SHIPTONAME=" . substr($shipAdrInf['name'], 0, 32);
        } else {
            $nvpstr = $nvpstr . "&SHIPTONAME=" . $shipAdrInf['name'];
        }

        if (strlen($shipAdrInf['street']) > 100) {
            $nvpstr = $nvpstr . "&SHIPTOSTREET=" . substr($shipAdrInf['street'], 0, 100);
        } else {
            $nvpstr = $nvpstr . "&SHIPTOSTREET=" . $shipAdrInf['street'];
        }

        if (strlen($shipAdrInf['street2']) > 100) {
            $nvpstr = $nvpstr . "&SHIPTOSTREET2=" . substr($shipAdrInf['street2'], 0, 100);
        } else {
            $nvpstr = $nvpstr . "&SHIPTOSTREET2=" . $shipAdrInf['street2'];
        }

        if (strlen($shipAdrInf['city']) > 40) {
            $nvpstr = $nvpstr . "&SHIPTOCITY=" . substr($shipAdrInf['city'], 0, 40);
        } else {
            $nvpstr = $nvpstr . "&SHIPTOCITY=" . $shipAdrInf['city'];
        }

        if (strlen($shipAdrInf['state']) > 40) {
            $nvpstr = $nvpstr . "&SHIPTOSTATE=" . substr($shipAdrInf['state'], 0, 40);
        } else {
            $nvpstr = $nvpstr . "&SHIPTOSTATE=" . $shipAdrInf['state'];
        }

        if (strlen($shipAdrInf['zip']) > 20) {
            $nvpstr = $nvpstr . "&SHIPTOZIP=" . substr($shipAdrInf['zip'], 0, 20);
        } else {
            $nvpstr = $nvpstr . "&SHIPTOZIP=" . $shipAdrInf['zip'];
        }

        if (strlen($shipAdrInf['country']) > 2) {
            $nvpstr = $nvpstr . "&SHIPTOCOUNTRY=" . substr($shipAdrInf['country'], 0, 2);
        } else {
            $nvpstr = $nvpstr . "&SHIPTOCOUNTRY=" . $shipAdrInf['country'];
        }

        if (strlen($shipAdrInf['phone']) > 20) {
            $nvpstr = $nvpstr . "&SHIPTOPHONENUM=" . substr($shipAdrInf['phone'], 0, 20);
        } else {
            $nvpstr = $nvpstr . "&SHIPTOPHONENUM=" . $shipAdrInf['phone'];
        }
    }
    /**/
    $resArray = hash_call("DoReferenceTransaction", $nvpstr);
    $_SESSION['resArray'] = $resArray;
    return $resArray;
}

function cancelBillingAgreement($billing_agreement)
{
    $nvpstr = "&REFERENCEID=" . $billing_agreement;
    $resArray = hash_call("BillAgreementUpdate", $nvpstr);

    return $resArray;
}

function GetTransactionDetails($transactionID)
{
    $nvpstr = "&TRANSACTIONID=" . $transactionID;
    $resArray = hash_call("GetTransactionDetails", $nvpstr);
    return $resArray;
}

function SearchTransactions($start_date, $email)
{
    $nvpstr = "&STARTDATE=" . $start_date . "&EMAIL=" . $email;
    $resArray = hash_call("TransactionSearch", $nvpstr);
    return $resArray;
}
