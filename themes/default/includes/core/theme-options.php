<?php

/**
 * Setup the Colors page inside WordPress Appearance Menu
 */
if ( !class_exists('Directory_Theme_Options') ):
class Directory_Theme_Options
{
    /** @var string The current page. Used for custom hooks. */
    var $page;

    /**
     * Class constructor. 
     */
    function Directory_Theme_Options() {
		add_action( 'init', array( &$this, 'init' ) );
	}

    /**
     * Init hooks.
     */
	function init() {
        add_action( 'admin_init', array( &$this, 'register_color_settings' ) );
        add_action( 'admin_menu', array( &$this, 'add_page' ) );
        add_action( 'admin_init', array( &$this, 'page_init' ) );
        add_action( 'dp_theme_options', array( &$this, 'output_theme_options' ) );
	}

    /**
     * Init plugin options to white list our options.
     */
    function register_color_settings() {
        register_setting( 'dir_options_group', 'dir_options' );
    }

    /**
     * Load up the menu page.
     */
    function add_page() {
       $this->page = add_theme_page( __('Options', 'directory'), __('Options', 'directory'), 'edit_theme_options', 'options', array( &$this, 'output_admin_page' ) );
    }

    /**
     * Load scripts and styles.
     */
    function page_init() {
        add_action( 'admin_print_styles-'  . $this->page, array( &$this, 'enqueue_styles' ));
        add_action( 'admin_print_scripts-' . $this->page, array( &$this, 'enqueue_scripts' ));
        add_action( 'admin_head-' . $this->page, array( &$this, 'print_admin_style' ));
        add_action( 'admin_head-' . $this->page, array( &$this, 'print_admin_scripts' ));
    }

    /**
     * Enqueue styles.
     */
    function enqueue_styles() {
        wp_enqueue_style('farbtastic');
    }

    /**
     * Enqueue scripts.
     */
    function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('farbtastic');
    }

    /**
     * Print document styles.
     */
    function print_admin_style() { ?>
        <style type="text/css">
            div.colorpicker { z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none; }
            input.use_default { margin-left: 10px; }
        </style> <?php
    }

    /**
     * Print document scripts. Handles the colorpickers.
     */
    function print_admin_scripts() { ?>
        <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function($) {
           $(this).mousedown(function() {
                $(this).find('.colorpicker').each(function() {
                    var display = $(this).css('display');
                    if (display == 'block') {
                        $(this).fadeOut(2);
                    }
                });
            });
            $('.colors').each(function() {
                $(this).children('.colorpicker').farbtastic($(this).children('.colors_field'));
                $(this).children('a').click(function() {
                    $(this).parent().children('.colorpicker').fadeIn(2);
                });
            });
        });
        //]]>
        </script> <?php
    }

    /**
     * Output the options page
     */
    function output_admin_page() {
        if ( isset( $_REQUEST['updated'] ) )
            $msg = __( 'Options Saved!', 'directory' ); ?>

        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo get_current_theme() . ' ' . __('Options', 'directory') ?></h2>

            <?php if ( isset( $msg ) ) : ?>
            <div class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields('dir_options_group'); ?>
                <?php $options = get_option('dir_options'); ?>
                <input type="hidden" name="dir_colors[enable]" value="1" />
                <h3><?php _e( 'Presentation', 'directory'  ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="text_shadows"><?php _e('Text Shadows', 'directory'); ?></label></th>
                        <td>
                            <input id="text_shadows" type="checkbox" name="text_shadows" value="1" <?php if ( !empty( $options['text_shadows'] ) ) echo 'checked="checked"'; ?> />
                            <span class="description"><?php _e('Switch text shadows ON', 'directory'); ?></span>
                        </td>
                    </tr>
                </table>
                <h3><?php _e( 'Layout', 'directory'  ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="layout"><?php _e('Layout', 'directory'); ?></label></th>
                        <td>
                            <select id="layout" name="layout">
                                <option><?php _e( 'Grid Layout', 'directory' ); ?></option>
                                <option><?php _e( 'Rows Layout', 'directory' ); ?></option>
                            </select>
                            <span class="description"><?php _e('Switch Theme Layout', 'directory'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="text_shadows"><?php _e('Styles', 'directory'); ?></label></th>
                        <td>
                            <select name="layout">
                                <option><?php _e( 'Style One', 'directory' ); ?></option>
                                <option><?php _e( 'Style Two', 'directory' ); ?></option>
                                <option><?php _e( 'Style Three', 'directory' ); ?></option>
                            </select>
                            <span class="description"><?php _e('Switch Theme Style', 'directory'); ?></span>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" name="save_colors" value="<?php _e('Save Changes', 'directory'); ?>" />
                </p>
            </form>
        </div> <?php
    }

    function output_theme_options() { 
        $options = get_option('dir_options');
        if ( empty( $options['text_shadows'] ) ): ?>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/notextshadows.css" type="text/css" media="all" />
        <?php endif;
    }
}
endif;

if ( class_exists('Directory_Theme_Options') )
	$__directory_theme_options = new Directory_Theme_Options();

?>
