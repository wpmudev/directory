<?php

if ( !class_exists('Directory_Gateway_Athorizenet') ):
class Directory_Gateway_Athorizenet
{
    //var $admin_options_name = "ShareADraft_options";
    
    // By default, this sample code is designed to post to our test server for
    // developer accounts: https://test.authorize.net/gateway/transact.dll
    // for real accounts (even in test mode), please make sure that you are
    // posting to: https://secure.authorize.net/gateway/transact.dll
    var $post_url = "https://test.authorize.net/gateway/transact.dll";

    function Directory_Gateway_Athorizenet() {
        add_action( 'init', array( &$this, 'init' ) );
    }

    function init() {
        //$this->options
    }

    function sample_code() {
        $post_values = array(
            // the API Login ID and Transaction Key must be replaced with valid values
            "x_login"			=> '7qYqxp266Fk',
            "x_tran_key"		=> '76ck9mLL55Be899p',

            "x_version"			=> "3.1",
            "x_delim_data"		=> "TRUE",
            "x_delim_char"		=> "|",
            "x_relay_response"	=> "FALSE",

            "x_type"			=> "AUTH_CAPTURE",
            "x_method"			=> "CC",
            "x_card_num"		=> "4111111111111111",
            "x_exp_date"		=> "0115",

            "x_amount"			=> "19.99",
            "x_description"		=> "Sample Transaction",

            "x_first_name"		=> "John",
            "x_last_name"		=> "Doe",
            "x_address"			=> "1234 Street",
            "x_state"			=> "WA",
            "x_zip"				=> "98004"
            // Additional fields can be added here as outlined in the AIM integration
            // guide at: http://developer.authorize.net
        );

        // This section takes the input fields and converts them to the proper format
        // for an http post.  For example: "x_login=username&x_tran_key=a1B2c3D4"
        $post_string = "";
        foreach( $post_values as $key => $value )
            { $post_string .= "$key=" . urlencode( $value ) . "&"; }
        $post_string = rtrim( $post_string, "& " );

        // The following section provides an example of how to add line item details to
        // the post string.  Because line items may consist of multiple values with the
        // same key/name, they cannot be simply added into the above array.
        //
        // This section is commented out by default.
        /*
        $line_items = array(
            "item1<|>golf balls<|><|>2<|>18.95<|>Y",
            "item2<|>golf bag<|>Wilson golf carry bag, red<|>1<|>39.99<|>Y",
            "item3<|>book<|>Golf for Dummies<|>1<|>21.99<|>Y");

        foreach( $line_items as $value )
            { $post_string .= "&x_line_item=" . urlencode( $value ); }
        */

        // This sample code uses the CURL library for php to establish a connection,
        // submit the post, and record the response.
        // If you receive an error, you may want to ensure that you have the curl
        // library enabled in your php configuration
        $request = curl_init($post_url); // initiate curl object
            curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
            $post_response = curl_exec($request); // execute curl post and store results in $post_response
            // additional options may be required depending upon your server configuration
            // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close ($request); // close curl object

        // This line takes the response and breaks it into an array using the specified delimiting character
        $response_array = explode($post_values["x_delim_char"],$post_response);

        // The results are output to the screen in the form of an html numbered list.
        echo "<OL>\n";
        foreach ($response_array as $value)
        {
            echo "<LI>" . $value . "&nbsp;</LI>\n";
            $i++;
        }
        echo "</OL>\n";
        // individual elements of the array could be accessed to read certain response
        // fields.  For example, response_array[0] would return the Response Code,
        // response_array[2] would return the Response Reason Code.
        // for a list of response fields, please review the AIM Implementation Guide
    }

    function save_admin_options() {
        if ( isset( $_POST['authorize_net_save'] )) {
            
            
        }
    }

    function get_admin_options() {
        
    }

    function output_admin_page() {
        
    }
}
endif;

if ( class_exists('Directory_Gateway_Athorizenet') )
	$_directory_gateway_athorizenet = new Directory_Gateway_Athorizenet();

?>
