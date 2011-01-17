<?php

/**
 * Setup the Colors page inside WordPress Appearance Menu
 */
if ( !class_exists('Directory_Theme_Colors') ):
class Directory_Theme_Colors
{
    /**
     * The current page. Used for custom hooks. 
     * @var string
     */
    var $page;

    /**
     * Class constructor. 
     */
    function Directory_Theme_Colors() {
		add_action( 'init', array( &$this, 'init' ) );
	}

    /**
     * Init hooks.
     */
	function init() {
        add_action( 'admin_init', array( &$this, 'register_color_settings' ) );
        add_action( 'admin_menu', array( &$this, 'add_page' ) );
        add_action( 'admin_init', array( &$this, 'page_init' ) );
	}

    /**
     * Init plugin options to white list our options.
     */
    function register_color_settings(){
        register_setting( 'dir_colors_group', 'dir_colors' );
    }

    /**
     * Load up the menu page.
     */
    function add_page() {
       $this->page = add_theme_page( __('Colors', 'directory'), __('Colors', 'directory'), 'edit_theme_options', 'colors', array( &$this, 'output_admin_page' ) );
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
            $msg = __( 'Colors Saved!', 'directory' ); ?>

        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo get_current_theme() . ' ' . __('Colors', 'directory') ?></h2>

            <?php if ( isset( $msg ) ) : ?>
            <div class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields('dir_colors_group'); ?>
                <?php $colors = get_option('dir_colors'); ?>
                <input type="hidden" name="dir_colors[enable]" value="1" />
                <table class="form-table">
                    <tr>
                        <th><?php _e('Site Title', 'directory'); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[site_title]" value="<?php if ( isset( $colors['site_title'] )) echo $colors['site_title']; else echo '#000000'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[site_title_ud]" value="1" <?php if ( $colors['site_title_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Header Bar One', 'directory'); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[header_bar_one]" value="<?php if ( isset( $colors['header_bar_one'] )) echo $colors['header_bar_one']; else echo '#4C4B4B'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[header_bar_one_ud]" value="1" <?php if ( $colors['header_bar_one_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Header Bar Two', 'directory'); ?></th>
                        <td  class="colors">
                            <input class="colors_field" type="text" name="dir_colors[header_bar_two]" value="<?php if ( isset( $colors['header_bar_two'] )) echo $colors['header_bar_two']; else echo '#727171'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[header_bar_two_ud]" value="1" <?php if ( $colors['header_bar_two_ud'] == 1 ) echo 'checked="checked"'; ?>  /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Content Wrapper', 'directory'); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[content_wrapper]" value="<?php if ( isset( $colors['content_wrapper'] )) echo $colors['content_wrapper']; else echo '#FFFFFF'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[content_wrapper_ud]" value="1" <?php if ( $colors['content_wrapper_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Above And Below Listings Bars', 'directory'); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[listing_bars]" value="<?php if ( isset( $colors['listing_bars'] )) echo $colors['listing_bars']; else echo '#727171'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[listing_bars_ud]" value="1" <?php if ( $colors['listing_bars_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Main Buttons', 'directory'); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[btns_bgr]" value="<?php if ( isset( $colors['btns_bgr'] )) echo $colors['btns_bgr']; else echo '#D3401A'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[btns_bgr_ud]" value="1" <?php if ( $colors['btns_bgr_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Main Buttons Text', 'directory'); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[btns_txt]" value="<?php if ( isset( $colors['btns_txt'] )) echo $colors['btns_txt']; else echo '#FFFFFF'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[btns_txt_ud]" value="1" <?php if ( $colors['btns_txt_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Category Headings', 'directory'); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[cath]" value="<?php if ( isset( $colors['cath'] )) echo $colors['cath']; else echo '#D3401A'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[cath_ud]" value="1" <?php if ( $colors['cath_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Global Text' ); ?></th>
                        <td class="colors">
                            <input class="colors_field" type="text" name="dir_colors[global_txt]" class="colors" value="<?php if ( isset( $colors['global_txt'] )) echo $colors['global_txt']; else echo '#666666'; ?>" />
                            <a href="#"><?php _e('Select Color', 'directory'); ?></a>
                            <input class="use_default" type="checkbox" name="dir_colors[global_txt_ud]" value="1" <?php if ( $colors['global_txt_ud'] == 1 ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', 'directory'); ?>
                            <div class="colorpicker"></div>
                        </td>
                    </tr>     
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" name="save_colors" value="<?php _e('Save Changes', 'directory'); ?>" />
                </p>
            </form>
        </div> <?php
    }
}
endif;

if ( class_exists('Directory_Theme_Colors') )
	$__directory_theme_colors = new Directory_Theme_Colors();

?>