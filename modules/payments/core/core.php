<?php

if ( !class_exists('Payments_Core') ):

/**
 * Payments Core Class. Handles requests and defines common utility functions.
 *
 * @package Payments
 * @version 1.0.0 
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class Payments_Core {

    /** @var string Url to the submodule directory */
    var $module_url = PG_MODULE_URL;
    /** @var string Path to the submodule directory */
    var $module_dir = PG_MODULE_DIR;
    /** @var object The PayPal API Module object */
    var $paypal_api_module;
    /** @var string The passed plugin admin slug ( admin.php?page=[slug] ) */
    var $admin_page_slug;
    /** @var string Text domain string */
    var $text_domain = 'payments';
    /** @var string Payments module options name */
    var $options_name = 'module_payments';   
    /** @var string Payments module options name */
    var $user_role;   

    /**
     * Constructor.
     */
    function Payments_Core( $admin_page_slug, $user_role ) {
        $this->init();
        $this->init_vars( $admin_page_slug, $user_role );
    }
    
    /**
     * Hook class methods into WordPress 
     * 
     * @access public
     * @return void
     */
    function init() {
        // Initiate default payment settings 
        add_action( 'init', array( &$this, 'init_payments_default_settings' ) );
        // Create neccessary pages 
        add_action( 'wp_loaded', array( &$this, 'create_default_pages' ) );
        // Handle all requests for checkout 
        add_action( 'template_redirect', array( &$this, 'handle_checkout_requests' ) );
        // Handle all requests for checkout 
        add_action( 'handle_module_admin_requests', array( &$this, 'handle_admin_requests' ) );
        // Render module admin navigation
        add_action( 'render_admin_navigation_tabs', array( &$this, 'render_admin_navigation_tabs' ) );
        add_action( 'render_admin_navigation_subs', array( &$this, 'render_admin_navigation_subs' ) );
    }

    /**
     * Initiate class variables.
     *
     * @return void
     **/
    function init_vars( $admin_page_slug, $user_role ) {
        $this->user_role = $user_role;
        $this->admin_page_slug = $admin_page_slug;

        $options = $this->get_options( 'paypal' );
        $this->paypal_api_module = new PayPal_API_Module( $options );
    }

    /**
     * Init data for submit site.
     *
     * @return <type>
     * @todo Move to Payments Module
     */
    function init_payments_default_settings() {

        $user = wp_get_current_user();
        $usermeta = get_user_meta( 5, $this->options_name, true );
        // var_dump($usermeta);

        $options = $this->get_options( $this->options_name );
        if ( !isset( $options['checkout'] ) ) {
            $checkout_settings = array( 'checkout' => array(
                'annual_price'   => 10,
                'annual_txt'     => 'Annually Charge',
                'one_time_price' => 50,
                'one_time_txt'   => 'One-Time Only Charge',
                'tos_txt'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at sem libero. Pellentesque accumsan consequat porttitor. Curabitur ut lorem sed ipsum laoreet tempus at vel erat. In sed tempus arcu. Quisque ut luctus leo. Nulla facilisi. Sed sodales lectus ut tellus venenatis ac convallis metus suscipit. Vestibulum nec orci ut erat ultrices ullamcorper nec in lorem. Vivamus mauris velit, vulputate eget adipiscing elementum, mollis ac sem. Aliquam faucibus scelerisque orci, ut venenatis massa lacinia nec. Phasellus hendrerit lorem ornare orci congue elementum. Nam faucibus urna a purus hendrerit sit amet pulvinar sapien suscipit. Phasellus adipiscing molestie imperdiet. Mauris sit amet justo massa, in pellentesque nibh. Sed congue, dolor eleifend egestas egestas, erat ligula malesuada nulla, sit amet venenatis massa libero ac lacus. Vestibulum interdum vehicula leo et iaculis.'
            ));
            $options = array_merge( $options, $checkout_settings );
            update_option( $this->options_name, $options );
            return;
        }
    }

    /**
     * Create the default pages.
     *
     * @return void
     */
    function create_default_pages() {
        $page['checkout'] = get_page_by_title('Checkout');
        if ( !isset( $page['checkout'] ) ) {
            $current_user = wp_get_current_user();
            /* Construct args for the new post */
            $args = array(
                'post_title'     => 'Checkout',
                'post_status'    => 'publish',
                'post_author'    => $current_user->ID,
                'post_type'      => 'page',
                'ping_status'    => 'closed',
                'comment_status' => 'closed'
            );
            wp_insert_post( $args );
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
    function update_user( $user_email, $first_name, $last_name, $billing_type, $transaction_details, $credits = '' ) {
        // Include registration helper functions  
        require_once( ABSPATH . WPINC . '/registration.php' );

        // If user exists, proceed accordingly
        if ( email_exists( $user_email )) {

            $user = get_user_by( 'email', $user_email );

            if ( !empty( $user ) ) {

                // Set new user role
                wp_update_user( array( 'ID' => $user->ID, 'role' => $this->user_role ) ); 

                // Set payment details ( transaction ID's and recurring payment profile ID ) 
                $this->update_user_payment_details( $user->ID, $billing_type, $transaction_details );

                // Set login credentials and sign user
                $credentials = array( 'remember' => true, 'user_login' => $user->user_login, 'user_password' => $user->user_pass );
                wp_signon( $credentials );

                return;
            }
        } 
        // Else new user, proceed accordingly 
        else {

            $user_login = sanitize_user( strtolower( $first_name ));
            $user_pass  = wp_generate_password();

            if ( username_exists( $user_login ) ) {
                $user_login .= '-' . sanitize_user( strtolower( $last_name ));
                if ( username_exists( $user_login ) )
                    $user_login = $user_email;
            }

            $user_id = wp_insert_user( array(
                'user_login'   => $user_login,
                'user_pass'    => $user_pass,
                'user_email'   => $user_email,
                'display_name' => $first_name . ' ' . $last_name,
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'role'         => $this->user_role
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
     * Handle the admin requests for the payments module.
     */
    function handle_admin_requests() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->admin_page_slug ) {
            if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) {
                if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'authorizenet' ) {
                    $this->render_admin( 'payments-authorizenet' );
                } elseif ( isset( $_GET['sub'] ) && $_GET['sub'] == 'paypal' ) {
                    /* Save options */
                    if ( isset( $_POST['save'] ) ) {
                        $this->save_options( $_POST );
                    }
                    /* Render admin template */
                    $this->render_admin( 'payments-paypal' );
                } else {
                    /* Save options */
                    if ( isset( $_POST['save'] ) ) {
                        $this->save_options( $_POST );
                    }
                    /* Render admin template */
                    $this->render_admin( 'payments-settings' );
                }
            }
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
        if ( is_page('checkout') ) {

            $options = get_option( $this->options_name );

            // We need to use session variables during the checkout process
            if ( !session_id() )
                session_start();

            // Redirect if user is logged in 
            if ( /*is_user_logged_in()*/ false ) {
                wp_redirect( get_bloginfo('url') );
                exit;
            }

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
                        $_SESSION['billing_agreement'] = $options['settings']['billing_agreement'];
                        $_SESSION['billing_period']    = $options['settings']['billing_period'];
                        $_SESSION['billing_frequency'] = $options['settings']['billing_frequency'];
                    }
                    else {
                        $_SESSION['cost'] = $_POST['one_time_cost'];
                    }

                    // Set the proper step which will be loaded by "page-checkout.php" 
                    set_query_var( 'checkout_step', 'payment_method' );

                }
            }

            // If payment method is selected and submitted 
            elseif ( isset( $_POST['payment_method_submit'] )) {

                // var_dump($_SESSION); die;
                if ( $_POST['payment_method'] == 'paypal' ) {

                    // If recuring payment selected pass '0' so we can void the direct payment 
                    $cost = $_SESSION['billing_type'] == 'recurring' ? 0 : $_SESSION['cost']; 
                    $billing_agreement = $_SESSION['billing_type'] == 'recurring' ? $_SESSION['billing_agreement'] : null;  

                    // Make API call 
                    $result = $this->paypal_api_module->call_shortcut_express_checkout( 
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

            // If direct CC payment is submitted 
            elseif ( isset( $_POST['direct_payment_submit'] ) ) {

                // Make API call 
                $result = $this->paypal_api_module->direct_payment( 
                    $_POST['total_amount'], 
                    $_POST['cc_type'], 
                    $_POST['cc_number'], 
                    $_POST['exp_date'], 
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
                $result = $this->paypal_api_module->get_express_checkout_details( $_SESSION['token'] );

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

                if ( $_SESSION['billing_type'] == 'recurring' ) {

                    // Make CreateRecurringPaymentsProfile API call 
                    $result = $this->paypal_api_module->create_recurring_payments_profile( 
                        $_SESSION['cost'], 
                        $_SESSION['billing_period'], 
                        $_SESSION['billing_frequency'], 
                        $_SESSION['billing_agreement'] 
                    );

                } else {
                    // Make DoExpressCheckout API call 
                    $result = $this->paypal_api_module->do_express_checkout_payment( $_POST['total_amount'] );
                }
                
                // Handle Success and Error scenarios 
                if ( $result['status'] == 'success' ) {

                    // Insert/Update User 
                    $this->update_user( 
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

    /**
     * Render the admin navigation tabs ( the big ones at the top )
     *
     * @return string(HTML)
     */
    function render_admin_navigation_tabs() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->admin_page_slug ): ?>
            <a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) echo 'nav-tab-active'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=checkout"><?php _e( 'Payments', $this->text_domain ); ?></a>
        <?php endif;
    }

    /**
     * Render the admin navigation sub-tabs ( the small ones below the big ones )
     *
     * @return string(HTML)
     */
    function render_admin_navigation_subs() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->admin_page_slug && isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ): ?>
        <ul>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'checkout' )     echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=checkout"><?php _e( 'Payments Settings', $this->text_domain ); ?></a> | </h3></li>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'paypal' )       echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=paypal"><?php _e( 'PayPal Express', $this->text_domain ); ?></a> | </h3></li>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'authorizenet' ) echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=authorizenet"><?php _e( 'Authorize.net', $this->text_domain ); ?></a></h3></li>
        </ul>
        <?php endif;
    }

    /**
	 * Renders an admin section of display code.
	 *
	 * @param  string $name Name of the admin file(without extension)
	 * @param  string $vars Array of variable name=>value that is available to the display code(optional)
	 * @return void
	 **/
    function render_admin( $name, $vars = array() ) {
		foreach ( $vars as $key => $val )
			$$key = $val;
		if ( file_exists( "{$this->module_dir}ui-admin/{$name}.php" ) )
			include "{$this->module_dir}ui-admin/{$name}.php";
		else
			echo "<p>Rendering of admin template {$this->module_dir}ui-admin/{$name}.php failed</p>";
	}

    /**
     * Save plugin options.
     *
     * @param  array $params The $_POST array
     * @return die() if _wpnonce is not verified
     **/
    function save_options( $params ) {
        if ( wp_verify_nonce( $params['_wpnonce'], 'verify' ) ) {
            /* Remove unwanted parameters */
            unset( $params['_wpnonce'], $params['_wp_http_referer'], $params['save'] );
            /* Update options by merging the old ones */
            $options = $this->get_options();
            $options = array_merge( $options, array( $params['key'] => $params ) );
            update_option( $this->options_name, $options );
        } else {
            die( __( 'Security check failed!', $this->text_domain ) );
        }
    }

    /**
     * Get plugin options.
     *
     * @param  string|NULL $key The key for that plugin option.
     * @return array $options Plugin options or empty array if no options are found
     **/
    function get_options( $key = NULL ) {
        $options = get_option( $this->options_name );
        $options = is_array( $options ) ? $options : array();
        /* Check if specific plugin option is requested and return it */
        if ( isset( $key ) && array_key_exists( $key, $options ) )
            return $options[$key];
        else
            return $options;
    }
}
endif;

?>
