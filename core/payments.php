<?php

/**
 * DR_Payments
 * Payments Core Class. Handles requests and defines common utility functions.
 *
 * @uses DR_Core
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh}
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class DR_Payments extends DR_Core {

    /** @var object The PayPal API Module object */
    var $paypal_express_gateway;
	// var $options_name = 'module_payments';

    /**
     * Constructor.
     */
    function DR_Payments() {
        register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'init_default_options' ) );
        // Initiate default payment settings
        add_action( 'init', array( &$this, 'init_default_options' ) );
        // Handle all requests for checkout
        add_action( 'template_redirect', array( &$this, 'handle_checkout_requests' ) );
    }

    /**
     * Init data for submit site.
     *
     * @return <type>
     */
    function init_default_options() {
        $options = $this->get_options();

        if ( empty( $options ) ) {
            $defaults = array(
                'payment_settings' => array(
                    'recurring_cost'    => '9.99',
                    'recurring_name'    => 'Subscription',
                    'billing_period'    => 'Month',
                    'billing_frequency' => '1',
                    'billing_agreement' => 'Customer will be billed at “9.99 per month for 2 years”',
                    'one_time_cost'     => '99.99',
                    'one_time_name'     => 'One Time Only',
                    'tos_content'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at sem libero. Pellentesque accumsan consequat porttitor. Curabitur ut lorem sed ipsum laoreet tempus at vel erat. In sed tempus arcu. Quisque ut luctus leo. Nulla facilisi. Sed sodales lectus ut tellus venenatis ac convallis metus suscipit. Vestibulum nec orci ut erat ultrices ullamcorper nec in lorem. Vivamus mauris velit, vulputate eget adipiscing elementum, mollis ac sem. Aliquam faucibus scelerisque orci, ut venenatis massa lacinia nec. Phasellus hendrerit lorem ornare orci congue elementum. Nam faucibus urna a purus hendrerit sit amet pulvinar sapien suscipit. Phasellus adipiscing molestie imperdiet. Mauris sit amet justo massa, in pellentesque nibh. Sed congue, dolor eleifend egestas egestas, erat ligula malesuada nulla, sit amet venenatis massa libero ac lacus. Vestibulum interdum vehicula leo et iaculis.',
                    'key'               => 'settings'
                ),
                'paypal' => array(
                    'api_url'       => 'sandbox',
                    'api_username'  => '',
                    'api_password'  => '',
                    'api_signature' => '',
                    'currency'      => 'USD',
                    'key'           => 'paypal'
            ));

            update_option( $this->options_name, $defaults );
        }
    }

    /**
     * Update user information. This method handles both add and update
     * operations.
     *
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $billing The billing type for the user
     * @return NULL|void
     */
    function update_user( $user_login, $user_pass, $user_email, $first_name, $last_name, $billing_type, $transaction_details, $credits = '' ) {
        // Include registration helper functions
        require_once( ABSPATH . WPINC . '/registration.php' );

        // If user exists, proceed accordingly
        if ( email_exists( $user_email )) {

            $user = get_user_by( 'email', $user_email );

            if ( !empty( $user ) ) {

                // Set new user role
                wp_update_user( array( 'ID' => $user->ID ) );

                // Set payment details ( transaction ID's and recurring payment profile ID )
                $this->update_user_payment_details( $user->ID, $billing_type, $transaction_details );

                // Set login credentials and sign user
                // $credentials = array( 'remember' => true, 'user_login' => $user->user_login, 'user_password' => $user->user_pass );
                // $result = wp_signon( $credentials );

                return;
            }
        }
        // Else new user, proceed accordingly
        else {

//            $user_login = sanitize_user( strtolower( $first_name ));
            if ( "" == $user_pass )
                $user_pass  = wp_generate_password();

            if ( username_exists( $user_login ) ) {
                $user_login .= '-' . sanitize_user( strtolower( $last_name ));
                if ( username_exists( $user_login ) )
                    $user_login = $user_email;
            }

            $user_id = wp_insert_user( array(
                'user_login'    => $user_login,
                'user_pass'     => $user_pass,
                'user_email'    => $user_email,
                'display_name'  => $first_name . ' ' . $last_name,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'role'          => "directory_member"
            ));

            // If user account created successfully, proceed
            if ( !empty( $user_id ) ) {

                // Set payment details ( transaction ID's and recurring payment profile ID )
                $this->update_user_payment_details( $user_id, $billing_type, $transaction_details );

                // $this->update_user_credits( $credits, $user_id );
                wp_new_user_notification( $user_id, $user_pass );
                $credentials = array( 'remember' => true, 'user_login' => $user_login, 'user_password' => $user_pass );
                wp_signon( $credentials );
            }
        }
    }

    /**
     * Set payment details for user in DB
     * ( transaction ID's and recurring payment profile ID )
     *
     * @param string|int $user_id
     * @param string $billing_type
     * @param array $transaction_details
     * @access public
     * @return void
     */
    function update_user_payment_details( $user_id, $billing_type, $transaction_details ) {

        $user_payment_details = get_user_meta( $user_id, $this->options_name, true );

        // If user payment details is not populated, update if with a default structure
        if ( !$user_payment_details ) {
            $user_payment_details_structure = array(
                'paypal' => array(
                    'transactions' => array(),
                    'profile_id'   => ''
            ));
            update_user_meta( $user_id, $this->options_name, $user_payment_details_structure );
            $user_payment_details = $user_payment_details_structure;
        }

        // Update user recurring profile ID
        if ( isset( $transaction_details['PROFILEID'] ) ) {
            $user_payment_details['paypal']['profile_id'] = $transaction_details['PROFILEID'];
            update_user_meta( $user_id, $this->options_name, $user_payment_details );
            return;
        }

        // Set transaction ID, the different API calls return different array
        // keys so we need to check all of them
        if ( isset( $transaction_details['PAYMENTINFO_0_TRANSACTIONID'] ) ) {
            $transaction_id = $transaction_details['PAYMENTINFO_0_TRANSACTIONID'];
        } elseif ( isset( $transaction_details['TRANSACTIONID'] ) ) {
            $transaction_id = $transaction_details['TRANSACTIONID'];
        }

        // If we have a valid transaction ID lets add it to our DB entries
        if ( !empty( $transaction_id ) ) {
            array_push( $user_payment_details['paypal']['transactions'], $transaction_id );
            update_user_meta( $user_id, $this->options_name, $user_payment_details );
        }
    }

    /**
     * Handle all checkout requests.
     *
     * @uses session_start() We need to keep track of some session variables for the checkout
     * @return NULL If the payment gateway options are not configured.
     */
    function handle_checkout_requests() {
        // Only handle request if on the proper page
        if ( is_dr_page('signup') ) {

			// Redirect if user is logged in
			if ( is_user_logged_in() ) {
				wp_redirect( get_bloginfo('url') );
				exit;
			}

            $options = $this->get_options();

			include_once $this->plugin_dir . 'core/paypal-express-gateway.php';

			$this->paypal_express_gateway = new Paypal_Express_Gateway( $options['paypal'] );

            // We need to use session variables during the checkout process
            if ( !session_id() )
                session_start();

            // If no PayPal API credentials are set, disable the checkout process
            if ( empty( $options['paypal'] ) ) {
                // Set the proper step which will be loaded by "page-checkout.php"
                set_query_var( 'checkout_step', 'disabled' );
                return;
            }

            // If Terms and Costs step is submitted
            if ( isset( $_POST['terms_submit'] )) {

                // Validate fields
                if ( empty( $_POST['tos_agree'] ) || empty( $_POST['billing_type'] ) ) {

                    if ( empty( $_POST['tos_agree'] ))
                        add_action( 'tos_invalid', create_function('', 'echo "class=\"error\"";') );
                    if ( empty( $_POST['billing_type'] ))
                        add_action( 'billing_invalid', create_function('', 'echo "class=\"error\"";') );

                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'terms' );

                } else {
                    // Set session variables
                    $_SESSION['billing_type'] = $_POST['billing_type'];
                    if ( $_SESSION['billing_type'] == 'recurring' ) {
                        $_SESSION['cost']              = $_POST['recurring_cost'];
                        $_SESSION['billing_agreement'] = $options['payment_settings']['billing_agreement'];
                        $_SESSION['billing_period']    = $options['payment_settings']['billing_period'];
                        $_SESSION['billing_frequency'] = $options['payment_settings']['billing_frequency'];
                    }
                    else {
                        $_SESSION['cost'] = $_POST['one_time_cost'];
                    }

                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'payment_method' );

                }
            }

            // If payment method is selected and submitted
            elseif ( isset( $_POST['payment_method_submit'] ) ) {

                // Validate fields
                if ( empty( $_POST['payment_method'] ) ) {

                    add_action( 'pm_invalid', create_function('', 'echo "class=\"error\"";') );

                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'payment_method' );

                } else {

                    if ( $_POST['payment_method'] == 'paypal' ) {

                        // If recuring payment selected pass '0' so we can void the direct payment
                        $cost = $_SESSION['billing_type'] == 'recurring' ? 0 : $_SESSION['cost'];
                        $billing_agreement = $_SESSION['billing_type'] == 'recurring' ? $_SESSION['billing_agreement'] : null;

                        // Make API call
                        $result = $this->paypal_express_gateway->call_shortcut_express_checkout(
                            $cost,
                            $_SESSION['billing_type'],
                            $billing_agreement
                        );

                        // Handle Error scenarios
                        if ( $result['status'] == 'error' ) {
                            // Set the proper step which will be loaded by "page-checkout.php"
                            set_query_var( 'checkout_step', 'api_call_error' );
                            // Pass error params to "page-checkout.php"
                            set_query_var( 'checkout_error', $result );

                            // Destroys the $_SESSION
                            $this->destroy_session();
                        }

                    } elseif ( $_POST['payment_method'] == 'cc' ) {
                        // Set the proper step which will be loaded by "page-checkout.php"
                        set_query_var( 'checkout_step', 'cc_details' );
                    }
                }
            }

            // If direct CC payment is submitted
            elseif ( isset( $_POST['direct_payment_submit'] ) ) {

                // Make API call
                $result = $this->paypal_express_gateway->direct_payment(
                    $_POST['total_amount'],
                    $_POST['cc_type'],
                    $_POST['cc_number'],
                    $_POST['exp_date_month'] . $_POST['exp_date_year'],
                    $_POST['cvv2'],
                    $_POST['first_name'],
                    $_POST['last_name'],
                    $_POST['street'],
                    $_POST['city'],
                    $_POST['state'],
                    $_POST['zip'],
                    $_POST['country_code']
                );

                // Handle Success and Error scenarios
                if ( $result['status'] == 'success' ) {
                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'direct_payment' );
                } else {
                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'api_call_error' );
                    // Pass error params to "page-checkout.php"
                    set_query_var( 'checkout_error', $result );

                    // Destroys the $_SESSION
                    $this->destroy_session();
                }
            }

            // If PayPal has redirected us back with the proper TOKEN
            elseif ( isset( $_REQUEST['token'] )
                && !isset( $_POST['confirm_payment_submit'] )
                && !isset( $_POST['redirect_my_classifieds'] ) ) {

                $_SESSION['token'] = $_REQUEST['token'];

                // Make API call
                $result = $this->paypal_express_gateway->get_express_checkout_details( $_SESSION['token'] );

                // Handle Success and Error scenarios
                if ( $result['status'] == 'success' ) {
                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'confirm_payment' );
                    // Pass transaction details params to "page-checkout.php"
                    set_query_var( 'checkout_transaction_details', $result );
                } else {
                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'api_call_error' );
                    // Pass error params to "page-checkout.php"
                    set_query_var( 'checkout_error', $result );

                    // Destroys the $_SESSION
                    $this->destroy_session();
                }
            }

            // If payment confirmation is submitted
            elseif ( isset( $_POST['confirm_payment_submit'] ) ) {

                if ( username_exists( $_POST['login'] ) ) {
                    $result =  unserialize( base64_decode( $_POST['result'] ) );
                    $result['login'] = $_POST['login'];
                    $result['confirm_error'] = __( 'Login incorrect!', $text_domain );;
                    set_query_var( 'checkout_transaction_details', $result );
                    set_query_var( 'checkout_step', 'confirm_payment' );
                } else {
                    if ( $_SESSION['billing_type'] == 'recurring' ) {

                        // Make CreateRecurringPaymentsProfile API call
                        $result = $this->paypal_express_gateway->create_recurring_payments_profile(
                            $_SESSION['cost'],
                            $_SESSION['billing_period'],
                            $_SESSION['billing_frequency'],
                            $_SESSION['billing_agreement']
                        );

                    } else {
                        // Make DoExpressCheckout API call
                        $result = $this->paypal_express_gateway->do_express_checkout_payment( $_POST['total_amount'] );
                    }

                    // Handle Success and Error scenarios
                    if ( $result['status'] == 'success' ) {

                        // Insert/Update User
                        $this->update_user(
                            $_POST['login'],
                            $_POST['password'],
                            $_POST['email'],
                            $_POST['first_name'],
                            $_POST['last_name'],
                            $_POST['billing_type'],
                            $result
                            // $_POST['credits']
                        );

                        // Set the proper step which will be loaded by "page-checkout.php"
                        set_query_var( 'checkout_step', 'success' );

                        // Destroys the $_SESSION
                        $this->destroy_session();

                    } else {
                        // Set the proper step which will be loaded by "page-checkout.php"
                        set_query_var( 'checkout_step', 'api_call_error' );
                        // Pass error params to "page-checkout.php"
                        set_query_var( 'checkout_error', $result );

                        // Destroys the $_SESSION
                        $this->destroy_session();
                    }
                }
            }

            // If login attempt is made
            elseif ( isset( $_POST['login_submit'] ) ) {

                if ( isset( $this->login_error )) {
                    add_action( 'login_invalid', create_function('', 'echo "class=\"error\"";') );
                    // Set the proper step which will be loaded by "page-checkout.php"
                    set_query_var( 'checkout_step', 'terms' );
                    // Pass error params to "page-checkout.php"
                    set_query_var( 'checkout_error', $this->login_error );
                } else {
                    wp_redirect( get_bloginfo('url') );
                    exit;
                }
            }

            // If no requests are made load default step
            else {
                // Set the proper step which will be loaded by "page-checkout.php"
                set_query_var( 'checkout_step', 'terms' );
            }
        }
    }

    /**
     * Destroy $_SESSION
     *
     * @access public
     * @return void
     */
    function destroy_session() {
        // Unset all of the session variables.
        $_SESSION = array();

        // Destroy the session cookie, and not just the session data!
        if ( ini_get("session.use_cookies" ) ) {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }
}

/* Initiate Payments */
new DR_Payments();

