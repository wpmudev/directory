<?php if (!defined('ABSPATH')) die('No direct access allowed!');

global $wp_roles;
$options = $this->get_options('general');

?>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_settings', 'tab' => 'general' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'General Settings', $this->text_domain ); ?></h1>

	<form action="#" method="post">
		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Directory Member Role', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="roles"><?php _e( 'Assign Member\'s Role', $this->text_domain ) ?></label>
						</th>
						<td>
							<select id="member_role" name="member_role" style="width:200px;">
								<?php
								$roles = array_reverse($wp_roles->role_names);
								foreach ( $roles as $role => $name ): ?>
								<option value="<?php echo $role; ?>" <?php selected(isset($options['member_role'] ) && $role == $options['member_role']); ?> ><?php echo $name; ?></option>
								<?php endforeach; ?>
							</select>
							<br /><span class="description"><?php _e('Select the role to which you want to assign a Directory member on signup.', $this->text_domain); ?></span>
							<br /><span class="description"><?php _e('If you are running multiple plugins that have signups use the same role for both.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label>Manage Member Roles</label>
						</th>
						<td>
							<label>Add Role Name</label><br />
							<input type="text" id="new_role" name="new_role" size="30"/>
							<input type="submit" class="button" id="add_role" name="add_role" value="<?php _e( 'Add a Role', $this->text_domain ); ?>" />
							<br /><span class="description"><?php _e('Add a new role. Alphanumerics only.', $this->text_domain); ?></span>
							<br /><span class="description"><?php _e('When you add a new role you must add the appropriate capabilities to make it functional.', $this->text_domain); ?></span>
							<br /><br />
							<label>Custom Roles</label><br />
							<select id="delete_role" name="delete_role"  style="width:200px;">
								<?php
								$system_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
								$role_names = $wp_roles->role_names;
								foreach ( $role_names as $role => $name ):
								if(! in_array($role, $system_roles) ): //Don't delete system roles.
								?>
								<option value="<?php echo $role; ?>"><?php echo $name; ?></option>
								<?php
								endif;
								endforeach;
								?>
							</select>
							<input type="button" class="button" onclick="jQuery(this).hide(); jQuery('#remove_role').show();" value="<?php _e( 'Remove a Role', $this->text_domain ); ?>" />
							<input type="submit" class="button-primary" id="remove_role" name="remove_role" value="<?php _e( 'Confirm Remove this Role', $this->text_domain ); ?>" style="display: none;" />

						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Directory Status Options', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="moderation"><?php _e('Available Directory Status Options', $this->text_domain ) ?></label>
						</th>
						<td>
							<label><input type="checkbox" name="moderation[publish]" value="1" <?php checked( ! empty($options['moderation']['publish']) ) ?> /> <?php _e('Published', $this->text_domain); ?></label>
							<br /><span class="description"><?php _e('Allow members to Publish Listings themselves.', $this->text_domain); ?></span>
							<br /><label><input type="checkbox" name="moderation[pending]" value="1" <?php checked( ! empty($options['moderation']['pending']) ) ?> /> <?php _e('Pending Review', $this->text_domain); ?></label>
							<br /><span class="description"><?php _e('Listing is pending review by an administrator.', $this->text_domain ); ?></span>
							<br /><label><input type="checkbox" name="moderation[draft]" value="1" <?php checked( ! empty($options['moderation']['draft']) ) ?> /> <?php _e('Draft', $this->text_domain); ?></label>
							<br /><span class="description"><?php _e('Allow members to save Drafts.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>


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
							<input type="text" name="signin_url" id="signin_url" value="<?php echo (empty($options['signin_url']) ) ? '' : $options['signin_url']; ?>" size="50" />
							<span class="description"><?php _e( 'by default to Home page', $this->text_domain ) ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="logout_url"><?php _e( 'Redirect URL (logout):', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="logout_url" id="logout_url" value="<?php echo (empty($options['logout_url']) ) ? '' : $options['logout_url']; ?>" size="50" />
							<span class="description"><?php _e( 'by default to Home page', $this->text_domain ) ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Custom Field Options', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="custom_fields_structure"><?php _e( 'Display Custom Fields as:', $this->text_domain ) ?></label>
						</th>
						<td>
							<select name="custom_fields_structure" id="custom_fields_structure" style="width:100px">
								<option value="table" <?php selected( empty( $options['custom_fields_structure']) ? false : 'table' == $options['custom_fields_structure'] ); ?> ><?php _e( 'table', $this->text_domain ) ?></option>
								<option value="ul" <?php selected( empty( $options['custom_fields_structure']) ? false : 'ul' == $options['custom_fields_structure'] ); ?> ><?php _e( 'ul', $this->text_domain ) ?></option>
								<option value="div" <?php selected( empty( $options['custom_fields_structure']) ? false : 'div' == $options['custom_fields_structure'] ); ?> ><?php _e( 'div', $this->text_domain ) ?></option>
							</select>
							<span class="description"><?php _e( 'Structure of the custom fields block.', $this->text_domain ) ?></span>
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
							<input type="text" name="count_cat" id="count_cat" value="<?php echo (empty( $options['count_cat'] ) ) ? '10' : $options['count_cat']; ?>" size="2" />
							<span class="description"><?php _e( 'a number of categories that will be displayed in the list of categories.', $this->text_domain ) ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="count_sub_cat"><?php _e( 'Count of sub-category:', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="count_sub_cat" id="count_sub_cat" value="<?php echo ( empty( $options['count_sub_cat'] ) ) ? '5' : $options['count_sub_cat']; ?>" size="2" />
							<span class="description"><?php _e( 'a number of sub-category that will be displayed for each category in the list of categories.', $this->text_domain ) ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<?php _e( 'Empty sub-category:', $this->text_domain ) ?>
						</th>
						<td>
							<input type="checkbox" name="hide_empty_sub_cat" id="hide_empty_sub_cat" value="1" <?php checked( empty( $options['hide_empty_sub_cat'] ) ? false : ! empty($options['hide_empty_sub_cat']) ); ?> />
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

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Pagination Settings', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="field_image_req"><?php _e( 'Pagination position:', $this->text_domain ); ?></label></th>
						<td>
							<input type="checkbox" id="pagination_top" name="pagination_top" value="1" <?php echo ( isset( $options['pagination_top'] ) && 1 == $options['pagination_top'] ) ? 'checked' : ''; ?> />
							<span class="description"><?php _e( 'display at top of page.', $this->text_domain ); ?></span>
							<br />
							<input type="checkbox" id="pagination_bottom" name="pagination_bottom" value="1" <?php echo ( isset( $options['pagination_bottom'] ) && 1 == $options['pagination_bottom'] ) ? 'checked' : ''; ?> />
							<span class="description"><?php _e( 'display at bottom of page.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="pagination_range"><?php _e( 'Pagination Range:', $this->text_domain ); ?></label></th>
						<td>
							<input type="text" id="pagination_range" name="pagination_range" size="4" value="<?php echo ( isset( $options['pagination_range'] ) && '' != $options['pagination_range'] ) ? $options['pagination_range'] : '4'; ?>" />
							<br />
							<span class="description"><?php _e( 'Number of page links to show at one time in pagination', $this->text_domain ); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Getting started page', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<label for="show_getting_started-yes"><?php _e( 'Show &quot;Getting started&quot; page even after all the steps are done:', $this->text_domain ) ?></label>
				<input type="radio" value="1" id="show_getting_started-yes" name="show_getting_started" <?php checked( empty( $options['show_getting_started'] ) ? false : ! empty($options['show_getting_started'] ) ); ?>>
				<label for="show_getting_started-yes"><?php _e( 'Yes', $this->text_domain ) ?></label>
				<input type="radio" value="0" id="show_getting_started-no" name="show_getting_started" <?php checked( empty( $options['show_getting_started'] ) ? true : empty($options['show_getting_started']) ); ?>>
				<label for="show_getting_started-no"><?php _e( 'No', $this->text_domain ) ?></label>
				<br />
				<span class="description"><?php _e( 'By default, "Getting started" page will be hidden once you completed all the steps. Use this option to make it control that behavior.', $this->text_domain ) ?></span>
			</div>
		</div>

		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="general" />
			<input type="submit" class="button-primary" name="save" value="Save Changes">
		</p>

	</form>

</div>
