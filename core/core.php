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
    var $directory_template;
    var $dr_first_thumbnail;


    /**
     * Constructor.
     */
    function DR_Core() {

        register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_activate' ) );
        register_deactivation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_deactivate' ) );

        //show "Directory Theme" only for old installation
        if ( 'Directory Theme' == get_current_theme() ) {
            register_theme_directory( $this->plugin_dir . 'themes' );
        }

        /* Create neccessary pages */
        add_action( 'wp_loaded', array( &$this, 'create_default_pages' ) );

        add_action( 'plugins_loaded', array( &$this, 'load_plugin_textdomain' ) );
        add_action( 'init', array( &$this, 'init' ) );


        if ( is_admin() )
            return;


        /* Enqueue styles */
        add_action( 'wp_enqueue_scripts', array( &$this, 'add_js_css' ) );

        add_action( 'template_redirect', array( &$this, 'template_redirect' ), 12 );

        /* Handle requests for plugin pages */
        add_action( 'template_redirect', array( &$this, 'handle_page_requests' ) );


        add_action( 'wp', array( &$this, 'load_directory_templates' ) );

        add_action( 'custom_banner_header', array( &$this, 'output_banners' ) );

        //hide some menu pages
        add_filter( 'wp_page_menu_args', array( &$this, 'hide_menu_pages' ), 99 );

        //add menu items
        add_filter( 'wp_list_pages', array( &$this, 'filter_list_pages' ), 10, 2 );


    }

    /**
     * adds our links to theme nav menus using wp_list_pages()
     **/
    function filter_list_pages( $list, $args ) {

        if ( current_user_can( 'edit_published_listings' ) ) {
            $directory_page = $this->get_page_by_meta( 'listings' );

            if ( $args['depth'] == 1 )
                return $list;

            $link = '<a href="' . $directory_page->guid . '">' . $directory_page->post_title . '</a>';

            $temp_break = strpos( $list, $link );

            //if we can't find the page for some reason try with other link
            if ( $temp_break === false ) {
                $link = '<a href="' . $directory_page->guid . '" title="' . $directory_page->post_title . '">' . $directory_page->post_title . '</a>';
                $temp_break = strpos( $list, $link );

                //if we can't find the page for some reason skip
                if ( $temp_break === false )
                    return $list;
            }


            $break = strpos( $list, '</a>', $temp_break ) + 4;

            $nav = substr( $list, 0, $break );

            $nav .= '<ul class="children">';
            $nav .= '<li class="page_item"><a href="' . site_url() . '/my-listings/" title="' . __( 'My Listings', $this->text_domain ) . '">' . __( 'My Listings', $this->text_domain ) . '</a></li>';
            $nav .= '<li class="page_item"><a href="' . site_url() . '/add-listing/" title="' . __( 'Add Listing', $this->text_domain ) . '">' . __( 'Add Listing', $this->text_domain ) . '</a></li>';
            $nav .= '</ul>';

            $nav .= substr( $list, $break );
            return $nav;
        }
        return $list;
    }


    /**
     * including JS/CSS
     **/
    function add_js_css() {
        //including CSS
        wp_enqueue_style( 'dr_style', $this->plugin_url . 'ui-front/style.css' );
    }

    /**
     * Hide some menu pages
     */
    function hide_menu_pages( $args ) {
        if ( is_user_logged_in() ) {

            $pages = '';
            $directory_page = $this->get_page_by_meta( 'signup' );
            if ( isset( $directory_page) && 0 < $directory_page->ID )
                $pages .= $directory_page->ID;
            $directory_page = $this->get_page_by_meta( 'signin' );
            if ( isset( $directory_page) && 0 < $directory_page->ID )
                $pages .= ','.$directory_page->ID;

            if ( isset( $args['exclude'] ) )
                $args['exclude'] .= ',' . $pages;
            else
                $args['exclude'] = $pages;
        }
        return $args;
    }

    /**
     * Intiate plugin.
     *
     * @return void
     */
    function init() {

        /*
        handle_action_buttons_requests
        If your want to go to admin profile
        */
        if ( isset( $_POST['redirect_profile'] ) ) {
            wp_redirect( admin_url() . 'profile.php' );
            exit();
        }
        elseif ( isset( $_POST['redirect_listing'] ) ) {
            wp_redirect( site_url() . '/add-listing' );
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

        $directory_listing_default = array(
            'public' => ( get_option( 'dp_options' ) ) ? false : true,
            'rewrite' => array( 'slug' => 'listings', 'with_front' => false ),
            'has_archive' => true,

            'capability_type' => 'listing',
            'capabilities' => array( 'edit_posts' => 'edit_published_listings' ),
            'map_meta_cap' => true,

            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', /*'custom-fields',*/ 'comments', 'revisions', /*'post-formats'*/ ),

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
        );


        //Register directory_listing post type
        if ( class_exists( 'CustomPress_Core_Admin' ) ) {
            //for CustomPress plugin
            $ct_custom_post_types = get_option( 'ct_custom_post_types' );

            if ( isset( $ct_custom_post_types['directory_listing'] ) ) {
                //use new values for directory_listing post type
                $ct_custom_post_types['directory_listing'] = array_merge( $directory_listing_default, $ct_custom_post_types['directory_listing'] );

                //don't change position in menu
                if ( isset( $ct_custom_post_types['directory_listing']['menu_position'] ) && 0 == $ct_custom_post_types['directory_listing']['menu_position'] )
                    $ct_custom_post_types['directory_listing']['menu_position'] = '';

                register_post_type( 'directory_listing', $ct_custom_post_types['directory_listing'] );
            } else {
                register_post_type( 'directory_listing', $directory_listing_default );
                $ct_custom_post_types['directory_listing'] = $directory_listing_default;
                update_option( 'ct_custom_post_types', $ct_custom_post_types );
            }
        } else {
            //without CustomPress plugin
            register_post_type( 'directory_listing', $directory_listing_default );
        }

        register_taxonomy( 'listing_tag', 'directory_listing', array(
            'rewrite'       => array( 'slug' => 'listings-tag', 'with_front' => false ),
            'capabilities'  => array( 'assign_terms' => 'edit_published_listings' ),
            'labels'        => array(
                'name'            => __( 'Listing Tags', $this->text_domain ),
                'singular_name'    => __( 'Listing Tag', $this->text_domain ),
                'search_items'    => __( 'Search Listing Tags', $this->text_domain ),
                'popular_items'    => __( 'Popular Listing Tags', $this->text_domain ),
                'all_items'        => __( 'All Listing Tags', $this->text_domain ),
                'edit_item'        => __( 'Edit Listing Tag', $this->text_domain ),
                'update_item'    => __( 'Update Listing Tag', $this->text_domain ),
                'add_new_item'    => __( 'Add New Listing Tag', $this->text_domain ),
                'new_item_name'    => __( 'New Listing Tag Name', $this->text_domain ),
                'separate_items_with_commas'    => __( 'Separate listing tags with commas', $this->text_domain ),
                'add_or_remove_items'            => __( 'Add or remove listing tags', $this->text_domain ),
                'choose_from_most_used'            => __( 'Choose from the most used listing tags', $this->text_domain ),
            )
        ) );

        register_taxonomy( 'listing_category', 'directory_listing', array(
            'rewrite'       => array( 'slug' => 'listings-category', 'with_front' => false, 'hierarchical' => true ),
            'capabilities'  => array( 'assign_terms' => 'edit_published_listings' ),
            'hierarchical'  => true,
            'labels' => array(
                'name'            => __( 'Listing Categories', $this->text_domain ),
                'singular_name'    => __( 'Listing Category', $this->text_domain ),
                'search_items'    => __( 'Search Listing Categories', $this->text_domain ),
                'popular_items'    => __( 'Popular Listing Categories', $this->text_domain ),
                'all_items'        => __( 'All Listing Categories', $this->text_domain ),
                'parent_item'    => __( 'Parent Category', $this->text_domain ),
                'edit_item'        => __( 'Edit Listing Category', $this->text_domain ),
                'update_item'    => __( 'Update Listing Category', $this->text_domain ),
                'add_new_item'    => __( 'Add New Listing Category', $this->text_domain ),
                'new_item_name'    => __( 'New Listing Category', $this->text_domain ),
                'parent_item_colon'        => __( 'Parent Category:', $this->text_domain ),
                'add_or_remove_items'    => __( 'Add or remove listing categories', $this->text_domain ),
            )
        ) );

        //import data from old plugin version
        $this->import_from_old();

    }



    /**
     * Create the default Directory pages.
     *
     * @return void
     **/
    function create_default_pages() {
        /* Create neccasary pages */

        $directory_page = $this->get_page_by_meta( 'listings' );
        if ( !$directory_page || 0 >= $directory_page->ID ) {
            $current_user = wp_get_current_user();
            /* Construct args for the new post */
            $args = array(
                'post_title'     => 'Listings',
                'post_status'    => 'publish',
                'post_author'    => $current_user->ID,
                'post_type'      => 'page',
                'ping_status'    => 'closed',
                'comment_status' => 'closed'
            );
            $parent_id = wp_insert_post( $args );
            add_post_meta( $parent_id, "directory_page", "listings" );
        }

        $directory_page = $this->get_page_by_meta( 'signup' );
        if ( !$directory_page || 0 >= $directory_page->ID ) {
            $current_user = wp_get_current_user();
            /* Construct args for the new post */
            $args = array(
                'post_title'     => 'Signup',
                'post_status'    => 'publish',
                'post_author'    => $current_user->ID,
                'post_type'      => 'page',
                'ping_status'    => 'closed',
                'comment_status' => 'closed'
            );
            $parent_id = wp_insert_post( $args );
            add_post_meta( $parent_id, "directory_page", "signup" );
        }

        $directory_page = $this->get_page_by_meta( 'signin' );
        if ( !$directory_page || 0 >= $directory_page->ID ) {
            $current_user = wp_get_current_user();
            /* Construct args for the new post */
            $args = array(
                'post_title'     => 'Signin',
                'post_status'    => 'publish',
                'post_author'    => $current_user->ID,
                'post_type'      => 'page',
                'ping_status'    => 'closed',
                'comment_status' => 'closed'
            );
            $parent_id = wp_insert_post( $args );
            add_post_meta( $parent_id, "directory_page", "signin" );
        }

    }

    /**
     * Get page by meta value
     *
     * @return int $page[0] /bool false
     */
    function get_page_by_meta( $value ) {
        $post_statuses = array( 'publish', 'trash', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit' );
        foreach ( $post_statuses as $post_status ) {
            $args = array(
                    'hierarchical'  => 0,
                    'meta_key'      => 'directory_page',
                    'meta_value'    => $value,
                    'post_type'     => 'page',
                    'post_status'   => $post_status
                );

            $page = get_pages( $args );

            if ( isset( $page[0] ) && 0 < $page[0]->ID )
                return $page[0];
        }
        return false;
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

                $new_taxonomies = array();

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

                    if ( !in_array( 'directory_listing', $old_taxonomy_args['object_type'] ) ) {
                        $new_taxonomies[$old_taxonomy] = $old_taxonomy_args;
                    }
                }
                //delete old directory taxonomies
                update_option( 'ct_custom_taxonomies', $new_taxonomies );
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
            delete_option( 'module_payments' );
            delete_option( 'ct_flush_rewrite_rules' );
            delete_site_option( 'dp_options' );
            delete_site_option( 'module_payments' );
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

        if ( wp_next_scheduled( 'check_expiration_dates' ) ) {
            wp_clear_scheduled_hook( 'check_expiration_dates' );
        }

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
        //TODO: do it when unistall plugin.
        if ( false ) {
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
            }
        }

        // Redirect template loading to archive-listing.php rather than to archive.php
        if ( is_dr_page( 'tag' ) || is_dr_page( 'category' ) ) {
            $wp_query->set( 'post_type', 'directory_listing' );
        }
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


    /**
     * Check if user can edit listing
     */
    function user_can_edit_listing( $post_id ) {
        if ( 0 < $post_id ) {
            $post = get_post( $post_id, ARRAY_A );
            if ( current_user_can( 'edit_published_listings' ) && get_current_user_id() == $post['post_author']  )
                return true;
        }
        return false;
    }

    /**
     * Update or insert listiong if no ID is passed.
     *
     * @param array $params Array of $_POST data
     * @param array|NULL $file Array of $_FILES data
     * @return int $post_id
     **/
    function update_listing( $params ) {

        /* Construct args for the new post */
        $args = array(
            /* If empty ID insert Listing insetad of updating it */
            'ID'             => ( isset( $params['listing_data']['ID'] ) ) ? $params['listing_data']['ID'] : '',
            'post_title'     => $params['listing_data']['post_title'],
            'post_content'   => $params['listing_data']['post_content'],
            'post_status'    => $params['listing_data']['post_status'],
            'post_author'    => get_current_user_id(),
            'post_type'      => 'directory_listing',
            'ping_status'    => 'closed',
            'comment_status' => 'closed'
        );

        /* Insert page and get the ID */
        $post_id = wp_insert_post( $args );

        if ( $post_id ) {
            //Set object terms - category
            if ( isset( $params['tax_input']['listing_category'] ) ) {
                foreach ( $params['tax_input']['listing_category'] as $term_id  ) {
                    if ( 0 < $term_id ) {
                        $term       = get_term_by( 'term_id', $term_id, 'listing_category', ARRAY_A );
                        $terms[]    = $term['name'];
                    }
                }
                wp_set_object_terms( $post_id, $terms, 'listing_category' );
            }

            if ( class_exists( 'CustomPress_Content_Types' ) ) {
                global $CustomPress_Content_Types;
                $CustomPress_Content_Types->save_custom_fields( $post_id );
            }

            return $post_id;
       }
    }


    /**
     * Handle $_REQUEST for main pages.
     *
     * @uses set_query_var() For passing variables to pages
     * @return void|die() if "_wpnonce" is not verified
     **/
    function handle_page_requests() {
        global $wp_query;

        /* Handles request for my-classifieds page */
        if ( 'add-listing' == $wp_query->query_vars['pagename'] || 'edit-listing' == $wp_query->query_vars['pagename'] ) {
            if ( isset( $_POST['update_listing'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'verify' ) ) {
                    //Update listing
                    $this->update_listing( $_POST );

                    if ( 'add-listing' == $wp_query->query_vars['pagename'] )
                        wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'New Listing is added!', '' ) ) ), site_url() . '/my-listings' ) );
                    else
                        wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'Listing is updated!', '' ) ) ), site_url() . '/my-listings' ) );

                    exit;

            } elseif ( isset( $_POST['cancel_listing'] ) ) {
                wp_redirect( site_url() . '/my-listings' );
                exit;
            }
        }

        /* Handles request for my-classifieds page */
        if ( 'my-listings' == $wp_query->query_vars['pagename'] ) {
            if ( isset( $_POST['action'] ) && 'delete_listing' ==  $_POST['action'] && wp_verify_nonce( $_POST['_wpnonce'], 'action_verify' ) ) {
                if ( $this->user_can_edit_listing( $_POST['post_id'] ) ) {
                    wp_delete_post( $_POST['post_id'] );
                    wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'Listing is deleted!', '' ) ) ), site_url() . '/my-listings' ) );
                    exit;
                }
            }
        }
    }


    //scans post type at template_redirect to apply custom themeing to listings
    function load_directory_templates() {
        global $wp_query;

        //load proper theme for home listing page
        if ( $wp_query->is_home ) {

            $templates = array( 'home-listing.php' );

            //if custom template exists load it
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            }
        }

        //load proper theme for archive listings page
         elseif ( is_dr_page( 'archive' ) ) {
            //defaults
            $templates = array( 'dr_listings.php' );

            //if custom template exists load it
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            } else {
                //otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
                $wp_query->is_page = 1;
                $wp_query->is_404 = null;
                $wp_query->post_count = 1;

                add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
                add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
                add_filter( 'the_title', array( &$this, 'page_title_output' ), 99, 2 );
                $this->dr_first_thumbnail = true;
                add_filter( 'post_thumbnail_html', array( &$this, 'delete_first_thumbnail' ) );
                add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 99 );
                add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 99 );
            }

            $this->is_directory_page = true;
        }

        //load proper theme for single listing page display
        elseif ( $wp_query->is_single &&  'directory_listing' == $wp_query->query_vars['post_type'] ) {

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
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            } else {
                //otherwise load the page template and use our own theme
                $wp_query->is_single    = null;
                $wp_query->is_page      = 1;
                add_filter( 'the_title', array( &$this, 'delete_post_title' ), 99 );
                add_filter( 'the_content', array( &$this, 'listing_content' ), 99 );
            }

            $this->is_directory_page = true;
        }

        //load proper theme for listing category or tag
        elseif ( 'listing_category' == $wp_query->query_vars['taxonomy'] || 'listing_tag' == $wp_query->query_vars['taxonomy'] ) {
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
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            } else {
                //otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
                $wp_query->is_page = 1;
                $wp_query->is_404 = null;
                $wp_query->post_count = 1;

                add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
                add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
                add_filter( 'the_title', array( &$this, 'page_title_output' ), 99, 2 );
                $this->dr_first_thumbnail = true;
                add_filter( 'post_thumbnail_html', array( &$this, 'delete_first_thumbnail' ) );
                add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 99 );
                add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 99 );
            }

            $this->is_directory_page = true;
        }

        //load proper theme for single listing page display
        elseif ( 'my-listings' == $wp_query->query_vars['pagename'] ) {

            if ( !current_user_can( 'edit_published_listings' ) ) {
                wp_redirect( 'listings/');
                exit;
            }

            $templates = array( 'page-my-listings.php' );

            //if custom template exists load it
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            } else {
                //otherwise load the page template and use our own theme
                $wp_query->is_404 = false;
                $wp_query->is_single    = null;
                $wp_query->is_page      = 1;
                add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
                add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
                add_filter( 'the_title', array( &$this, 'page_title_output' ), 99 );
                add_filter( 'the_content', array( &$this, 'my_listings_content' ), 99 );
            }
            $this->is_directory_page = true;
        }

        //load proper theme for single listing page display
        elseif ( 'add-listing' == $wp_query->query_vars['pagename'] || 'edit-listing' == $wp_query->query_vars['pagename'] ) {

            if ( !current_user_can( 'edit_published_listings' ) ) {
                wp_redirect( 'listings/');
                exit;
            }

            $templates = array( 'page-update-listing.php' );

            //if custom template exists load it
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            } else {
                //otherwise load the page template and use our own theme
                $wp_query->is_404 = false;
                $wp_query->is_single    = null;
                $wp_query->is_page      = 1;
                add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
                add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
                add_filter( 'the_title', array( &$this, 'page_title_output' ), 99 );
                add_filter( 'the_content', array( &$this, 'update_listing_content' ), 99 );
            }
            $this->is_directory_page = true;
        }

        //load proper theme for signin listing page
        elseif ( is_page( 'signin' ) ) {

            $templates = array( 'page-signin.php' );

            //if custom template exists load it
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            } else {
                //fix for Infinite SEO (output buffering)
                $GLOBALS['post']->post_excerpt = 'signin';

                //otherwise load the page template and use our own theme
                $wp_query->is_single    = null;
                $wp_query->is_page      = 1;
                add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
                add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
                add_filter( 'the_title', array( &$this, 'delete_post_title' ), 99 );
                add_filter( 'the_content', array( &$this, 'signin_content' ), 99 );
            }
            $this->is_directory_page = true;
        }

        //load proper theme for signup listing page
        elseif ( is_page( 'signup' ) ) {

            $templates = array( 'page-signup.php' );

            //if custom template exists load it
            if ( $this->directory_template = locate_template( $templates ) ) {
                add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
            } else {
                //fix for Infinite SEO (output buffering)
                $GLOBALS['post']->post_excerpt = 'signup';

                //otherwise load the page template and use our own theme
                $wp_query->is_single    = null;
                $wp_query->is_page      = 1;
                add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
                add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
                add_filter( 'the_title', array( &$this, 'delete_post_title' ), 99 );
                add_filter( 'the_content', array( &$this, 'signup_content' ), 99 );
            }
            $this->is_directory_page = true;
        }


        //load  specific items
        if ( $this->is_directory_page ) {
            add_filter( 'edit_post_link', array( &$this, 'delete_edit_post_link' ), 99 );

            //prevents 404 for virtual pages
            status_header( 200 );
        }
    }


    //filter the custom single listing template
    function custom_directory_template( $template ) {
        return $this->directory_template;
    }


    //content for single listing
    function listing_content( $content ) {
        global $post, $current_user;
        //don't filter outside of the loop
        if ( !in_the_loop() )
            return $content;

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
                <span class="tags"><?php the_tags( __( 'Tags: ', DR_TEXT_DOMAIN ), ', ', ''); ?></span>

                <?php if ( !is_user_logged_in() ) : ?>
                    <?php echo '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to rate item.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>'; ?>
                <?php else: ?>
                    <?php do_action('sr_rate_this'); ?>
                <?php endif; ?>
            </div>

            <div class="custom-fields">
                <?php $this->display_custom_fields_values(); ?>
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

        <?php
            $new_content = ob_get_contents();
        ob_end_clean();

        return $new_content;
    }


    //content for my-listings page
    function my_listings_content( $content ) {
        ob_start();
            include( $this->plugin_dir . 'ui-front/general/page-my-listings.php' );
            $new_content = ob_get_contents();
        ob_end_clean();
        return $new_content;
    }


    //content for add/edit listing page
    function update_listing_content( $content ) {
        ob_start();
            include( $this->plugin_dir . 'ui-front/general/page-update-listing.php' );
            $new_content = ob_get_contents();
        ob_end_clean();
        return $new_content;
    }

    //content for signup page
    function signup_content( $content ) {
        ob_start();
            include( $this->plugin_dir . 'ui-front/general/page-signup.php' );
            $new_content = ob_get_contents();
        ob_end_clean();
        return $new_content;
    }

    //content for singin page
    function signin_content( $content ) {
        ob_start();
            include( $this->plugin_dir . 'ui-front/general/page-signin.php' );
            $new_content = ob_get_contents();
        ob_end_clean();
        return $new_content;
    }

    //content for listings list
    function listing_list_theme( $content ) {
        //don't filter outside of the loop
        if ( !in_the_loop() )
            return $content;

        $content = $this->dr_listing_list( false );
        $content .= get_posts_nav_link();

        return $content;
    }

    //generate listing list content
    function dr_listing_list( $echo = true, $paginate = '', $page = '', $per_page = '', $order_by = '', $order = '', $category = '', $tag = '' ) {
        global $wp_query;

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


        //breadcrumbs
        if ( !is_dr_page( 'archive' ) ) {
            $content = '<div class="breadcrumbtrail">';
            $content .= '   <p class="page-title dp-taxonomy-name">';

            ob_start();
                 the_dr_breadcrumbs();
                $content .= ob_get_contents();
            ob_end_clean();

            $content .= '   </p>';
            $content .= '   <div class="clear"></div>';
            $content .= '</div> ';
        }

        $content .= '<div id="dr_listing_list">';

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
                    $class = 'dr_listing last-listing';
                else
                    $class = 'dr_listing';

                $content .= '<div class="' . $class . '">';

                $content .= '   <div class="entry-post">';
                $content .= '       <h2 class="entry-title">';
                $content .= '           <a href="' . get_permalink( $post->ID ) . '" title="' . sprintf( esc_attr__( 'Permalink to %s', DR_TEXT_DOMAIN ), $post->post_title ) . '" rel="bookmark">' . $post->post_title . '</a>';
                $content .= '       </h2>';
                $content .= '       <div class="entry-summary">';
                $content .=         get_the_post_thumbnail( $post->ID, array( 50, 50 ), array( 'class' => 'alignleft dr_listing_image_lising', 'title' => $post->post_title  ) );
                $content .=         $this->listing_excerpt( $post->post_excerpt, $post->post_content, $post->ID );
                $content .= '       </div>';
                $content .= '       <div class="clear"></div>';
                $content .= '   </div>';


                $content .= '   <div class="entry-meta">';

                ob_start();
                    the_dr_posted_on();
                    $content .= ob_get_contents();
                ob_end_clean();

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
                <?php
                $content .= ob_get_contents();
                ob_end_clean();

                $content .= '       </div>';
                $content .= '   </div>';
                $content .= '</div>';

                $count++;
            }
        } else {
            $content .= '<div id="dr_no_listings">' . apply_filters( 'dr_listing_list_none', __( 'No Listings', DR_TEXT_DOMAIN ) ) . '</div>';
        }

        $content .= '</div>';

        if ( $echo )
            echo $content;
        else
            return $content;
    }

    //replaces wp_trim_excerpt in our custom loops
    function listing_excerpt( $excerpt, $content, $post_id ) {
        $excerpt_more = ' <a class="dr_listing_more_link" href="' . get_permalink( $post_id ) . '">' .  __( 'More Info &raquo;', $this->text_domain ) . '</a>';
        if ( $excerpt ) {
            return $excerpt . $excerpt_more;
        } else {
            $text = strip_shortcodes( $content );
            //$text = apply_filters('the_content', $text);
            $text = str_replace( ']]>', ']]&gt;', $text );
            $text = strip_tags( $text );
            $excerpt_length = apply_filters( 'excerpt_length', 55 );
            $words = preg_split( "/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
            if ( count( $words ) > $excerpt_length ) {
                array_pop( $words );
                $text = implode(' ', $words);
                $text = $text . $excerpt_more;
            } else {
                $text = implode( ' ', $words );
            }
        }
        return $text;
    }

    //delete first image for lising on catregory/tag page (fixed duplication)
    function delete_first_thumbnail( $html ) {
        if ( $this->dr_first_thumbnail ) {
            $this->dr_first_thumbnail = false;
            return '';
        } else {
            return $html;
        }
    }

    //filters the titles for our custom pages
    function delete_post_title( $title, $id = false ) {
        global $wp_query;
        if ( $title == $wp_query->post->post_title ) {
            return '';
        }
        return $title;
    }

    //close comments
    function close_comments( $open ) {
        return false;
    }

    //filters the comments close text
    function comments_closed_text( $text ) {
        return '';
    }

    //filters the edit post button
    function delete_edit_post_link( $text ) {
        return '';
    }

    //filters the titles for our custom pages
    function page_title_output( $title, $id = false ) {
        global $wp_query;

        //filter out nav titles
        if ( !empty( $title ) && $id === false )
            return $title;

        //taxonomy pages
        if (  ( 'listing_category' == $wp_query->query_vars['taxonomy']  || 'listing_tag' == $wp_query->query_vars['taxonomy'] ) && $wp_query->post->ID == $id ) {
            if ( 'listing_category' == $wp_query->query_vars['taxonomy'] ) {
                $term = get_term_by( 'slug', get_query_var( 'listing_category' ), 'listing_category' );
                return sprintf( __( 'Listing Category: %s', $this->text_domain ), $term->name );
            } elseif ( 'listing_tag' == $wp_query->query_vars['taxonomy'] ) {
                $term = get_term_by( 'slug', get_query_var( 'listing_tag' ), 'listing_tag' );
                return sprintf( __( 'Listing Tag: %s', $this->text_domain ), $term->name );
            }
        }

        //title for listings page
        if ( is_dr_page( 'archive' ) && $wp_query->post->ID == $id ) {
            return __( 'Listings', $this->text_domain );
        }
        if ( 'my-listings' == $wp_query->query_vars['pagename'] ) {
            return __( 'My Listings', $this->text_domain );
        }

        if ( 'add-listing' == $wp_query->query_vars['pagename'] ) {
            return __( 'Add Listing', $this->text_domain );
        }

        if ( 'edit-listing' == $wp_query->query_vars['pagename'] ) {
            return __( 'Edit Listing', $this->text_domain );
        }

        return $title;
    }


    //display custom fields
    function display_custom_fields_values( ) {
        global $wp_query;

        include( $this->plugin_dir . 'ui-front/general/display-custom-fields-values.php' );

    }

}

/* Initiate Class */
new DR_Core();