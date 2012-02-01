<?php

/**
 * DR_Core
 *
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://premium.wpmudev.org}
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class DR_Core {

    /** @var string $plugin_url Plugin URL */
    var $plugin_url = DR_PLUGIN_URL;
    /** @var string $plugin_dir Path to plugin directory */
    var $plugin_dir = DR_PLUGIN_DIR;
    /** @var string $text_domain The text domain for strings localization */
    var $text_domain = DR_TEXT_DOMAIN;
    /** @var string Name of options DB entry */
    var $options_name = DR_OPTIONS_NAME;

    var $is_directory_page = false;
    var $home_listing_template;
    var $listing_template;
    var $dr_taxonomy_template;


    /**
     * Constructor.
     */
    function DR_Core() {
        register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_activate' ) );
        register_deactivation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_deactivate' ) );

        register_theme_directory( $this->plugin_dir . 'themes' );

		add_action( 'plugins_loaded', array( &$this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( &$this, 'init' ) );

		// TODO: These are not properly implemented
        add_action( 'wp_loaded', array( &$this, 'scheduly_expiration_check' ) );
        add_action( 'check_expiration_dates', array( &$this, 'check_expiration_dates_callback' ) );

		if ( is_admin() )
			return;


        /* Enqueue styles */
        add_action( 'template_redirect', array( &$this, 'template_redirect' ), 12 );

        add_action( 'wp', array( &$this, 'load_directory_templates' ) );


//        add_filter( 'home_template', array( &$this, 'get_home_template' ) );

//		add_filter( 'single_template', array( &$this, 'handle_single_template' ) );
//        add_filter( 'archive_template', array( &$this, 'handle_archive_template' ) );
//		add_filter( 'archive_template', array( &$this, 'handle_template' ) );

        add_action( 'custom_banner_header', array( &$this, 'output_banners' ) );
    }

    /**
     * Intiate plugin.
     *
     * @return void
     */
    function init() {
		global $wp, $wp_rewrite;

        /*
        handle_action_buttons_requests
        If your want to go to admin profile
        */
        if ( isset( $_POST['redirect_profile'] ) ) {
            wp_redirect( admin_url() . 'profile.php' );
            exit();
        }
        elseif ( isset( $_POST['redirect_listing'] ) ) {
            wp_redirect( admin_url() . 'post-new.php?post_type=directory_listing' );
            exit();
        }
        elseif ( isset( $_POST['directory_logout'] ) ) {
            $options = get_option( DR_OPTIONS_NAME );

            wp_logout();

            if ( '' != $options['general_settings']['logout_url'] )
                wp_redirect( $options['general_settings']['logout_url'] );
            else
                wp_redirect( get_option( 'siteurl' ) );

            exit();
        }


        // Signin page
        $wp->add_query_var( 'dr_signin' );
        $this->add_rewrite_rule( 'signin/?$', array(
            'dr_signin' => 1
        ) );

		// Signup page
		$wp->add_query_var( 'dr_signup' );
		$this->add_rewrite_rule( 'signup/?$', array(
			'dr_signup' => 1
		) );


        register_post_type( 'directory_listing', array(
            'public' => ( get_option( 'dp_options' ) ) ? false : true,
            'rewrite' => array( 'slug' => 'listings', 'with_front' => false ),
            'has_archive' => true,

            'capability_type' => 'listing',
            'capabilities' => array( 'edit_posts' => 'edit_published_listings' ),
            'map_meta_cap' => true,

            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'post-formats' ),

            'labels' => array(
                'name'          => __('Listings', $this->text_domain ),
                'singular_name' => __('Listing', $this->text_domain ),
                'add_new'       => __('Add New', $this->text_domain ),
                'add_new_item'  => __('Add New Listing', $this->text_domain ),
                'edit_item'     => __('Edit Listing', $this->text_domain ),
                'new_item'      => __('New Listing', $this->text_domain ),
                'view_item'     => __('View Listing', $this->text_domain ),
                'search_items'  => __('Search Listings', $this->text_domain ),
                'not_found'     => __('No listings found', $this->text_domain ),
                'not_found_in_trash'    => __('No listings found in trash', $this->text_domain ),
            )
        ) );

		register_taxonomy( 'listing_tag', 'directory_listing', array(
			'rewrite'       => array( 'slug' => 'listings-tag', 'with_front' => false ),
            'capabilities'  => array( 'assign_terms' => 'edit_published_listings' ),
			'labels'        => array(
				'name'			=> __( 'Listing Tags', $this->text_domain ),
				'singular_name'	=> __( 'Listing Tag', $this->text_domain ),
				'search_items'	=> __( 'Search Listing Tags', $this->text_domain ),
				'popular_items'	=> __( 'Popular Listing Tags', $this->text_domain ),
				'all_items'		=> __( 'All Listing Tags', $this->text_domain ),
				'edit_item'		=> __( 'Edit Listing Tag', $this->text_domain ),
				'update_item'	=> __( 'Update Listing Tag', $this->text_domain ),
				'add_new_item'	=> __( 'Add New Listing Tag', $this->text_domain ),
				'new_item_name'	=> __( 'New Listing Tag Name', $this->text_domain ),
				'separate_items_with_commas'	=> __( 'Separate listing tags with commas', $this->text_domain ),
				'add_or_remove_items'			=> __( 'Add or remove listing tags', $this->text_domain ),
				'choose_from_most_used'			=> __( 'Choose from the most used listing tags', $this->text_domain ),
			)
		) );

        //rewrite rules for tags
        $wp_rewrite->add_rule( 'listings-tag/([^/]+)/', '?listing_tag=$matches[1]', 'top' );


		register_taxonomy( 'listing_category', 'directory_listing', array(
			'rewrite'       => array( 'slug' => 'listings-category', 'with_front' => false, 'hierarchical' => true ),
            'capabilities'  => array( 'assign_terms' => 'edit_published_listings' ),
			'hierarchical'  => true,
			'labels' => array(
				'name'			=> __( 'Listing Categories', $this->text_domain ),
				'singular_name'	=> __( 'Listing Category', $this->text_domain ),
				'search_items'	=> __( 'Search Listing Categories', $this->text_domain ),
				'popular_items'	=> __( 'Popular Listing Categories', $this->text_domain ),
				'all_items'		=> __( 'All Listing Categories', $this->text_domain ),
				'parent_item'	=> __( 'Parent Category', $this->text_domain ),
				'edit_item'		=> __( 'Edit Listing Category', $this->text_domain ),
				'update_item'	=> __( 'Update Listing Category', $this->text_domain ),
				'add_new_item'	=> __( 'Add New Listing Category', $this->text_domain ),
				'new_item_name'	=> __( 'New Listing Category', $this->text_domain ),
				'parent_item_colon'		=> __( 'Parent Category:', $this->text_domain ),
				'add_or_remove_items'	=> __( 'Add or remove listing categories', $this->text_domain ),
			)
		) );

        //rewrite rules for categories
        $wp_rewrite->add_rule( 'listings-category/([^/]+)/', '?listing_category=$matches[1]', 'top' );

        //import data from old plugin version
        $this->import_from_old();

    }

	/**
	 * Simple wrapper for adding straight rewrite rules,
	 * but with the matched rule as an associative array.
	 *
	 * @see http://core.trac.wordpress.org/ticket/16840
	 *
	 * @param string $regex The rewrite regex
	 * @param array $args The mapped args
	 * @param string $position Where to stick this rule in the rules array. Can be 'top' or 'bottom'
	 */
	function add_rewrite_rule( $regex, $args, $position = 'top' ) {
		global $wp, $wp_rewrite;

		$result = add_query_arg( $args, 'index.php' );
		add_rewrite_rule( $regex, $result, $position );
	}

    /**
     * Import data from old version to new.
     *
     * @return void
     */
    function import_from_old() {
        //update children of categories
        if ( get_option( 'dr_cache_update' ) ) {
            clean_term_cache( get_option( 'dp_cache_update' ), 'listing_category' );
            delete_option( 'dr_cache_update' );
        }

        if ( get_option( 'dp_options' ) && isset( $_POST['install_dir2'] ) ) {
            global $wpdb;
            //put top level Categories and Tags
            $old_taxonomies = get_option( 'ct_custom_taxonomies' );

            if ( $old_taxonomies ) {
                foreach( $old_taxonomies as $old_taxonomy => $old_taxonomy_args ) {
                    $args = array(
                        'description'   => '',
                        'slug'          => $old_taxonomy_args['args']['labels']['slug'],
                        'parent'        => 0
                        );

                    if ( true == $old_taxonomy_args['args']['hierarchical'] ) {
                        //add main level category
                        $new_taxonomy = wp_insert_term( $old_taxonomy_args['args']['labels']['name'], 'listing_category', $args );

                        if ( is_array( $new_taxonomy ) ) {
                            //add child levels of Categories
                            $result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'listing_category', parent=%d WHERE taxonomy = '%s' AND parent=0", $new_taxonomy['term_taxonomy_id'], $old_taxonomy ) );
                            $result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'listing_category' WHERE taxonomy = '%s'", $old_taxonomy ) );
                            //add action for update children of categories
                            update_option( 'dr_cache_update', $new_taxonomy['term_taxonomy_id'] );
                        }

                    } else {
                        //add tags
                        $result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'listing_tag' WHERE taxonomy = '%s'", $old_taxonomy ) );
                    }
                }

            }

            //rewrite options
            $old_options = get_option( 'dp_options' );
            if ( $old_options ) {
                if ( isset( $old_options['ads_settings']['header_ad_code'] ) && '' != $old_options['ads_settings']['header_ad_code'] ) {
                    $params = array(
                        'header_ad_code' => $old_options['ads_settings']['header_ad_code'],
                        'key' => 'ads_settings'
                        );
                    $options = $this->get_options();
                    $options = array_merge( $options, array( $params['key'] => $params ) );
                    update_option( $this->options_name, $options );
                }
            }

            //rewrite payments options
            $old_payments = get_option( 'module_payments' );
            if ( $old_payments ) {
                if ( isset( $old_payments['settings'] ) ) {
                    $old_settings = $old_payments['settings'];
                    $new_payments = $this->get_options( 'payment_settings' );

                    $params = array(
                        'recurring_cost'    => isset( $old_settings['recurring_cost'] ) ? sprintf( "%01.2f", $old_settings['recurring_cost'] ) : sprintf( "%01.2f", $new_payments['recurring_cost'] ),
                        'recurring_name'    => isset( $old_settings['recurring_name'] ) ? $old_settings['recurring_name'] : $new_payments['recurring_name'],
                        'billing_period'    => isset( $old_settings['billing_period'] ) ? $old_settings['billing_period'] : $new_payments['billing_period'],
                        'billing_frequency' => isset( $old_settings['billing_frequency'] ) ? $old_settings['billing_frequency'] : $new_payments['billing_frequency'],
                        'billing_agreement' => isset( $old_settings['billing_agreement'] ) ? $old_settings['billing_agreement'] : $new_payments['billing_agreement'],
                        'one_time_cost'     => isset( $old_settings['one_time_cost'] ) ? sprintf( "%01.2f", $old_settings['one_time_cost'] ) : sprintf( "%01.2f", $new_payments['one_time_cost'] ),
                        'one_time_name'     => isset( $old_settings['one_time_name'] ) ? $old_settings['one_time_name'] : $new_payments['one_time_name'],
                        'tos_content'       => isset( $old_settings['tos_content'] ) ? $old_settings['tos_content'] : $new_payments['tos_content'],
                        'key'               => 'payment_settings'
                        );

                    $options = $this->get_options();
                    $options = array_merge( $options, array( $params['key'] => $params ) );
                    update_option( $this->options_name, $options );
                }

                if ( isset( $old_payments['paypal'] ) ) {
                    $old_paypal = $old_payments['paypal'];
                    $new_paypal = $this->get_options( 'paypal' );

                    $params = array(
                        'api_url'       => isset( $old_paypal['api_url'] ) ? $old_paypal['api_url'] : $new_paypal['api_url'],
                        'api_username'  => isset( $old_paypal['api_username'] ) ? $old_paypal['api_username'] : $new_paypal['api_username'],
                        'api_password'  => isset( $old_paypal['api_password'] ) ? $old_paypal['api_password'] : $new_paypal['api_password'],
                        'api_signature' => isset( $old_paypal['api_signature'] ) ? $old_paypal['api_signature'] : $new_paypal['api_signature'],
                        'currency'      => isset( $old_paypal['currency'] ) ? $old_paypal['currency'] : $new_paypal['currency'],
                        'key'           => 'paypal'
                        );

                    $options = $this->get_options();
                    $options = array_merge( $options, array( $params['key'] => $params ) );
                    update_option( $this->options_name, $options );
                }
            }

            //change role for old users
            if( function_exists( get_users ) ) {
                $old_users = get_users( array( 'role' => 'dp_member' ) );
            } else {
                $wp_user_search = new WP_User_Search( "", "", 'dp_member' );
                $old_users = $wp_user_search->get_results();
            }

            if ( 0 < count( $old_users ) ) {

                foreach ( $old_users as $old_user ) {
                    $old_userdata = get_userdata( $old_user->ID );

                    if ( isset( $old_userdata->module_payments ) &&
                        ( 0 < count( $old_userdata->module_payments['paypal']['transactions'] ) || '' != $old_userdata->module_payments['paypal']['profile_id'] )
                        ) {
                        update_usermeta( $old_user->ID, 'wp_capabilities', array( 'directory_member_paid' => '1' ) );
                    } else {
                        update_usermeta( $old_user->ID, 'wp_capabilities', array( 'directory_member_not_paid' => '1' ) );
                    }
                }
                $result = $wpdb->query( "UPDATE {$wpdb->usermeta} SET meta_key = 'dr_options' WHERE meta_key = 'module_payments'" );
            }

            //delete old options
            delete_option( 'dp_options' );
            delete_option( 'ct_custom_taxonomies' );
            delete_option( 'module_payments' );
            delete_option( 'ct_custom_post_types' );
            delete_option( 'ct_custom_fields' );
            delete_option( 'ct_flush_rewrite_rules' );
            delete_site_option( 'dp_options' );
            delete_site_option( 'ct_custom_taxonomies' );
            delete_site_option( 'module_payments' );
            delete_site_option( 'ct_custom_post_types' );
            delete_site_option( 'ct_custom_fields' );
            delete_site_option( 'ct_flush_rewrite_rules' );
            delete_site_option( 'allow_per_site_content_types' );

            wp_redirect( add_query_arg( array( 'post_type' => 'directory_listing' ), 'edit.php' ) );
            exit;
        }

    }


    /**
     * Fire on plugin activation.
     *
     * @return void
     */
    function plugin_activate() {
		$this->init();
		flush_rewrite_rules( false );
    }

    /**
	 * Fire on plugin deactivation.
	 * If $this->flush_plugin_data is set to "true"
     * all plugin data will be deleted
     *
     * @return void
     */
    function plugin_deactivate() {
		// if true all plugin data will be deleted
        if ( true ) {
            delete_option( $this->options_name );
            delete_site_option( $this->options_name );
        }
    }

    /**
     * Loads "{$text_domain}-[xx_XX].mo" language file from the "languages" directory
	 *
     * @return void
     */
    function load_plugin_textdomain() {
        load_plugin_textdomain( $this->text_domain, null, plugin_basename( $this->plugin_dir . 'languages' ) );
    }

	/**
	 * Redirect templates using $wp_query.
	 */
	function template_redirect() {
		global $wp_query;

        if ( is_dr_page( 'signup' ) ) {
            $this->load_template( 'page-signup.php' );
        }

		if ( is_dr_page( 'signin' ) ) {
            if ( !is_user_logged_in() ) {
                if ( $_POST['signin_submit'] ) {
                    $args = array( 'remember'   => ( $_POST['user_rem'] ) ? true : false,
                               'user_login'     => $_POST['user_login'],
                               'user_password'  => $_POST['user_pass']
                               );

                    $signin = wp_signon( $args, false );

                    if ( is_wp_error( $signin ) ) {
                        set_query_var( 'signin_error', $signin->get_error_message() );
                    } else {
                        $options = $this->get_options( 'general_settings' );

                        if ( '' != $options['signin_url'] )
                            wp_redirect( $options['signin_url'] );
                        else
                            wp_redirect( get_option( 'siteurl' ) );

                        exit;
                    }
                }
            } else {
                wp_redirect(  get_option( 'siteurl' ) );
                exit;
            }

			$this->load_template( 'page-signin.php' );
		}

		// Redirect template loading to archive-listing.php rather than to archive.php
		if ( is_dr_page( 'tag' ) || is_dr_page( 'category' ) ) {
			$wp_query->set( 'post_type', 'directory_listing' );
		}
	}


	/**
	 * Load a template, with fallback to default-templates.
	 */
	function load_template( $name ) {
		$path = locate_template( $name );

		if ( !$path ) {
			$path = $this->plugin_dir . "ui-front/general/$name";
		}

		load_template( $path );
		die;
	}

    /**
	 * Output banner.
	 *
	 * @access public
	 * @return void
     */
    function output_banners() {
        $options = $this->get_options( 'ads_settings' );
        if ( !empty( $options['header_ad_code'] ) ) {
            echo stripslashes( $options['header_ad_code'] );
        } else {
            echo '<span>' .  __( 'Advartise Here', $this->text_domain ) . '</span>';
        }
    }


    /**
     * Schedule expiration check for twice daily.
     *
     * @return void
     */
    function scheduly_expiration_check() {
        if ( !wp_next_scheduled( 'check_expiration_dates' ) ) {
            wp_schedule_event( time(), 'twicedaily', 'check_expiration_dates' );
        }
    }

    /**
     * Check each post from the used post type and compare the expiration date/time
     * with the current date/time. If the post is expired update it's status.
     *
     * @return void
     */
    function check_expiration_dates_callback() {}

    /**
     * Get plugin options.
     *
     * @param  string|NULL $key The key for that plugin option.
     * @return array $options Plugin options or empty array if no options are found
     */
    function get_options( $key = null ) {
        $options = get_option( $this->options_name );
        $options = is_array( $options ) ? $options : array();
        /* Check if specific plugin option is requested and return it */
        if ( isset( $key ) && array_key_exists( $key, $options ) )
            return $options[$key];
        else
            return $options;
    }




    //scans post type at template_redirect to apply custom themeing to listings
    function load_directory_templates() {
        global $wp_query;

        //load proper theme for single listing page display
        if ( $wp_query->is_home ) {

            $templates = array( 'home-listing.php' );

            //if custom template exists load it
            if ( $this->home_listing_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_home_listing_template' ) );
            }

        }

        //load proper theme for single listing page display
        if ( $wp_query->is_single &&  'directory_listing' == $wp_query->query_vars['post_type'] ) {

            //check for custom theme templates
            $listing_name = get_query_var( 'directory_listing' );
            $listing_id   = (int) $wp_query->get_queried_object_id();

            $templates = array();
            if ( $listing_name )
                $templates[] = "single-listing-$listing_name.php";
            if ( $listing_id )
                $templates[] = "single-listing-$listing_id.php";
            $templates[] = 'single-listing.php';

            //if custom template exists load it
            if ( $this->listing_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_listing_template' ) );
            } else {
                //otherwise load the page template and use our own theme
                $wp_query->is_single    = null;
                $wp_query->is_page      = 1;
                add_filter( 'the_title', array( &$this, 'delete_post_title' ), 99 );
                add_filter( 'the_content', array( &$this, 'listing_content' ), 99 );
            }

            $this->is_directory_page = true;
        }


        //load proper theme for product category or tag listings
        if ( 'listing_category' == $wp_query->query_vars['taxonomy'] || 'listing_tag' == $wp_query->query_vars['taxonomy'] ) {
            $templates = array();

            if ( 'listing_category' == $wp_query->query_vars['taxonomy'] ) {

                $cat_name = get_query_var( 'listing_category' );
                $cat_id = absint( $wp_query->get_queried_object_id() );

                if ( $cat_name )
                    $templates[] = "dr_category-$cat_name.php";
                if ( $cat_id )
                    $templates[] = "dr_category-$cat_id.php";

                $templates[] = 'dr_category.php';

            } elseif ( 'listing_tag' == $wp_query->query_vars['taxonomy'] ) {

                $tag_name = get_query_var( 'listing_tag' );
                $tag_id = absint( $wp_query->get_queried_object_id() );

                if ( $tag_name )
                    $templates[] = "dr_tag-$tag_name.php";
                if ( $tag_id )
                    $templates[] = "dr_tag-$tag_id.php";

                $templates[] = "dr_tag.php";

            }

            //defaults
            $templates[] = "dr_taxonomy.php";
            $templates[] = "dr_listinglist.php";

            //override if id is passed, mainly for product category dropdown widget
            if ( is_numeric( get_query_var($wp_query->query_vars['taxonomy']) ) ) {
                $term = get_term( get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy'] );
                $wp_query->query_vars[$wp_query->query_vars['taxonomy']] = $term->slug;
                $wp_query->query_vars['term'] = $term->slug;
            }

            //if custom template exists load it
            if ( $this->dr_taxonomy_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_dr_taxonomy_template' ) );
            } else {
                //otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
                $wp_query->is_page = 1;
                //$wp_query->is_singular = 1;
                $wp_query->is_404 = null;
                $wp_query->post_count = 1;
//                add_filter( 'single_post_title', array(&$this, 'page_title_output'), 99 );
//                add_filter( 'bp_page_title', array(&$this, 'page_title_output'), 99 );
//                add_filter( 'wp_title', array(&$this, 'wp_title_output'), 19, 3 );
//                add_filter( 'the_title', array(&$this, 'page_title_output'), 99, 2 );
                add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 99 );
                add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 99 );
            }

            $this->is_directory_page = true;
        }

        //load shop specific items
        if ( $this->is_directory_page ) {
            //fixes a nasty bug in BP theme's functions.php file which always loads the activity stream if not a normal page
            remove_all_filters( 'page_template' );

            //prevents 404 for virtual pages
            status_header( 200 );
        }
    }


    function listing_content( $content ) {
        global $post, $current_user;

        ob_start();
        ?>


            <div class="entry-post">
                <h1 class="entry-title"><?php echo get_dr_title( $post->ID ); ?></h1>
                <div class="entry-content">
                    <?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>
                    <?php
                    $content = get_the_content( null, 0 );
                    $content = str_replace( ']]>', ']]&gt;', $content );
                    echo $content;
                    ?>

                    <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', DR_TEXT_DOMAIN ), 'after' => '</div>' ) ); ?>
                </div><!-- .entry-content -->
                <div class="clear"></div>
            </div>

            <div class="entry-meta">
                <?php the_dr_posted_on(); ?>
                <?php do_action('sr_avg_rating'); ?><br />
                <span class="comments"><?php comments_popup_link( __( 'No Reviews &#187;', DR_TEXT_DOMAIN ), __( '1 Review &#187;', DR_TEXT_DOMAIN ), __( '% Reviews &#187;', DR_TEXT_DOMAIN ) ); ?></span>
                <br />
                <?php the_dr_posted_in(); ?>
                <?php edit_post_link( __( 'Edit', DR_TEXT_DOMAIN ), '<span class="edit-link">', '</span>' ); ?>
                <span class="tags"><?php the_tags( __( 'Tags: ', DR_TEXT_DOMAIN ), ', ', ''); ?></span>

                <?php if ( !is_user_logged_in() ) : ?>
                    <?php echo '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to rate item.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>'; ?>
                <?php else: ?>
                    <?php do_action('sr_rate_this'); ?>
                <?php endif; ?>
            </div>

            <?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
                <div id="entry-author-info">
                    <div id="author-avatar">
                        <?php echo get_avatar( get_the_author_meta( 'user_email' ), 60 ); ?>
                    </div><!-- #author-avatar -->
                    <div id="author-description">
                        <h2><?php printf( esc_attr__( 'About %s', DR_TEXT_DOMAIN ), get_the_author() ); ?></h2>
                        <?php the_author_meta( 'description' ); ?>
                        <div id="author-link">
                            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
                                <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', DR_TEXT_DOMAIN ), get_the_author() ); ?>
                            </a>
                        </div><!-- #author-link    -->
                    </div><!-- #author-description -->
                </div><!-- #entry-author-info -->
            <?php endif; ?>

        <?
        $new_content = ob_get_contents();
    ob_end_clean();

        return $new_content;
    }



  //this is the default theme added to product listings
  function listing_list_theme( $content ) {
        //don't filter outside of the loop

      if ( !in_the_loop() )
          return $content;


    $content = $this->dr_listing_list( false );
    $content .= get_posts_nav_link();
//    var_dump( $content );
//    exit;
    return $content;
  }


    //filter the custom home listing template
    function custom_home_listing_template( $template ) {
        return $this->home_listing_template;
    }

    //filter the custom single listing template
    function custom_listing_template( $template ) {
        return $this->listing_template;
    }

    //filter the custom listing list template
    function custom_dr_taxonomy_template( $template ) {
        return $this->dr_taxonomy_template;
    }


    //filters the titles for our custom pages
    function delete_post_title( $title, $id = false ) {
        global $wp_query;

        if ( $title == $wp_query->post->post_title ) {
            return '';
        }

        return $title;
    }


    function dr_listing_list( $echo = true, $paginate = '', $page = '', $per_page = '', $order_by = '', $order = '', $category = '', $tag = '' ) {
        global $wp_query, $mp;

        //setup taxonomy if applicable
        if ( $category ) {
            $taxonomy_query = '&listing_category=' . sanitize_title($category);
        } elseif ( $tag ) {
            $taxonomy_query = '&listing_tag=' . sanitize_title($tag);
        } elseif ( $wp_query->query_vars['taxonomy'] == 'listing_category' || $wp_query->query_vars['taxonomy'] == 'listing_tag' ) {
            $taxonomy_query = '&' . $wp_query->query_vars['taxonomy'] . '=' . get_query_var( $wp_query->query_vars['taxonomy'] );
        }

        //The Query
        $custom_query = new WP_Query( 'post_type=directory_listing&post_status=publish' . $taxonomy_query );

        //allows pagination links to work get_posts_nav_link()
        if ( $wp_query->max_num_pages == 0 || $taxonomy_query )
            $wp_query->max_num_pages = $custom_query->max_num_pages;

        $content = '<div id="dr_listing_list">';

        if ( $last = count( $custom_query->posts ) ) {
            $count = 1;
            foreach ( $custom_query->posts as $post ) {

                // Retrieves categories list of current post, separated by commas.
                $categories = wp_get_post_terms( $post->ID, "listing_category", "" );

                foreach ( $categories as $category )
                    $categories_list[] = '<a href="' . get_term_link( $category ) . '" title="' . $category->name . '" >' . $category->name . '</a>';

                $categories_list = implode( ", ", ( array ) $categories_list );

                // Retrieves tag list of current post, separated by commas.
                $tags = wp_get_post_terms( $post->ID, "listing_tag", "" );
                foreach ( $tags as $tag )
                    $tags_list[] = '<a href="' . get_term_link( $tag ) . '" title="' . $tag->name . '" >' . $tag->name . '</a>';

                $tags_list = implode( ", ", ( array ) $tags_list );



                //add last css class for styling grids
                if ( $count == $last )
                    $class = array( 'dr_listing', 'last-listing' );
                else
                    $class = 'dr_listing';

                $content .= '<div class="' . $class . '">';
                $content .= '   <div class="entry-meta">';
                $content .= '       <div class="entry-utility">';

                if ( $categories_list ) {
                    $content .= '           <span class="cat-links">' . sprintf( __( '<span class="%1$s">Posted in</span> %2$s', DR_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list ) . '</span><br />';
                    unset( $categories_list );
                }

                if ( $tags_list ) {
                    $content .= '           <span class="tag-links">' . sprintf( __( '<span class="%1$s">Tagged</span> %2$s', DR_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ) . '</span><br />';
                    unset( $tags_list );
                }


                ob_start();
                ?>
                    <?php do_action( 'sr_avg_ratings_of_listings', $post->ID ); ?>
                                        <span class="comments-link"><?php comments_popup_link( __( 'Leave a review', DR_TEXT_DOMAIN ), __( '1 Review', DR_TEXT_DOMAIN ), __( '% Reviews', DR_TEXT_DOMAIN ), __( 'Reviews Off', DR_TEXT_DOMAIN ) ); ?></span>;
                        <?php edit_post_link( __( 'Edit', DR_TEXT_DOMAIN ), '<span class="edit-link">', '</span>' ); ?>
                <?php
                $content .= ob_get_contents();
                ob_end_clean();

                $content .= '       </div>';
                $content .= '   </div>';

                $content .= '   <div class="entry-post">';
                $content .= '   <h2 class="entry-title">';
                $content .= '       <a href="' . get_permalink( $post->ID ) . '" title="' . sprintf( esc_attr__( 'Permalink to %s', DR_TEXT_DOMAIN ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark">' . $post->post_title . '</a>';
                $content .= '   </h2>';
                $content .= '   </div>';

                $content .= '           <h3 class="mp_product_name"><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h3>';
                $content .= '           <div class="mp_product_content">';
//                $product_content = mp_product_image( false, 'list', $post->ID );
//                $product_content .= $mp->product_excerpt($post->post_excerpt, $post->post_content, $post->ID);
//                $content .= apply_filters( 'mp_product_list_content', $product_content, $post->ID );
                $content .= $post->post_content;
                $content .= '       </div>';



                $content .= '<div class="mp_product_meta">';
                //price
//                $meta = mp_product_price(false, $post->ID);
                //button
//                $meta .= mp_buy_button(false, 'list', $post->ID);
                $content .= apply_filters( 'mp_product_list_meta', $meta, $post->ID );
                $content .= '</div>';

                $content .= '</div>';

                $count++;
            }
        } else {
            $content .= '<div id="mp_no_products">' . apply_filters( 'mp_product_list_none', __('No Products', 'mp') ) . '</div>';
        }

        $content .= '</div>';

        if ( $echo )
            echo $content;
        else
            return $content;
    }

















}

/* Initiate Class */
new DR_Core();

