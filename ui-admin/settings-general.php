<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$options = $this->get_options('general_settings');
?>

<div class="wrap">

    <?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'general' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

    <h1><?php _e( 'General Settings', $this->text_domain ); ?></h1>

	<form action="" method="post">
         <div class="postbox">
            <h3 class='hndle'><span><?php _e( 'Redirection Options', $this->text_domain ) ?></span></h3>
            <div class="inside">
                <p class="description"><?php _e( 'Here you can set the page, to which will redirected the user after registration or login.', $this->text_domain ) ?></p>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="signin_url"><?php _e( 'Redirect URL (sign in):', $this->text_domain ) ?></label>
                        </th>
                        <td>
                            <input type="text" name="signin_url" id="signin_url" value="<?php echo $options['signin_url'] ? $options['signin_url'] : ''; ?>" size="50" />
                            <span class="description"><?php _e( 'by default to Home page', $this->text_domain ) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="logout_url"><?php _e( 'Redirect URL (logout):', $this->text_domain ) ?></label>
                        </th>
                        <td>
                            <input type="text" name="logout_url" id="logout_url" value="<?php echo $options['logout_url'] ? $options['logout_url'] : ''; ?>" size="50" />
                            <span class="description"><?php _e( 'by default to Home page', $this->text_domain ) ?></span>
                        </td>
                    </tr>
                </table>
            </div>
         </div>

         <div class="postbox">
            <h3 class='hndle'><span><?php _e( 'Display Options', $this->text_domain ) ?></span></h3>
            <div class="inside">
		        <table class="form-table">
                    <tr>
                        <th>
                            <label for="count_cat"><?php _e( 'Count of category:', $this->text_domain ) ?></label>
                        </th>
                        <td>
                            <input type="text" name="count_cat" id="count_cat" value="<?php echo ( isset( $options['count_cat'] ) ) ? $options['count_cat'] : '10'; ?>" size="2" />
                            <span class="description"><?php _e( 'a number of categories that will be displayed in the list of categories.', $this->text_domain ) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="count_sub_cat"><?php _e( 'Count of sub-category:', $this->text_domain ) ?></label>
                        </th>
                        <td>
                            <input type="text" name="count_sub_cat" id="count_sub_cat" value="<?php echo ( isset( $options['count_sub_cat'] ) ) ? $options['count_sub_cat'] : '5'; ?>" size="2" />
                            <span class="description"><?php _e( 'a number of sub-category that will be displayed for each category in the list of categories.', $this->text_domain ) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e( 'Empty sub-category:', $this->text_domain ) ?>
                        </th>
                        <td>
                            <input type="checkbox" name="hide_empty_sub_cat" id="hide_empty_sub_cat" value="1" <?php echo ( isset( $options['hide_empty_sub_cat'] ) && '1' == $options['hide_empty_sub_cat'] ) ? 'checked' : ''; ?> />
                            <label for="hide_empty_sub_cat"><?php _e( 'Hide empty sub-category', $this->text_domain ) ?></label>
                        </td>
                    </tr>
                    <?php
                       /*
                        <tr>
                            <th>
                                <?php _e( 'Display listing:', $this->text_domain ) ?>
                            </th>
				            <td>
					            <input type="checkbox" name="display_listing" id="display_listing" value="1" <?php echo ( isset( $options['display_listing'] ) && '1' == $options['display_listing'] ) ? 'checked' : ''; ?> />
                                <label for="display_listing"><?php _e( 'add Listings to align blocks according to height while  sub-categories are lacking', $this->text_domain ) ?></label>
				            </td>
			            </tr>
                        */
                    ?>
		        </table>
            </div>
         </div>

		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="general_settings" />
			<input type="submit" class="button-primary" name="save" value="Save Changes">
		</p>

	</form>

</div>
