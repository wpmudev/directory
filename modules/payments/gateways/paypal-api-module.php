<?php

/**
 * PayPal API Module
 *
 * An express checkout transaction starts with a token, that identifies
 * to PayPal your transaction. In this example, when the script sees a token,
 * the script knows that the buyer has already authorized payment through paypal.
 * If no token was found, the action is to send the buyer to PayPal
 * to first authorize payment
 **/
if ( !class_exists('PayPal_API_Module') ):
class PayPal_API_Module {

    /** @var string PayPal API Credentials - Username */
    var $api_username;
    /** @var string PayPal API Credentials - Password */
    var $api_password;
    /** @var string PayPal API Credentials - Signature */
    var $api_signature;
    /** @var string This is the URL that is used for the API calls */
    var $api_endpoint;
    /** @var string This is the URL that the buyer is first sent to authorize payment */
    var $paypal_url;
    /** @var string Currency code value for the PayPal API */
    var $currency_code_type;
    /** @var string Payment Type has to be one of the following values: "Sale", "Order" or "Authorization" */
    var $payment_type = 'Sale';
    /** @var string The page where buyers return to after they are done with the payment review on PayPal */
    var $return_url;
    /** @var string The page where buyers return to when they cancel the payment review on PayPal */
    var $cancel_url;
    /** @var string Proxy Host */
    var $proxy_host = '127.0.0.1';
    /** @var string Proxy Port */
    var $proxy_port = '808';
    /** @var string The BN code used by PayPal to track the transactions from a given shopping cart. BN Code is only applicable for partners */
    var $sbn_code = 'PP-ECWizard';
    /** @var boolean Proxy Usage  */
    var $use_proxy = false;
    /** @var string Version  */
    var $version   = '64';

    /**
     * Constructor.
     * Fire init_vars() and create session.
     *
     * @return void
     **/
    function PayPal_API_Module( $options ) {
        $this->init_vars( $options );
    }

    /**
     * Initiate variables.
     *
     * @return void
     */
    function init_vars( $options ) {
        /* Get PayPal options defined in the admin area */
        //$options = get_options('paypal');

        if ( !empty( $options ) ) {

            /* PayPal API Credentials */
            $this->api_username  = $options['api_username'];
            $this->api_password  = $options['api_password'];
            $this->api_signature = $options['api_signature'];

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
            if ( $options['api_url'] == 'live' ) {
                $this->api_endpoint = 'https://api-3t.paypal.com/nvp';
                $this->paypal_url   = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
            } else  {
                $this->api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
                $this->paypal_url   = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=';
            } 

            /*
             * The currencyCodeType and paymentType are set to the selections
             * made on the Integration Assistant.
             */
            $this->currency_code_type = $options['currency'];

            /*
             * The returnURL is the location where buyers return to when a payment
             * has been succesfully authorized.
             * This is set to the value entered on the Integration Assistant.
             */
            $this->return_url =  get_bloginfo('url') . '/checkout/';

            /*
             * The cancelURL is the location buyers are sent to when they hit the
             * cancel button during authorization of payment during the PayPal flow.
             * This is set to the value entered on the Integration Assistant
             */
            $this->cancel_url = get_bloginfo('url') . '/';
        }
    }

    /**
     * Prepares the parameters for the SetExpressCheckout API Call.
     *
     * @param  string $payment_amount Total value of the shopping cart 
     * @return array  $result
     **/
    function call_shortcut_express_checkout( $payment_amount ) {

        // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
        $nvpstr  = '&PAYMENTREQUEST_0_AMT='           . $payment_amount;
        $nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION=' . $this->payment_type;
        $nvpstr .= '&RETURNURL='                      . $this->return_url;
        $nvpstr .= '&CANCELURL='                      . $this->cancel_url;
        $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='  . $this->currency_code_type;

        $_SESSION['currency_code_type'] = $this->currency_code_type;
        $_SESSION['payment_type']       = $this->payment_type;

        /*
         * Make the API call to PayPal
         * If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
         * If an error occured, show the resulting errors
         */
        $result = $this->hash_call( 'SetExpressCheckout', $nvpstr );
        $ack    = strtoupper( $result["ACK"] );

        if ( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' ) {
            $token = urldecode( $result['TOKEN'] );
            $_SESSION['token'] = $token;
            /* Construct URL and redirect to PayPal */
            $paypal_url = $this->paypal_url . $result['TOKEN'];
            wp_redirect( $paypal_url );
        } else {
            /* Display a user friendly Error on the page using any of the following error information returned by PayPal */
            $error_call          = 'SetExpressCheckout';
            $error_code          = urldecode( $result['L_ERRORCODE0'] );
            $error_short_msg     = urldecode( $result['L_SHORTMESSAGE0'] );
            $error_long_msg      = urldecode( $result['L_LONGMESSAGE0'] );
            $error_severity_code = urldecode( $result['L_SEVERITYCODE0'] );
            /* Build error messages array */
            $result = array(
                'error_call'          => $error_call,
                'error_code'          => $error_code,
                'error_short_msg'     => $error_short_msg,
                'error_long_msg'      => $error_long_msg,
                'error_severity_code' => $error_severity_code );
            /* Set status and return error */
            $result['status'] = 'error';
            return $result;
        }
    }

