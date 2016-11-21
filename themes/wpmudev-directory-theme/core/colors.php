<?php

/**
* DR_Theme_Colors
* Setup the Colors page inside WordPress Appearance Menu
*
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/
class DR_Theme_Colors {

	/**
	* Class constructor.
	*/
	function DR_Theme_Colors() {
		add_action( 'admin_init', array( &$this, 'register_color_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_page' ) );

		// Hook located in header.php
		add_action( 'dir_theme_colors', array( &$this, 'output_theme_colors' ) );
	}

	/**
	* Init plugin options to white list our options.
	*/
	function register_color_settings() {
		register_setting( 'dir_colors_group', 'dir_colors' );
	}

	/**
	* Load up the menu page.
	*/
	function add_page() {
		$page = add_theme_page( __('Colors', THEME_TEXT_DOMAIN), __('Colors', THEME_TEXT_DOMAIN), 'edit_theme_options', 'colors', array( &$this, 'output_admin_page' ) );

		add_action( 'admin_print_styles-'  . $page, array( &$this, 'enqueue_styles' ));
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'enqueue_scripts' ));
		add_action( 'admin_head-' . $page, array( &$this, 'print_admin_style' ));
		add_action( 'admin_head-' . $page, array( &$this, 'print_admin_scripts' ));
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
				$msg = __( 'Colors Saved!', THEME_TEXT_DOMAIN ); ?>

				<div class="wrap">
					<?php screen_icon(); ?>
					<h2><?php echo wp_get_theme()->Name . ' ' . __('Colors', THEME_TEXT_DOMAIN) ?></h2>

					<?php if ( isset( $msg ) ) : ?>
					<div class="updated fade"><p><strong><?php echo $msg; ?></strong></p></div>
					<?php endif; ?>

					<form method="post" action="options.php">
						<h3><?php _e( 'Colors', THEME_TEXT_DOMAIN  ); ?></h3>
						<?php settings_fields('dir_colors_group'); ?>
						<?php $colors = get_option('dir_colors'); ?>
						<label><input type="checkbox" name="dir_colors[enable]" value="1" <?php checked(! empty($colors['enable'] ) ); ?> /> <?php _e('Allow color overrides below.', THEME_TEXT_DOMAIN); ?></label>
						<table class="form-table">
							<tr>
								<th><?php _e('Site Title', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[site_title]" value="<?php if ( isset( $colors['site_title'] )) echo $colors['site_title']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[site_title_ud]"  <?php if ( !empty( $colors['site_title_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e('Navigation Bar', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[navigation_bar]" value="<?php if ( isset( $colors['navigation_bar'] )) echo $colors['navigation_bar']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[navigation_bar_ud]" value="1" <?php if ( !empty( $colors['navigation_bar_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e('Content Wrapper', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[content_wrapper]" value="<?php if ( isset( $colors['content_wrapper'] )) echo $colors['content_wrapper']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[content_wrapper_ud]" value="1" <?php if ( !empty( $colors['content_wrapper_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e('Search Box', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[search_box]" value="<?php if ( isset( $colors['search_box'] )) echo $colors['search_box']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[search_box_ud]" value="1" <?php if ( !empty( $colors['search_box_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e('Grid and Action Bars', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[ga_bars]" value="<?php if ( isset( $colors['ga_bars'] )) echo $colors['ga_bars']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[ga_bars_ud]" value="1" <?php if ( !empty( $colors['ga_bars_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e('Headings Bars', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[hbars]" value="<?php if ( isset( $colors['hbars'] )) echo $colors['hbars']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[hbars_ud]" value="1" <?php if ( !empty( $colors['hbars_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e('Buttons', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[btns]" value="<?php if ( isset( $colors['btns'] )) echo $colors['btns']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[btns_ud]" value="1" <?php if ( !empty( $colors['btns_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e('Buttons Text', THEME_TEXT_DOMAIN); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[btns_txt]" value="<?php if ( isset( $colors['btns_txt'] )) echo $colors['btns_txt']; else echo '#FFFFFF'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[btns_txt_ud]" value="1" <?php if ( !empty( $colors['btns_txt_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Global Text' ); ?></th>
								<td class="colors">
									<input class="colors_field" type="text" name="dir_colors[global_txt]" class="colors" value="<?php if ( isset( $colors['global_txt'] )) echo $colors['global_txt']; else echo '#666666'; ?>" />
									<a href="#"><?php _e('Select Color', THEME_TEXT_DOMAIN); ?></a>
									<input class="use_default" type="checkbox" name="dir_colors[global_txt_ud]" value="1" <?php if ( !empty( $colors['global_txt_ud'] ) ) echo 'checked="checked"'; ?> /> <?php _e('Use Default', THEME_TEXT_DOMAIN); ?>
									<div class="colorpicker"></div>
								</td>
							</tr>
						</table>
						<p class="submit">
							<input type="submit" class="button-primary" name="save_colors" value="<?php _e('Save Changes', THEME_TEXT_DOMAIN); ?>" />
						</p>
					</form>
				</div> <?php
			}

			/**
			* output_theme_colors
			*
			* @access public
			* @return void
			*/
			function output_theme_colors() {
				$colors = get_option('dir_colors');

				if ( isset( $colors['enable'] ) ) { ?>
					<style type="text/css">

						<?php if ( empty( $colors['site_title_ud'] ) && isset( $colors['site_title'] ) ): ?>
						/* Site Title Custom Colors */
						#site-logo a, #site-logo a:hover, #site-logo a:visited {
							color: <?php echo $colors['site_title']; ?> !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['navigation_bar_ud'] ) && isset( $colors['navigation_bar'] ) ): ?>
						/* Navigation Bar Custom Colors */
						#navigation-wrapper, #navigation {
							background: <?php echo $colors['navigation_bar']; ?> !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['content_wrapper_ud'] ) && isset( $colors['content_wrapper'] ) ): ?>
						/* Content Wrapper Custom Colors */
						#site-wrapper, .current_page_item {
							background: <?php echo $colors['content_wrapper']; ?> !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['search_box_ud'] ) && isset( $colors['search_box'] ) ): ?>
						/* Search Box Custom Colors */
						#searchbox {
							background: <?php echo $colors['search_box']; ?> !important;
							border: none !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['ga_bars_ud'] ) && isset( $colors['ga_bars'] ) ): ?>
						/* Grid and Action Bars Custom Colors */
						#action-bar, #content h2 {
							background: <?php echo $colors['ga_bars']; ?> !important;
						}
						#action-bar {
							border: none !important;
						}
						#content li {
							border-color: <?php echo $colors['ga_bars']; ?> !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['hbars_ud'] ) && isset( $colors['hbars'] ) ): ?>
						/* Headers Custom Colors */
						#action-bar span, #content h2, #searchbox label {
							color: <?php echo $colors['hbars']; ?> !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['btns_ud'] ) && isset( $colors['btns'] ) ): ?>
						/* Btns Custom Colors */
						a.button, a.button:visited, ul.button-nav li a, div.generic-button a,
						input[type="submit"], input[type="button"], button {
							background: <?php echo $colors['btns']; ?> !important;
							border: none !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['btns_txt_ud'] ) && isset( $colors['btns_txt'] ) ): ?>
						/* Btns Text Custom Colors */
						a.button, a.button:visited, ul.button-nav li a, div.generic-button a,
						input[type="submit"], input[type="button"], button {
							color: <?php echo $colors['btns_txt']; ?> !important;
						}
						<?php endif; ?>

						<?php if ( empty( $colors['global_txt_ud'] ) && isset( $colors['global_txt'] ) ): ?>
						/* Global Text Custom Colors */
						body {
							color: <?php echo $colors['global_txt']; ?> !important;
						}
						<?php endif; ?>

						</style> <?php
					}
				}
			}

			new DR_Theme_Colors();
