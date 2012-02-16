<?php

class DR_Tutorial {

    private $_setup_tutorial;
    private $_category_tutorial;
    private $_listing_tutorial;
    private $text_domain = DR_TEXT_DOMAIN;

    function DR_Tutorial() {
        __construct();
    }

    /**
     * PHP 5 constructor
     **/
    function __construct () {
        if ( !class_exists( 'Pointer_Tutorial' ) ) require_once DR_PLUGIN_DIR . 'libs/pointers_tutorial.php';
        $this->_setup_tutorial  = new Pointer_Tutorial( 'dr-setup', __( 'Setup tutorial', $this->text_domain ), false, false );
        $this->_category_tutorial   = new Pointer_Tutorial( 'dr-category', __( 'Category tutorial', $this->text_domain ), false, false );
        $this->_listing_tutorial = new Pointer_Tutorial( 'dr-listing', __( 'Listing tutorial', $this->text_domain ), false, false );

        add_action( 'admin_init', array( $this, 'process_tutorial' ) );
        add_action( 'wp_ajax_dr_restart_tutorial', array( $this, 'json_restart_tutorial' ) );
    }

    private $_setup_steps = array(
        'general',
        'capabilities',
        'payments',
        'payments_type',
        'shortcodes',
    );

	private $_category_steps = array(
		'category',
	);

	private $_listing_steps = array(
		'listing',
	);

	function process_tutorial () {
		global $pagenow;
		if ( isset( $_GET['page'] ) && 'settings' == $_GET['page'] && isset( $_GET['post_type'] ) && 'directory_listing' == $_GET['post_type'] )
            $this->_init_tutorial( $this->_setup_steps );

        if ( isset( $_GET['post_type'] ) && 'directory_listing' == $_GET['post_type'] && isset( $_GET['taxonomy'] ) && 'listing_category' == $_GET['taxonomy'] && 'edit-tags.php' == $pagenow )
            $this->_init_tutorial( $this->_category_steps );

        if ( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && 'directory_listing' == $_GET['post_type'] )
            $this->_init_tutorial( $this->_listing_steps );

		if ( defined( 'DOING_AJAX' ) ) {
			$this->_init_tutorial( $this->_setup_steps );
            $this->_init_tutorial( $this->_category_steps );
			$this->_init_tutorial( $this->_listing_steps );
		}

        $this->_setup_tutorial->initialize();
		$this->_category_tutorial->initialize();
		$this->_listing_tutorial->initialize();
	}

	function json_restart_tutorial () {
		$tutorial = @$_POST['tutorial'];
		$this->restart($tutorial);
		die;
	}

	public function restart ( $part=false ) {
		$tutorial = "_{$part}_tutorial";
		if ( $part && isset( $this->$tutorial ) ) return $this->$tutorial->restart();
		else if ( !$part ) {
			$this->_category_tutorial->restart();
			$this->_setup_tutorial->restart();
		}
	}

	private function _init_tutorial ( $steps ) {
		$this->_category_tutorial->set_textdomain( $this->text_domain );
		$this->_setup_tutorial->set_capability( 'manage_options' );

		foreach ( $steps as $step ) {
			$call_step = "add_{$step}_step";
			if ( method_exists( $this, $call_step ) ) $this->$call_step();
		}
	}



/* ----- Setup Steps ----- */

    function add_general_step () {
        $this->_setup_tutorial->add_step(
            admin_url( 'edit.php?post_type=directory_listing&page=settings' ), 'directory_listing_page_settings',
            '#dr-settings_general',
            __( 'General tab', $this->text_domain ),
            array(
                'content' => '<p>' . esc_js( __( 'Here you can set some settings as redirection, display options.', $this->text_domain ) ) . '</p>',
                'position' => array( 'edge' => 'top', 'align' => 'left' ),
            )
        );
    }

    function add_capabilities_step () {
        $this->_setup_tutorial->add_step(
            admin_url( 'edit.php?post_type=directory_listing&page=settings' ), 'directory_listing_page_settings',
            '#dr-settings_capabilities',
            __( 'Capabilities tab', $this->text_domain ),
            array(
                'content' => '<p>' . esc_js( __( "Here you can change capabilities (edit, view, delete etc.) of listings for user's roles.", $this->text_domain ) ) . '</p>',
                'position' => array( 'edge' => 'top', 'align' => 'left' ),
            )
        );
    }

    function add_payments_step () {
        $this->_setup_tutorial->add_step(
            admin_url( 'edit.php?post_type=directory_listing&page=settings' ), 'directory_listing_page_settings',
            '#dr-settings_payments',
            __( 'Payments tab', $this->text_domain ),
            array(
                'content' => '<p>' . esc_js( __( 'Here you can set price and settings for Recurring and One-time payments and to change Terms of Service.', $this->text_domain ) ) . '</p>',
                'position' => array( 'edge' => 'top', 'align' => 'left' ),
            )
        );
    }

    function add_payments_type_step () {
        $this->_setup_tutorial->add_step(
            admin_url( 'edit.php?post_type=directory_listing&page=settings' ), 'directory_listing_page_settings',
            '#dr-settings_payments_type',
            __( 'Payments Type tab', $this->text_domain ),
            array(
                'content' => '<p>' . esc_js( __( 'Here you can set settings for payments gateways or set "Free listings" mode.', $this->text_domain ) ) . '</p>',
                'position' => array( 'edge' => 'top', 'align' => 'left' ),
            )
        );
    }

    function add_shortcodes_step () {
        $this->_setup_tutorial->add_step(
            admin_url( 'edit.php?post_type=directory_listing&page=settings' ), 'directory_listing_page_settings',
            '#dr-settings_shortcodes',
            __( 'Shortcodes tab', $this->text_domain ),
            array(
                'content' => '<p>' . esc_js( __( 'Here you can find useful Sortcodes for expansion opportunities.', $this->text_domain ) ) . '</p>',
                'position' => array( 'edge' => 'top', 'align' => 'left' ),
            )
        );
    }


/* ----- Edit Steps ----- */

	function add_category_step () {
		$this->_category_tutorial->add_step(
			admin_url( 'edit-tags.php?taxonomy=listing_category&post_type=directory_listing' ), 'edit-tags.php',
			'#icon-edit',
			__( 'Categories', $this->text_domain ),
			array(
				'content' => '<p>' . esc_js( __( 'Here you can create new listing category or edit already exist.', $this->text_domain ) ) . '</p>',
				'position' => array( 'edge' => 'top', 'align' => 'left' ),
			)
		);

	}

/* ----- Insert ----- */

	function add_listing_step () {
		$this->_listing_tutorial->add_step(
			admin_url( 'edit.php?post_type=directory_listing' ), 'edit.php',
			'#icon-edit',
			__( 'Listings', $this->text_domain ),
			array(
				'content' => '<p>' . esc_js( __( 'Here you can create your own listings or edit already exist.', $this->text_domain ) ) . '</p>',
				'position' => array( 'edge' => 'top', 'align' => 'left' ),
			)
		);
	}


}

/* Initiate Admin */
new DR_Tutorial();
