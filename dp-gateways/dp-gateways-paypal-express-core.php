<?php

/* PayPal API Module
 *
 * An express checkout transaction starts with a token, that identifies
 * to PayPal your transaction. In this example, when the script sees a token,
 * the script knows that the buyer has already authorized payment through paypal.
 * If no token was found, the action is to send the buyer to PayPal
 * to first authorize payment
 */

/* All PayPal settings defined in the admin area */
$dp_options = get_site_option( 'dp_options' );

/* PayPal API Credentials */
$API_UserName  = $dp_options['paypal']['api_username'];
$API_Password  = $dp_options['paypal']['api_password'];
$API_Signature = $dp_options['paypal']['api_signature'];

/*
 * Define the PayPal Redirect URLs.
 * This is the URL that the buyer is first sent to do authorize payment
 * with their paypal account change the URL depending if you are testing
 * on the sandbox or the live PayPal site.
 * For the sandbox, the URL is:
 * https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=
 * For the live site, the URL is:
 * https://www.paypal.com/webscr&cmd=_express-checkout&token=
 */
if ( $dp_options['paypal']['api_url'] == 'Sandbox' ) {
    $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
    $PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
}
else {
    $API_Endpoint = "https://api-3t.paypal.com/nvp";
    $PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
}

/*
 * The currencyCodeType and paymentType are set to the selections
 * made on the Integration Assistant.
 */
$currencyCodeType = $dp_options['paypal']['currency_code'];
$paymentType      = 'Sale';

/*
 * The returnURL is the location where buyers return to when a payment
 * has been succesfully authorized.
 * This is set to the value entered on the Integration Assistant.
 */
$returnURL =  get_bloginfo('url') . '/submit-listing/';

/*
 * The cancelURL is the location buyers are sent to when they hit the
 * cancel button during authorization of payment during the PayPal flow.
 * This is set to the value entered on the Integration Assistant
 */
$cancelURL = get_bloginfo('url') . '/';

/* Defines all the global variables  */
$PROXY_HOST = '127.0.0.1';
$PROXY_PORT = '808';

/* BN Code 	is only applicable for partners */
$sBNCode = "PP-ECWizard";

/* Version and Proxy Usage  */
$USE_PROXY = false;
$version   = '64';

if ( session_id() == '' )
    session_start();

/**
 * dp_geteways_paypal_express_checkout()
 *
 * PayPal Express Checkout Module
 */
function dp_geteway_paypal_express_call_checkout( $amount ) {
    global $currencyCodeType, $paymentType;
    global $returnURL, $cancelURL;

    /*
     * The paymentAmount is the total value of the shopping cart, that was set
     * earlier in a session variable by the shopping cart page.
     */
    $paymentAmount = $amount;

    /*
     * Calls the SetExpressCheckout API call.
     * The CallShortcutExpressCheckout function is defined in the file
     * "dp-gatways-paypal-functions.php", it is included at the top of this file.
     */
    $resArray = CallShortcutExpressCheckout( $paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL );

    $ack = strtoupper( $resArray['ACK'] );

    if ( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' ) {
        RedirectToPayPal( $resArray['TOKEN'] );
    }
    else {
        //Display a user friendly Error on the page using any of the following error information returned by PayPal
        $ErrorCode         = urldecode( $resArray['L_ERRORCODE0'] );
        $ErrorShortMsg     = urldecode( $resArray['L_SHORTMESSAGE0'] );
        $ErrorLongMsg      = urldecode( $resArray['L_LONGMESSAGE0'] );
        $ErrorSeverityCode = urldecode( $resArray['L_SEVERITYCODE0'] );

        echo 'SetExpressCheckout API call failed. ';
        echo 'Detailed Error Message: ' . $ErrorLongMsg;
        echo 'Short Error Message: '    . $ErrorShortMsg;
        echo 'Error Code: '             . $ErrorCode;
        echo 'Error Severity Code: '    . $ErrorSeverityCode;
    }
}

/**
 * PayPal Express Checkout Call
 */
