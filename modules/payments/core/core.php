<?php

/**
 * Handles requests for "page-checkout.php". You can customize it for any
 * other request handling flow.
 **/
if ( !class_exists('Payments_Core') ):
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
     **/
    function Payments_Core( $admin_page_slug, $user_role ) {
        $this->admin_page_slug = $admin_page_slug;
        $this->user_role = $user_role;
        /* Hook the vars assignment to init hook */
        add_action( 'init', array( &$this, 'init_vars' ) );
        add_action( 'init', array( &$this, 'init_checkout_settings' ) );
        /* Handle all requests for checkout */
        add_action( 'template_redirect', array( &$this, 'handle_checkout_requests' ) );
        /* Handle all requests for checkout */
        add_action( 'handle_module_admin_requests', array( &$this, 'handle_admin_requests' ) );
        add_action( 'render_admin_navigation_tabs', array( &$this, 'render_admin_navigation_tabs' ) );
        add_action( 'render_admin_navigation_subs', array( &$this, 'render_admin_navigation_subs' ) );
        /* Create neccessary pages */
        add_action( 'wp_loaded', array( &$this, 'create_default_pages' ) );
    }

    /**
     * Initiate class variables.
     *
     * @return void
     **/
    function init_vars() {
        $options = $this->get_options( 'paypal' );
        $this->paypal_api_module = new PayPal_API_Module( $options );
    }

    /**
     * Init data for submit site.
     *
     * @return <type>
     * @todo Move to Payments Module
     */
    function init_checkout_settings() {
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
     * Create the default Classifieds pages.
     *
     * @return void
     **/
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
     * Insert/Update User
     *
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $billing The billing type for the user
     * @return NULL|void
      **/
    function update_user( $email, $first_name, $last_name, $billing, $credits = '' ) {
        /* Include registration helper functions */
        require_once( ABSPATH . WPINC . '/registration.php' );
        /* Variables */
        $user_login     = sanitize_user( strtolower( $first_name ));
        $user_email     = $email;
        $user_pass      = wp_generate_password();
        if ( username_exists( $user_login ) )
            $user_login .= '-' . sanitize_user( strtolower( $last_name ));
        if ( username_exists( $user_login ) )
            $user_login .= rand(1,9);
        if ( email_exists( $user_email )) {
            $user = get_user_by( 'email', $user_email );
            /* If user exists update it */
            if ( $user ) {
                wp_update_user( array( 'ID' => $user->ID, 'role' => $this->user_role ) );
                update_user_meta( $user->ID, $this->options_name . '_billing', $billing );
                $credentials = array( 'remember'=>true, 'user_login' => $user->user_login, 'user_password' => $user->user_pass );
                wp_signon( $credentials );
                return;
            }
        }
        $user_id = wp_insert_user( array(
            'user_login'   => $user_login,
            'user_pass'    => $user_pass,
            'user_email'   => $email,
            'display_name' => $first_name . ' ' . $last_name,
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'role'         => $this->user_role
        ) ) ;
        if ( $user_id ) {
            update_user_meta( $user_id, $this->options_name . '_billing', $billing );
            //$this->update_user_credits( $credits, $user_id );
            wp_new_user_notification( $user_id, $user_pass );
            $credentials = array( 'remember'=> true, 'user_login' => $user_login, 'user_password' => $user_pass );
            wp_signon( $credentials );
        }
    }

    /**
     * Render the admin navigation tabs ( the big ones at the top )
     *
     * @return string(HTML)
     **/
    function render_admin_navigation_tabs() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->admin_page_slug ): ?>
            <a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) echo 'nav-tab-active'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=checkout"><?php _e( 'Payments', $this->text_domain ); ?></a>
        <?php endif;
    }

    /**
     * Render the admin navigation sub-tabs ( the small ones below the big ones )
     *
     * @return string(HTML)
     **/
    function render_admin_navigation_subs() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->admin_page_slug && isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ): ?>
        <ul>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'checkout' )     echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=checkout"><?php _e( 'Checkout Settings', $this->text_domain ); ?></a> | </h3></li>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'paypal' )       echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=paypal"><?php _e( 'PayPal Express', $this->text_domain ); ?></a> | </h3></li>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'authorizenet' ) echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=authorizenet"><?php _e( 'Authorize.net', $this->text_domain ); ?></a></h3></li>
        </ul>
        <?php endif;
    }

    /**
     * Handle the admin requests for the payments module.
     **/
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
                    $this->render_admin( 'payments-checkout-settings' );
                }
            }
        }
    }

    /**
     * Handle all checkout requests.
     *
     * @uses session_start() We need to keep track of some session variables for the checkout
     * @return NULL If the payment gateway options are not configured.
     **/
    function handle_checkout_requests() {
        /* Only handle request if on the proper page */
        if ( is_page('checkout') ) {
            /* Start session */
            if ( !session_id() )
                session_start();
            /* Get site options */
            $options = get_option( $this->options_name );
            /* Redirect if user is logged in */
            if ( is_user_logged_in() ) {
                /** @todo Set redirect */
                //wp_redirect( get_bloginfo('url') );
            }
            /* If no PayPal API credentials are set, disable the checkout process */
            if ( empty( $options['paypal'] ) ) {
                /* Set the proper step which will be loaded by "page-checkout.php" */
                set_query_var( 'checkout_step', 'disabled' );
                return;
            }
            /* If Terms and Costs step is submitted */
            if ( isset( $_POST['terms_submit'] ) ) {
                /* Validate fields */
                if ( empty( $_POST['tos_agree'] ) || empty( $_POST['billing'] ) ) {
                    if ( empty( $_POST['tos_agree'] ))
                        add_action( 'tos_invalid', create_function('', 'echo "class=\"error\"";') );
                    if ( empty( $_POST['billing'] ))
                        add_action( 'billing_invalid', create_function('', 'echo "class=\"error\"";') );
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'terms' );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'payment_method' );
                }
            }
            /* If login attempt is made */
            elseif ( isset( $_POST['login_submit'] ) ) {
                if ( isset( $this->login_error )) {
                    add_action( 'login_invalid', create_function('', 'echo "class=\"error\"";') );
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'terms' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'checkout_error', $this->login_error );
                } else {
                    wp_redirect( get_bloginfo('url') . '/classifieds/my-classifieds/' );
                }
            }
            /* If payment method is selected and submitted */
            elseif ( isset( $_POST['payment_method_submit'] )) {
                if ( $_POST['payment_method'] == 'paypal' ) {
                    /* Make API call */
                    $result = $this->paypal_api_module->call_shortcut_express_checkout( $_POST['cost'] );
                    /* Handle Success and Error scenarios */
                    if ( $result['status'] == 'error' ) {
                        /* Set the proper step which will be loaded by "page-checkout.php" */
                        set_query_var( 'checkout_step', 'api_call_error' );
                        /* Pass error params to "page-checkout.php" */
                        set_query_var( 'checkout_error', $result );
                    } else {
                        /* Set billing and credits so we can update the user account later */
                        $_SESSION['billing'] = $_POST['billing'];
                        $_SESSION['credits'] = $_POST['credits'];
                    }
                } elseif ( $_POST['payment_method'] == 'cc' ) {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'cc_details' );
                }
            }
            /* If direct CC payment is submitted */
            elseif ( isset( $_POST['direct_payment_submit'] ) ) {
                /* Make API call */
                $result = $this->paypal_api_module->direct_payment( $_POST['total_amount'], $_POST['cc_type'], $_POST['cc_number'], $_POST['exp_date'], $_POST['cvv2'], $_POST['first_name'], $_POST['last_name'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['country_code'] );
                /* Handle Success and Error scenarios */
                if ( $result['status'] == 'success' ) {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'direct_payment' );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'api_call_error' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'checkout_error', $result );
                }
            }
            /* If PayPal has redirected us back with the proper TOKEN */
            elseif ( isset( $_REQUEST['token'] ) && !isset( $_POST['confirm_payment_submit'] ) && !isset( $_POST['redirect_my_classifieds'] ) ) {
                /* Make API call */
                $result = $this->paypal_api_module->get_shipping_details();
                /* Handle Success and Error scenarios */
                if ( $result['status'] == 'success' ) {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'confirm_payment' );
                    /* Pass transaction details params to "page-checkout.php" */
                    set_query_var( 'checkout_transaction_details', $result );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'api_call_error' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'checkout_error', $result );
                }
            }
            /* If payment confirmation is submitted */
            elseif ( isset( $_POST['confirm_payment_submit'] ) ) {
                /* Make API call */
                $result = $this->paypal_api_module->confirm_payment( $_POST['total_amount'] );
                /* Handle Success and Error scenarios */
                if ( $result['status'] == 'success' ) {
                    /* Insert/Update User */
                    $this->update_user( $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['billing'], $_POST['credits'] );
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'success' );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'checkout_step', 'api_call_error' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'checkout_error', $result );
                }
            }
            /* If transaction processed successfully, redirect to my-classifieds */
            elseif( isset( $_POST['redirect_my_classifieds'] ) ) {
                wp_redirect( get_bloginfo('url') . '/classifieds/my-classifieds/' );
            }
            /* If no requests are made load default step */
            else {
                /* Set the proper step which will be loaded by "page-checkout.php" */
                set_query_var( 'checkout_step', 'terms' );
            }
        }
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
