<?php
/**
 * Classifieds Core Admin Class
 */
if ( !class_exists('Content_Types_Core_Admin') ):
class Content_Types_Core_Admin extends Content_Types_Core {

    /** @var string Current page hook */
    var $hook;

    /**
     * Constructor.
     **/
    function Content_Types_Core_Admin( $parent_menu_slug ) {
        $this->init();
        $this->init_vars();
        $this->parent_menu_slug = $parent_menu_slug;
    }

    /**
     * 
     */
    function init() {
        add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );
        add_action( 'admin_init', array( &$this, 'get_hook' ) );
        add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_custom_field_styles') );
        add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_custom_field_styles') );
    }

    /**
     * Register submodule submenue
     *
     * @return void
     **/
    function admin_menu() {
        add_submenu_page( $this->parent_menu_slug , __( 'Content Types', $this->text_domain ), __( 'Content Types', $this->text_domain ), 'edit_users', 'ct_content_types', array( &$this, 'handle_admin_requests' ) );
    }

    /**
     * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
     *
     * @return void
     **/
    function get_hook() {
        $page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : NULL;
        $this->hook = get_plugin_page_hook( $page , $this->parent_menu_slug );
        add_action( 'admin_print_styles-' .  $this->hook, array( &$this, 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $this->hook, array( &$this, 'enqueue_scripts' ) );
    }

    /**
     * Load styles on plugin admin pages only.
     */
    function enqueue_styles() {
        wp_enqueue_style( 'ct-admin-styles',
                           $this->submodule_url . 'ui-admin/css/styles.css');
    }

    /**
     * Load scripts on plugin specific admin pages only.
     */
    function enqueue_scripts() {
        wp_enqueue_script( 'ct-admin-scripts',
                            $this->submodule_url . 'ui-admin/js/scripts.js',
                            array( 'jquery' ) );
    }

    /**
     * Load styles for "Custom Fields" on add/edit post type pages only.
     *
     * @return void
     **/
    function enqueue_custom_field_styles() {
        wp_enqueue_style( 'ct-admin-custom-field-styles',
                           $this->submodule_url . 'ui-admin/css/custom-fields-styles.css' );
    }

    /**
     * Handle admin page requests.
     *
     * @return void
     */
    function handle_admin_requests() {

        if ( isset( $_GET['page'] ) && $_GET['page'] == 'ct_content_types' ) {
            $this->render_admin('content-types');
            
            if ( isset( $_GET['ct_content_type'] ) && $_GET['ct_content_type'] == 'post_type' || !isset( $_GET['ct_content_type'] ) ) {
                if ( isset( $_GET['ct_add_post_type'] ) )
                    $this->render_admin('add-post-type');
                elseif ( isset( $_GET['ct_edit_post_type'] ) )
                    $this->render_admin('edit-post-type');
                elseif ( isset( $_GET['ct_delete_post_type'] ) )
                    $this->render_admin('delete-post-type');
                else 
                    $this->render_admin('post-types');
            }
            elseif ( $_GET['ct_content_type'] == 'taxonomy' ) {
                if ( isset( $_GET['ct_add_taxonomy'] ))
                    $this->render_admin('add-taxonomy');
                elseif ( isset( $_GET['ct_edit_taxonomy'] ) )
                    $this->render_admin('edit-taxonomy');
                elseif ( isset( $_GET['ct_delete_taxonomy'] ) )
                    $this->render_admin('delete-taxonomy');
                else
                    $this->render_admin('taxonomies');
            }
            elseif ( $_GET['ct_content_type'] == 'custom_field' ) {
                if ( isset( $_GET['ct_add_custom_field'] )) {
                    $this->render_admin('add-custom-field');
                }
                elseif ( isset( $_GET['ct_edit_custom_field'] ) ) {
                    $this->render_admin('edit-custom-field');
                }
                elseif ( isset( $_GET['ct_delete_custom_field'] )) {
                    $this->render_admin('delete-custom-field');
                }
                else {
                    $this->render_admin('custom-fields');
                }
            }
        }
    }
}
endif;

?>