function dp_gateway_paypal_express_get_payment_details() {
    // Check to see if the Request object contains a variable named 'token'
    $token = '';

    if ( isset($_REQUEST['token']) )
        $token = $_REQUEST['token'];

    // If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.
    if ( $token != '' ) {

        $resArray = GetShippingDetails( $token );
        $ack  = strtoupper( $resArray["ACK"] );

        if ( $ack == 'SUCCESS' || $ack == 'SUCESSWITHWARNING' ) {
            /*
             * The information that is returned by the GetExpressCheckoutDetails
             * call should be integrated by the partner into his Order Review page
             */
            $email 				= $resArray["EMAIL"]; // ' Email address of payer.
            $payerId 			= $resArray["PAYERID"]; // ' Unique PayPal customer account identification number.
            $payerStatus		= $resArray["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
            $salutation			= $resArray["SALUTATION"]; // ' Payer's salutation.
            $firstName			= $resArray["FIRSTNAME"]; // ' Payer's first name.
            $middleName			= $resArray["MIDDLENAME"]; // ' Payer's middle name.
            $lastName			= $resArray["LASTNAME"]; // ' Payer's last name.
            $suffix				= $resArray["SUFFIX"]; // ' Payer's suffix.
            $cntryCode			= $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
            $business			= $resArray["BUSINESS"]; // ' Payer's business name.
            $shipToName			= $resArray["SHIPTONAME"]; // ' Person's name associated with this address.
            $shipToStreet		= $resArray["SHIPTOSTREET"]; // ' First street address.
            $shipToStreet2		= $resArray["SHIPTOSTREET2"]; // ' Second street address.
            $shipToCity			= $resArray["SHIPTOCITY"]; // ' Name of city.
            $shipToState		= $resArray["SHIPTOSTATE"]; // ' State or province
            $shipToCntryCode	= $resArray["SHIPTOCOUNTRYCODE"]; // ' Country code.
            $shipToZip			= $resArray["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
            $addressStatus 		= $resArray["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal
            $invoiceNumber		= $resArray["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
            $phonNumber			= $resArray["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one.
        }
        else {
            //Display a user friendly Error on the page using any of the following error information returned by PayPal
            $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
            $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
            $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
            $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

            echo "GetExpressCheckoutDetails API call failed. ";
            echo "Detailed Error Message: " . $ErrorLongMsg;
            echo "Short Error Message: " . $ErrorShortMsg;
            echo "Error Code: " . $ErrorCode;
            echo "Error Severity Code: " . $ErrorSeverityCode;
        }

        return $resArray;
    }
}

function dp_gateway_paypal_express_direct_payment( $amount, $creditCardType, $creditCardNumber, $expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, $countryCode ) {
    global $currencyCodeType, $paymentType;

    /*
     * The paymentAmount is the total value of the shopping cart, that was set
     * earlier in a session variable by the shopping cart page.
     */
    $paymentAmount = $amount;

    //' Set these values based on what was selected by the user on the Billing page Html form

    // $creditCardType 		    = "<<Visa/MasterCard/Amex/Discover>>"; //' Set this to one of the acceptable values (Visa/MasterCard/Amex/Discover) match it to what was selected on your Billing page
    // $creditCardNumber 		= "<<CC number>>"; //' Set this to the string entered as the credit card number on the Billing page
    // $expDate 				= "<<Expiry Date>>"; //' Set this to the credit card expiry date entered on the Billing page
    // $cvv2 					= "<<cvv2>>"; //' Set this to the CVV2 string entered on the Billing page
    // $firstName 				= "<<firstName>>"; //' Set this to the customer's first name that was entered on the Billing page
    // $lastName 				= "<<lastName>>"; //' Set this to the customer's last name that was entered on the Billing page
    // $street 				    = "<<street>>"; //' Set this to the customer's street address that was entered on the Billing page
    // $city 					= "<<city>>"; //' Set this to the customer's city that was entered on the Billing page
    // $state 					= "<<state>>"; //' Set this to the customer's state that was entered on the Billing page
    // $zip 					= "<<zip>>"; //' Set this to the zip code of the customer's address that was entered on the Billing page
    // $countryCode 			= "<<PayPal Country Code>>"; //' Set this to the PayPal code for the Country of the customer's address that was entered on the Billing page
    // $currencyCode 			= "<<PayPal Currency Code>>"; //' Set this to the PayPal code for the Currency used by the customer

    /*
     * Calls the DoDirectPayment API call
     *
     * The DirectPayment function is defined in PayPalFunctions.php included at the top of this file.
     */

    $resArray = DirectPayment( $paymentType, $paymentAmount,$creditCardType, $creditCardNumber, $expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, $countryCode, $currencyCodeType );

    $ack = strtoupper( $resArray["ACK"] );

    if ( $ack=='SUCCESS' || $ack=='SUCCESSWITHWARNING' ) {
        //Getting transaction ID from API responce.
        $TransactionID = urldecode($resArray["TRANSACTIONID"]);

        echo "Your payment has been successfully processed";
    }
    else {
        //Display a user friendly Error on the page using any of the following error information returned by PayPal
        $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
        $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
        $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
        $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

        echo "Direct credit card payment API call failed.<br /><br />";
        echo " Detailed Error Message: " . $ErrorLongMsg;
        echo "<br /> Short Error Message: " . $ErrorShortMsg;
        echo "<br /> Error Code: " . $ErrorCode;
        echo "<br /> Error Severity Code: " . $ErrorSeverityCode;
    }

    return $resArray;
}

/**
 * PayPal Express Checkout Call
 */
function dp_geteway_paypal_express_confirm_payment( $amount ) {

    /*
     * The paymentAmount is the total value of the shopping cart,
     * that was set earlier in a session variable by the shopping cart page.
     */
    $finalPaymentAmount =  $amount;

    /*
     * Calls the DoExpressCheckoutPayment API call
     *
     * The ConfirmPayment function is defined in the file PayPalFunctions.jsp,
     * that is included at the top of this file.
     */
    $resArray = ConfirmPayment( $finalPaymentAmount );

    $ack = strtoupper( $resArray['ACK'] );

    if ( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" ) {

        /*
         * THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE
         * transactionId & orderTime
         * IN THEIR OWN  DATABASE
         * AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT
         */
        $transactionId		= $resArray["TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs.
        $transactionType 	= $resArray["TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout
        $paymentType		= $resArray["PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant
        $orderTime 			= $resArray["ORDERTIME"];  //' Time/date stamp of payment
        $amt				= $resArray["AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
        $currencyCode		= $resArray["CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
        $feeAmt				= $resArray["FEEAMT"];  //' PayPal fee amount charged for the transaction
        $settleAmt			= $resArray["SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
        $taxAmt				= $resArray["TAXAMT"];  //' Tax charged on the transaction.
        $exchangeRate		= $resArray["EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.

        /*
         * Status of the payment:
         *
         * 'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
         * 'Pending: The payment is pending. See the PendingReason element for more information.
         */
        $paymentStatus	= $resArray["PAYMENTSTATUS"];

        /*
         * The reason the payment is pending:
         *
         * none: No pending reason
         * address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.
         * echeck: The payment is pending because it was made by an eCheck that has not yet cleared.
         * intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.
         * multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.
         * verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.
         * other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service.
         */
        $pendingReason	= $resArray["PENDINGREASON"];

        /*
         * The reason for a reversal if TransactionType is reversal:
         *
         * none: No reason code
         * chargeback: A reversal has occurred on this transaction due to a chargeback by your customer.
         * guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee.
         * buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer.
         * refund: A reversal has occurred on this transaction because you have given the customer a refund.
         * other: A reversal has occurred on this transaction due to a reason not listed above.
         */
        $reasonCode		= $resArray["REASONCODE"];

        return $resArray;
    }
    else {
        //Display a user friendly Error on the page using any of the following error information returned by PayPal
        $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
        $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
        $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
        $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

        echo "GetExpressCheckoutDetails API call failed. ";
        echo "Detailed Error Message: " . $ErrorLongMsg;
        echo "Short Error Message: " . $ErrorShortMsg;
        echo "Error Code: " . $ErrorCode;
        echo "Error Severity Code: " . $ErrorSeverityCode;
    }
}

/**
 * CallShortcutExpressCheckout()
 * 
 * Prepares the parameters for the SetExpressCheckout API Call.
 * 
 * @param $paymentAmount  	 Total value of the shopping cart
 * @param $currencyCodeType  Currency code value the PayPal API
 * @param $paymentType  	 PaymentType has to be one of the following values: Sale or Order or Authorization
 * @param $returnURL		 The page where buyers return to after they are done with the payment review on PayPal
 * @param $cancelURL		 The page where buyers return to when they cancel the payment review on PayPal
 *
 * @return $resArray
 */
function CallShortcutExpressCheckout( $paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL ) {
    
    // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
    $nvpstr  = '&PAYMENTREQUEST_0_AMT='           . $paymentAmount;
    $nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymentType;
    $nvpstr .= '&RETURNURL='                      . $returnURL;
    $nvpstr .= '&CANCELURL='                      . $cancelURL;
    $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='  . $currencyCodeType;

    $_SESSION['currencyCodeType'] = $currencyCodeType;
    $_SESSION['PaymentType']      = $paymentType;

    /*
     * Make the API call to PayPal
     * If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
     * If an error occured, show the resulting errors
     */
    $resArray = hash_call( 'SetExpressCheckout', $nvpstr );
    $ack      = strtoupper( $resArray["ACK"] );
    
    if( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' ) {
        $token = urldecode( $resArray['TOKEN'] );
        $_SESSION['TOKEN'] = $token;
    }

    return $resArray;
}

/**
 * CallMarkExpressCheckout()
 *
 * Prepares the parameters for the SetExpressCheckout API Call.
 * 
 * @param $paymentAmount  	 Total value of the shopping cart
 * @param $currencyCodeType  Currency code value the PayPal API
 * @param $paymentType  	 PaymentType has to be one of the following values: Sale or Order or Authorization
 * @param $returnURL		 The page where buyers return to after they are done with the payment review on PayPal
 * @param $cancelURL	     The page where buyers return to when they cancel the payment review on PayPal
 * @param $shipToName		 The Ship to name entered on the merchant's site
 * @param $shipToStreet 	 The Ship to Street entered on the merchant's site
 * @param $shipToCity		 The Ship to City entered on the merchant's site
 * @param $shipToState		 The Ship to State entered on the merchant's site
 * @param $shipToCountryCode The Code for Ship to Country entered on the merchant's site
 * @param $shipToZip		 The Ship to ZipCode entered on the merchant's site
 * @param $shipToStreet2	 The Ship to Street2 entered on the merchant's site
 * @param $phoneNum 		 The phoneNum  entered on the merchant's site
 *
 * @return $resArray
 */
function CallMarkExpressCheckout( $paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState, $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum ) {
    
    // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
    $nvpstr  = '&PAYMENTREQUEST_0_AMT='               . $paymentAmount;
    $nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION='     . $paymentType;
    $nvpstr .= '&RETURNURL='                          . $returnURL;
    $nvpstr .= '&CANCELURL='                          . $cancelURL;
    $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='      . $currencyCodeType;
    $nvpstr .= '&ADDROVERRIDE=1';
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTONAME='        . $shipToName;
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOSTREET='      . $shipToStreet;
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOSTREET2='     . $shipToStreet2;
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOCITY='        . $shipToCity;
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOSTATE='       . $shipToState;
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=' . $shipToCountryCode;
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOZIP='         . $shipToZip;
    $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOPHONENUM='    . $phoneNum;

    $_SESSION['currencyCodeType'] = $currencyCodeType;
    $_SESSION['PaymentType']      = $paymentType;

    /*
     * Make the API call to PayPal
     * If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
     * If an error occured, show the resulting errors
     */
    $resArray = hash_call( 'SetExpressCheckout', $nvpstr );
    $ack      = strtoupper( $resArray['ACK'] );

    if( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' ) {
        $token = urldecode( $resArray['TOKEN'] );
        $_SESSION['TOKEN'] = $token;
    }

    return $resArray;
}

/**
 * GetShippingDetails
 *
 * Prepares the parameters for the GetExpressCheckoutDetails API Call.
 *
 * @param $token
 * @return The NVP Collection object of the GetExpressCheckoutDetails Call Response.
 */
function GetShippingDetails( $token ) {

    /*
     * At this point, the buyer has completed authorizing the payment
     * at PayPal.  The function will call PayPal to obtain the details
     * of the authorization, incuding any shipping information of the
     * buyer.  Remember, the authorization is not a completed transaction
     * at this state - the buyer still needs an additional step to finalize
     * the transaction
     */

    /*
     * Build a second API request to PayPal, using the token as the
     * ID to get the details on the payment authorization
     */
    $nvpstr="&TOKEN=" . $token;

    /*
     * Make the API call and store the results in an array.
     * If the call was a success, show the authorization details, and provide
     * an action to complete the payment.
     * If failed, show the error.
     */
    $resArray = hash_call( 'GetExpressCheckoutDetails', $nvpstr );
    $ack      = strtoupper( $resArray['ACK'] );

    if ( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' ) {
        $_SESSION['payer_id'] =	$resArray['PAYERID'];
    }

    return $resArray;
}

/**
 * ConfirmPayment()
 * 
 * Prepares the parameters for the GetExpressCheckoutDetails API Call.
 *
 * @param $FinalPaymentAmt
 * sBNCode:	The BN code used by PayPal to track the transactions from a given shopping cart.
 * @return $resArray The NVP Collection object of the GetExpressCheckoutDetails Call Response.
 */
function ConfirmPayment( $FinalPaymentAmt ) {
    /*
     * Gather the information to make the final call to finalize the PayPal payment.
     * The variable nvpstr holds the name value pairs
     */
    
    //Format the other parameters that were stored in the session from the previous calls
    $token 				= urlencode( $_SESSION['TOKEN'] );
    $paymentType 		= urlencode( $_SESSION['PaymentType'] );
    $currencyCodeType 	= urlencode( $_SESSION['currencyCodeType'] );
    $payerID 			= urlencode( $_SESSION['payer_id'] );

    $serverName 		= urlencode($_SERVER['SERVER_NAME']);

    $nvpstr  = '&TOKEN='                          . $token .
               '&PAYERID='                        . $payerID .
               '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymentType .
               '&PAYMENTREQUEST_0_AMT='           . $FinalPaymentAmt;
    
    $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='  . $currencyCodeType .
               '&IPADDRESS='                      . $serverName;

    /*
     * Make the call to PayPal to finalize payment.
     * If an error occured, show the resulting errors
     */
    $resArray = hash_call( 'DoExpressCheckoutPayment', $nvpstr );

    /*
     * Display the API response back to the browser.
     * If the response from PayPal was a success, display the response parameters.
     * If the response was an error, display the errors received using APIError.php.
     */
    $ack = strtoupper( $resArray['ACK'] );

    return $resArray;
}

/**
 * DirectPayment()
 * 
 * This function makes a DoDirectPayment API call
 * 
 * @param $paymentType      PaymentType has to be one of the following values: Sale or Order or Authorization
 * @param $paymentAmount    Total value of the shopping cart
 * @param $currencyCode     Currency code value the PayPal API
 * @param $firstName        First name as it appears on credit card
 * @param $lastName         Last name as it appears on credit card
 * @param $street           Buyer's street address line as it appears on credit card
 * @param $city             Buyer's city
 * @param $state            Buyer's state
 * @param $countryCode      Buyer's country code
 * @param $zip              Buyer's zip
 * @param $creditCardType   Buyer's credit card type (i.e. Visa, MasterCard ... )
 * @param $creditCardNumber	Buyers credit card number without any spaces, dashes or any other characters
 * @param $expDate          Credit card expiration date.
 * @param $cvv2             Card Verification Value 
 * 
 * @return $resArray The NVP Collection object of the DoDirectPayment Call Response.
 */
function DirectPayment( $paymentType, $paymentAmount, $creditCardType, $creditCardNumber, $expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, $countryCode, $currencyCode ) {
    
    //Construct the parameter string that describes DoDirectPayment
    $nvpstr  = '&AMT='            . urlencode( $paymentAmount );
    $nvpstr .= '&CURRENCYCODE='   . urlencode( $currencyCode );
    $nvpstr .= '&PAYMENTACTION='  . urlencode( $paymentType );
    $nvpstr .= '&CREDITCARDTYPE=' . urlencode( $creditCardType );
    $nvpstr .= '&ACCT='           . urlencode( $creditCardNumber );
    $nvpstr .= '&EXPDATE='        . urlencode( $expDate );
    $nvpstr .= '&CVV2='           . urlencode( $cvv2 );
    $nvpstr .= '&FIRSTNAME='      . urlencode( $firstName );
    $nvpstr .= '&LASTNAME='       . urlencode( $lastName );
    $nvpstr .= '&STREET='         . urlencode( $street );
    $nvpstr .= '&CITY='           . urlencode( $city );
    $nvpstr .= '&STATE='          . urlencode( $state );
    $nvpstr .= '&ZIP='            . urlencode( $zip );
    $nvpstr .= '&COUNTRYCODE='    . urlencode( $countryCode );
    $nvpstr .= '&IPADDRESS='      . urlencode( $_SERVER['REMOTE_ADDR'] );

    $resArray = hash_call( 'DoDirectPayment', $nvpstr );

    return $resArray;
}

/**
 * hash_call()
 *
 * Function to perform the API call to PayPal using API signature
 *
 * @param $methodName is name of API  method.
 * @param $nvpStr is nvp string.
 * 
 * @return array $nvpResArray Associtive array containing the response from the server.
 */
function hash_call( $methodName, $nvpStr ) {
    
    //declaring of global variables
    global $API_Endpoint, $API_UserName, $API_Password, $API_Signature, $version;
    global $USE_PROXY, $PROXY_HOST, $PROXY_PORT;
    global $gv_ApiErrorURL, $sBNCode;

    //setting the curl parameters.
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $API_Endpoint );
    curl_setopt( $ch, CURLOPT_VERBOSE, 1);

    //turning off the server and peer verification ( TrustManager Concept ).
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );

    //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
    //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
    if ( $USE_PROXY )
        curl_setopt( $ch, CURLOPT_PROXY, $PROXY_HOST. ':' . $PROXY_PORT );

    //NVPRequest for submitting to server
    $nvpreq = 'METHOD='        . urlencode( $methodName ) .
              '&VERSION='      . urlencode( $version) .
              '&PWD='          . urlencode( $API_Password ) .
              '&USER='         . urlencode( $API_UserName ) .
              '&SIGNATURE='    . urlencode( $API_Signature ) . $nvpStr .
              '&BUTTONSOURCE=' . urlencode($sBNCode);

    //setting the nvpreq as POST FIELD to curl
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $nvpreq );

    //getting response from server
    $response = curl_exec( $ch );

    //convrting NVPResponse to an Associative Array
    $nvpResArray = deformatNVP( $response );
    $nvpReqArray = deformatNVP( $nvpreq );
    $_SESSION['nvpReqArray'] = $nvpReqArray;

    if ( curl_errno( $ch )) {
        // moving to display page to display curl errors
        $_SESSION['curl_error_no']  = curl_errno( $ch );
        $_SESSION['curl_error_msg'] = curl_error( $ch );
        //Execute the Error handling module to display errors.
    } else {
        //closing the curl
        curl_close( $ch );
    }

    return $nvpResArray;
}

/**
 * RedirectToPayPal()
 *
 * Redirects to PayPal.com site.
 * 
 * @param $token NVP string.
 */
function RedirectToPayPal( $token ) {
    global $PAYPAL_URL;
    
    // Redirect to paypal.com here
    $payPalURL = $PAYPAL_URL . $token;

    header( 'Location: ' . $payPalURL );
    exit();
}

/**
 * deformatNVP()
 *
 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
 * It is usefull to search for a particular key and displaying arrays.
 * 
 * @param  $nvpstr is NVPString.
 * 
 * @return array $nvpArray is Associative Array.
 */
function deformatNVP( $nvpstr ) {
    $intial = 0;
    $nvpArray = array();

    while( strlen( $nvpstr )) {
        //postion of Key
        $keypos = strpos( $nvpstr, '=' );
        
        //position of value
        $valuepos = strpos( $nvpstr, '&' ) ? strpos( $nvpstr, '&' ) : strlen( $nvpstr );

        /*getting the Key and Value values and storing in a Associative Array*/
        $keyval = substr( $nvpstr, $intial, $keypos );
        $valval = substr( $nvpstr, $keypos + 1, $valuepos - $keypos - 1 );
        
        // decoding the respose
        $nvpArray[urldecode( $keyval )] = urldecode( $valval );
        $nvpstr = substr( $nvpstr, $valuepos + 1, strlen( $nvpstr ));
     }
     
    return $nvpArray;
}

?>