    /**
     * Prepares the parameters for the SetExpressCheckout API Call.
     *
     * @param string $payment_amount       Total value of the shopping cart
     * @param string $ship_to_name         The Ship to name entered on the merchant's site
     * @param string $ship_to_street       The Ship to Street entered on the merchant's site
     * @param string $ship_to_city         The Ship to City entered on the merchant's site
     * @param string $ship_to_state        The Ship to State entered on the merchant's site
     * @param string $ship_to_country_code The Code for Ship to Country entered on the merchant's site
     * @param string $ship_to_zip          The Ship to ZipCode entered on the merchant's site
     * @param string $ship_to_street2      The Ship to Street2 entered on the merchant's site
     * @param string $phone_number         The phoneNum  entered on the merchant's site
     *
     * @return array $result The NVP Collection object of the SetExpressCheckout Call Response.
     **/
    function call_mark_express_checkout( $payment_amount, $ship_to_name, $ship_to_street, $ship_to_city, $ship_to_state, $ship_to_country_code, $ship_to_zip, $ship_to_street2, $phone_number ) {

        // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
        $nvpstr  = '&PAYMENTREQUEST_0_AMT='               . $payment_amount;
        $nvpstr .= '&PAYMENTREQUEST_0_PAYMENTACTION='     . $this->payment_type;
        $nvpstr .= '&RETURNURL='                          . $this->return_url;
        $nvpstr .= '&CANCELURL='                          . $this->cancel_url;
        $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE='      . $this->currency_code_type;
        $nvpstr .= '&ADDROVERRIDE=1';
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTONAME='        . $ship_to_name;
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOSTREET='      . $ship_to_street;
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOSTREET2='     . $ship_to_street2;
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOCITY='        . $ship_to_city;
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOSTATE='       . $ship_to_state;
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=' . $ship_to_country_code;
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOZIP='         . $ship_to_zip;
        $nvpstr .= '&PAYMENTREQUEST_0_SHIPTOPHONENUM='    . $phone_number;

        $_SESSION['currency_code_type'] = $this->currency_code_type;
        $_SESSION['payment_type']       = $this->payment_type;

        /*
         * Make the API call to PayPal
         * If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.
         * If an error occured, show the resulting errors
         */
        $result = $this->hash_call( 'SetExpressCheckout', $nvpstr );
        $ack    = strtoupper( $result['ACK'] );

        if ( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' ) {
            $token = urldecode( $result['TOKEN'] );
            $_SESSION['token'] = $token;
            /* Construct URL and redirect to PayPal */
            $paypal_url = $this->paypal_url . $result['TOKEN'];
            wp_redirect( $paypal_url );
        } else {
            /* Display a user friendly Error on the page using any of the following error information returned by PayPal */
            $error_call          = 'SetExpressCheckout';
            $error_code          = urldecode( $result['L_ERRORCODE0'] );
            $error_short_msg     = urldecode( $result['L_SHORTMESSAGE0'] );
            $error_long_msg      = urldecode( $result['L_LONGMESSAGE0'] );
            $error_severity_code = urldecode( $result['L_SEVERITYCODE0'] );
            /* Build error messages array */
            $result = array(
                'error_call'          => $error_call,
                'error_code'          => $error_code,
                'error_short_msg'     => $error_short_msg,
                'error_long_msg'      => $error_long_msg,
                'error_severity_code' => $error_severity_code );
            /* Set status and return error */
            $result['status'] = 'error';
            return $result;
        }
    }

