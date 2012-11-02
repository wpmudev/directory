<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_settings', 'tab' => 'shortcodes' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Listings Shortcodes', $this->text_domain ); ?></h1>

	<div class="postbox">
		<h3 class='hndle'><span><?php _e( 'Listing Shortcodes', $this->text_domain ) ?></span></h3>
		<div class="inside">
			<p>
				<?php _e( 'Shortcodes allow you to include dynamic store content in posts and pages on your site. Simply type or paste them into your post or page content where you would like them to appear. Optional attributes can be added in a format like <em>[shortcode attr1="value" attr2="value"]</em>.', $this->text_domain ) ?>
			</p>
			<p>
				<?php _e( 'Attributes: ("|" means use one OR the other. ie style="grid" or style="list" not style="grid | list")', $this->text_domain); ?>
				<br /><?php _e( 'text = <em>Text to display on a button</em>', $this->text_domain ) ?>
				<br /><?php _e( 'view = <em>Whether the button is visible when loggedin, loggedout, or always</em>', $this->text_domain ) ?>
				<br /><?php _e( 'redirect = <em>On the Logout button, what page to go to after logout</em>', $this->text_domain ) ?>
			</p>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'List of Categories:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_list_categories style="grid | list"]</strong></code>
						<br /><span class="description"><?php _e( 'Displays a list of categories.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Listings Button:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_listings_btn text="<?php _e('Listings', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_listings_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Listings', $this->text_domain);?>[/dr_listings_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Listings Page. Generates a &lt;button&gt; &lt;/button&gt; with the contents you define.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Add Listing Button:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_add_listing_btn text="<?php _e('Add Listing', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_add_listing_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Add Listing', $this->text_domain);?>[/dr_add_listing_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Add Listings Page. Generates a &lt;button&gt; &lt;/button&gt; with the contents you define.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'My Listings Button:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_my_listings_btn text="<?php _e('My Listings', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_my_listings_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('My Listings', $this->text_domain);?>[/dr_my_listings_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the My Listings Page. Generates a &lt;button&gt; &lt;/button&gt; with the contents you define.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Profile Button:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_profile_btn text="<?php _e('Go to Profile', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_profile_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Go to Profile', $this->text_domain);?>[/dr_profile_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Profile Page. Generates a &lt;button&gt; &lt;/button&gt; with the contents you define.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Signin Button:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_signin_btn text="<?php _e('Signin', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_signin_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Signin', $this->text_domain);?>[/dr_signin_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Signin Page. Generates a &lt;button&gt; &lt;/button&gt; with the contents you define.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Signup Button:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_signup_btn text="<?php _e('Signup', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_signup_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Signup', $this->text_domain);?>[/dr_signup_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Signup Page. Generates a &lt;button&gt; &lt;/button&gt; with the contents you define.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Logout Button:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_logout_btn text="<?php _e('Logout', $this->text_domain);?>"  view="loggedin | loggedout | always" redirect="http://someurl"]</strong></code> or
						<br /><code><strong>[dr_logout_btn  view="loggedin | loggedout | always" redirect="http://someurl"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Logout', $this->text_domain);?>[/dr_logout_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Logout Page. Generates a &lt;button&gt; &lt;/button&gt; with the contents you define. The "redirect" attribute is the url to go to after logging out.', $this->text_domain ) ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

</div>