    /**
     * Prepares the parameters for the GetExpressCheckoutDetails API Call.
     * 
     * @return The NVP Collection object of the GetExpressCheckoutDetails Call Response.
     */
    function get_shipping_details() {

        /* Check to see if the Request object contains a variable named 'token' */
        if ( isset( $_REQUEST['token'] ) )
            $token = $_REQUEST['token'];

        /* If the Request object contains the variable 'token' then it means that the user is coming from PayPal site. */
        if ( isset( $token ) ) {

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
            $result = $this->hash_call( 'GetExpressCheckoutDetails', $nvpstr );
            $ack    = strtoupper( $result['ACK'] );

            if ( $ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING' ) {
                $_SESSION['payer_id'] =	$result['PAYERID'];
                
                /*
                 * The information that is returned by the GetExpressCheckoutDetails
                 * call should be integrated by the partner into his Order Review page
                 */
                $email                = $result["EMAIL"]; // ' Email address of payer.
                $payer_id             = $result["PAYERID"]; // ' Unique PayPal customer account identification number.
                $payer_status         = $result["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
                $salutation           = $result["SALUTATION"]; // ' Payer's salutation.
                $first_name           = $result["FIRSTNAME"]; // ' Payer's first name.
                $middle_name          = $result["MIDDLENAME"]; // ' Payer's middle name.
                $last_name            = $result["LASTNAME"]; // ' Payer's last name.
                $suffix               = $result["SUFFIX"]; // ' Payer's suffix.
                $country_code         = $result["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
                $business             = $result["BUSINESS"]; // ' Payer's business name.
                $ship_to_name         = $result["SHIPTONAME"]; // ' Person's name associated with this address.
                $ship_to_street       = $result["SHIPTOSTREET"]; // ' First street address.
                $ship_to_street2      = $result["SHIPTOSTREET2"]; // ' Second street address.
                $ship_to_city         = $result["SHIPTOCITY"]; // ' Name of city.
                $ship_to_state        = $result["SHIPTOSTATE"]; // ' State or province
                $ship_to_country_code = $result["SHIPTOCOUNTRYCODE"]; // ' Country code.
                $ship_to_zip          = $result["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
                $address_status       = $result["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal
                $invoice_number       = $result["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
                $phone_number         = $result["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one.

                $result['status'] = 'success';
                return $result;
                
            } else {
                /* Display a user friendly Error on the page using any of the following error information returned by PayPal */
                $error_call          = 'GetExpressCheckoutDetails';
                $error_code          = urldecode( $result['L_ERRORCODE0'] );
                $error_short_msg     = urldecode( $result['L_SHORTMESSAGE0'] );
                $error_long_msg      = urldecode( $result['L_LONGMESSAGE0'] );
                $error_severity_code = urldecode( $result['L_SEVERITYCODE0'] );
                /* Build error messages array */
                $result = array(
                    'error_call'          => $error_call,
                    'error_code'          => $error_code,
                    'error_short_msg'     => $error_short_msg,
                    'error_long_msg'      => $error_long_msg,
                    'error_severity_code' => $error_severity_code );
                /* Set status and return error */
                $result['status'] = 'error';
                return $result;
            }
        }
    }

    /**
     * Prepares the parameters for the GetExpressCheckoutDetails API Call.
     *
     * @param $final_payment_amt
     * @return array $result The NVP Collection object of the GetExpressCheckoutDetails Call Response.
     **/
    function confirm_payment( $final_payment_amt ) {
        /*
         * Gather the information to make the final call to finalize the PayPal payment.
         * The variable nvpstr holds the name value pairs
         *
         * Format the other parameters that were stored in the session from the previous calls 
         */
        $token              = urlencode( $_SESSION['token'] );
        $payment_type       = urlencode( $_SESSION['payment_type'] );
        $currency_code_type = urlencode( $_SESSION['currency_code_type'] );
        $payer_id           = urlencode( $_SESSION['payer_id'] );
        $server_name        = urlencode( $_SERVER['SERVER_NAME'] );

        $nvpstr = '&TOKEN='                          . $token .
                  '&PAYERID='                        . $payer_id .
                  '&PAYMENTREQUEST_0_PAYMENTACTION=' . $payment_type .
                  '&PAYMENTREQUEST_0_AMT='           . $final_payment_amt .
                  '&PAYMENTREQUEST_0_CURRENCYCODE='  . $currency_code_type .
                  '&IPADDRESS='                      . $server_name;

        /*
         * Make the call to PayPal to finalize payment.
         * If an error occured, show the resulting errors
         */
        $result = $this->hash_call( 'DoExpressCheckoutPayment', $nvpstr );

        /*
         * Display the API response back to the browser.
         * If the response from PayPal was a success, display the response parameters.
         * If the response was an error, display the errors received using APIError.php.
         */
        $ack = strtoupper( $result['ACK'] );

        if ( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" ) {

            /*
             * THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE
             * transactionId & orderTime
             * IN THEIR OWN  DATABASE
             * AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT
             */
            $transaction_id   = $result["TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs.
            $transaction_type = $result["TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout
            $payment_type     = $result["PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant
            $order_time       = $result["ORDERTIME"];  //' Time/date stamp of payment
            $amt              = $result["AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
            $currency_code    = $result["CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
            $fee_amt          = $result["FEEAMT"];  //' PayPal fee amount charged for the transaction
            $settle_amt       = $result["SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
            $tax_amt          = $result["TAXAMT"];  //' Tax charged on the transaction.
            $exchange_rate    = $result["EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.

            /*
             * Status of the payment:
             *
             * 'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
             * 'Pending: The payment is pending. See the PendingReason element for more information.
             */
            $payment_status = $result["PAYMENTSTATUS"];

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
            $pending_reason = $result["PENDINGREASON"];

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
            $reason_code = $result["REASONCODE"];

            /* Set status and return result */
            $result['status'] = 'success';
            return $result;
            
        } else {
            /* Display a user friendly Error on the page using any of the following error information returned by PayPal */
            $error_call          = 'DoExpressCheckoutPayment';
            $error_code          = urldecode( $result['L_ERRORCODE0'] );
            $error_short_msg     = urldecode( $result['L_SHORTMESSAGE0'] );
            $error_long_msg      = urldecode( $result['L_LONGMESSAGE0'] );
            $error_severity_code = urldecode( $result['L_SEVERITYCODE0'] );
            /* Build error messages array */
            $result = array(
                'error_call'          => $error_call,
                'error_code'          => $error_code,
                'error_short_msg'     => $error_short_msg,
                'error_long_msg'      => $error_long_msg,
                'error_severity_code' => $error_severity_code );
            /* Set status and return error */
            $result['status'] = 'error';
            return $result;
        }
    }

    /**
     * This function makes a DoDirectPayment API call
     *
     * @param $payment_amount     Total value of the shopping cart
     * @param $first_name         First name as it appears on credit card
     * @param $last_name          Last name as it appears on credit card
     * @param $street             Buyer's street address line as it appears on credit card
     * @param $city               Buyer's city
     * @param $state              Buyer's state
     * @param $country_code       Buyer's country code
     * @param $zip                Buyer's zip
     * @param $credit_card_type   Buyer's credit card type (i.e. Visa, MasterCard ... )
     * @param $credit_card_number Buyers credit card number without any spaces, dashes or any other characters
     * @param $exp_date           Credit card expiration date.
     * @param $cvv2               Card Verification Value
     *
     * @return array $result The NVP Collection object of the DoDirectPayment Call Response.
     **/
    function direct_payment( $payment_amount, $credit_card_type, $credit_card_number, $exp_date, $cvv2, $first_name, $last_name, $street, $city, $state, $zip, $country_code ) {

        //Construct the parameter string that describes DoDirectPayment
        $nvpstr  = '&AMT='            . urlencode( $payment_amount );
        $nvpstr .= '&CURRENCYCODE='   . urlencode( $this->currency_code_type );
        $nvpstr .= '&PAYMENTACTION='  . urlencode( $this->payment_type );
        $nvpstr .= '&CREDITCARDTYPE=' . urlencode( $credit_card_type );
        $nvpstr .= '&ACCT='           . urlencode( $credit_card_number );
        $nvpstr .= '&EXPDATE='        . urlencode( $exp_date );
        $nvpstr .= '&CVV2='           . urlencode( $cvv2 );
        $nvpstr .= '&FIRSTNAME='      . urlencode( $first_name );
        $nvpstr .= '&LASTNAME='       . urlencode( $last_name );
        $nvpstr .= '&STREET='         . urlencode( $street );
        $nvpstr .= '&CITY='           . urlencode( $city );
        $nvpstr .= '&STATE='          . urlencode( $state );
        $nvpstr .= '&ZIP='            . urlencode( $zip );
        $nvpstr .= '&COUNTRYCODE='    . urlencode( $country_code );
        $nvpstr .= '&IPADDRESS='      . urlencode( $_SERVER['REMOTE_ADDR'] );

        $result = $this->hash_call( 'DoDirectPayment', $nvpstr );
        $ack    = strtoupper( $result["ACK"] );

        if ( $ack=='SUCCESS' || $ack=='SUCCESSWITHWARNING' ) {
            //Getting transaction ID from API responce.
            $transaction_id = urldecode( $result["TRANSACTIONID"] );
            /* Set status and return result */
            $result['status'] = 'success';
            return $result;
            
        } else {
            /* Display a user friendly Error on the page using any of the following error information returned by PayPal */
            $error_call          = 'DoDirectPayment';
            $error_code          = urldecode( $result['L_ERRORCODE0'] );
            $error_short_msg     = urldecode( $result['L_SHORTMESSAGE0'] );
            $error_long_msg      = urldecode( $result['L_LONGMESSAGE0'] );
            $error_severity_code = urldecode( $result['L_SEVERITYCODE0'] );
            /* Build error messages array */
            $result = array(
                'error_call'          => $error_call,
                'error_code'          => $error_code,
                'error_short_msg'     => $error_short_msg,
                'error_long_msg'      => $error_long_msg,
                'error_severity_code' => $error_severity_code );
            /* Set status and return error */
            $result['status'] = 'error';
            return $result;
        }
    }

    /**
     * Function to perform the API call to PayPal using API signature
     *
     * @param string $method_name is name of API  method.
     * @param string $nvpstr is nvp string.
     *
     * @return array $nvpResArray Associtive array containing the response from the server.
     **/
    function hash_call( $method_name, $nvpstr ) {
        /* Setting the curl parameters */
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $this->api_endpoint );
        curl_setopt( $ch, CURLOPT_VERBOSE, 1);
        /* Turning off the server and peer verification ( TrustManager Concept ). */
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        /* If use_proxy var is set to TRUE, then only proxy will be enabled. */
        if ( $this->use_proxy )
            curl_setopt( $ch, CURLOPT_PROXY, $this->proxy_host . ':' . $this->proxy_port );
        
        /* NVPRequest for submitting to server */
        $nvpreq = 'METHOD='        . urlencode( $method_name ) .
                  '&VERSION='      . urlencode( $this->version ) .
                  '&PWD='          . urlencode( $this->api_password ) .
                  '&USER='         . urlencode( $this->api_username ) .
                  '&SIGNATURE='    . urlencode( $this->api_signature ) . $nvpstr .
                  '&BUTTONSOURCE=' . urlencode( $this->sbn_code );
        
        /* Setting the nvpreq as POST FIELD to curl */
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $nvpreq );
        /* Getting response from server */
        $response = curl_exec( $ch );
        /* Convrting NVPResponse to an Associative Array */
        $nvp_response = $this->deformat_nvp( $response );
        $nvp_request  = $this->deformat_nvp( $nvpreq );
        $_SESSION['nvp_request'] = $nvp_request;

        if ( curl_errno( $ch )) {
            /* Moving to display page to display curl errors */
            $_SESSION['curl_error_no']  = curl_errno( $ch );
            $_SESSION['curl_error_msg'] = curl_error( $ch );
            /* Execute the Error handling module to display errors. */
        } else {
            /* Closing the curl */
            curl_close( $ch );
        }
        return $nvp_response;
    }

    /**
     * This function will take NVPString and convert it to an Associative Array and it will decode the response.
     * It is usefull to search for a particular key and displaying arrays.
     *
     * @param  $nvpstr is NVPString.
     * @return array $nvp_array
     **/
    function deformat_nvp( $nvpstr ) {
        $intial = 0;
        $nvp_array = array();

        while( strlen( $nvpstr ) ) {
            /* postion of Key */
            $keypos = strpos( $nvpstr, '=' );

            /* position of value */
            $valuepos = strpos( $nvpstr, '&' ) ? strpos( $nvpstr, '&' ) : strlen( $nvpstr );

            /* Getting the Key and Value values and storing in a Associative Array */
            $keyval = substr( $nvpstr, $intial, $keypos );
            $valval = substr( $nvpstr, $keypos + 1, $valuepos - $keypos - 1 );

            /* decoding the respose */
            $nvp_array[urldecode( $keyval )] = urldecode( $valval );
            $nvpstr = substr( $nvpstr, $valuepos + 1, strlen( $nvpstr ));
        }
        return $nvp_array;
    }
}
endif;
?>