<?php

global $user_ID, $user_identity, $user_login, $user_email, $userdata, $blog_id;
get_currentuserinfo();

$register = (empty($_GET['register'])) ? '' : $_GET['register'];
$reset = (empty($_GET['reset'])) ? '' : $_GET['reset'];
$redirect = (empty($_GET['redirect_to'])) ? '' : $_GET['redirect_to'];

$options = $this->get_options('payments');

if(is_multisite() ){
	$registration = get_site_option('registration');
	$can_register = ($registration == 'user' || $registration == 'all' );
	if($blog_id > 1) set_site_transient('register_blog_id_'.$_SERVER['REMOTE_ADDR'], $blog_id, 60 * 60 );
} else {
	$can_register = get_option('users_can_register');
}

?>

<div id="login-register-password">

	<?php if (! $user_ID): ?>

	<ul class="dr_tabs">
		<li class="dr_active"><a href="#tab1_login"><?php _e('Login', $this->text_domain); ?></a></li>
		<?php if($can_register): ?>
		<li><a href="#tab2_login"><?php _e('New Account', $this->text_domain); ?></a></li>
		<?php endif; ?>
		<li><a href="#tab3_login"><?php _e('Forgot?', $this->text_domain); ?></a></li>
	</ul>
	<div class="dr_tab_container">

		<div id="tab1_login" class="dr_tab_content">
			<?php if ($register == true): ?>

			<h3><?php _e('Success!', $this->text_domain); ?></h3>
			<p><?php _e('Check your email for the password and then return to log in.', $this->text_domain); ?></p>

			<?php elseif($reset == true): ?>

			<h3><?php _e('Success!', $this->text_domain); ?></h3>
			<p><?php _e('Check your email to reset your password.', $this->text_domain); ?></p>

			<?php else: ?>

			<h3><?php _e('Have an account?', $this->text_domain); ?></h3>
			<p><?php _e('You have to login to view the contents of this page.', $this->text_domain); ?></p>
			<p><?php _e('Log in or sign up! It&rsquo;s fast &amp; <em>free!</em>', $this->text_domain); ?></p>

			<?php endif; ?>

			<form method="post" action="<?php bloginfo('url') ?>/wp-login.php" class="wp-user-form">
				<div class="username">
					<label for="user_login"><?php _e('Username', $this->text_domain); ?>: </label>
					<input type="text" name="log" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" id="user_login" tabindex="11" />
				</div>
				<div class="password">
					<label for="user_pass"><?php _e('Password', $this->text_domain); ?>: </label>
					<input type="password" name="pwd" value="" size="20" id="user_pass" tabindex="12" />
				</div>
				<div class="login_fields">
					<div class="rememberme">
						<label for="rememberme">
							<input type="checkbox" name="rememberme" value="forever" checked="checked" id="rememberme" tabindex="13" /> <?php _e('Remember me', $this->text_domain); ?>
						</label>
					</div>
					<?php do_action('login_form'); ?>
					<input type="submit" name="user-submit" value="<?php _e('Login', $this->text_domain); ?>" tabindex="14" class="user-submit" />
					<input type="hidden" name="redirect_to" value="<?php echo $redirect; ?>" />
					<input type="hidden" name="user-cookie" value="1" />
				</div>
			</form>
		</div>

		<div id="tab2_login" class="dr_tab_content" style="display:none;">
			<h3><?php _e('Register for this site!', $this->text_domain); ?></h3>
			<p><?php _e('Sign up now for the good stuff.', $this->text_domain); ?></p>

			<?php if(is_multisite()): ?>
			<form method="post" id="register_frm" action="<?php echo network_home_url('wp-signup.php') ?>" class="wp-user-form">
				<input type="hidden" name="stage" value="validate-user-signup" />
				<?php do_action( 'signup_hidden_fields' ); ?>
				<input type="hidden" name="signup_for" value="user" />
				<div class="username">
					<label for="user_name"><?php _e('Username', $this->text_domain); ?>: </label>
					<input type="text" name="user_name" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" id="user_name" tabindex="101" />
				</div>

				<?php else:	?>

				<form method="post" id="register_frm" action="<?php echo home_url('wp-login.php?action=register', 'login_post') ?>" class="wp-user-form">
					<div class="username">
						<label for="user_login"><?php _e('Username', $this->text_domain); ?>: </label>
						<input  class="required" type="text" name="user_login" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" id="user_login" tabindex="101" />
					</div>

					<?php endif; ?>

					<div class="password">
						<label for="user_email"><?php _e('Your Email', $this->text_domain); ?>: </label>
						<input type="text" name="user_email" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" id="user_email" tabindex="102" />
					</div>

					<?php if(! empty($options['tos_txt']) ): ?>
					<div>
						<br />
						<label><strong><?php _e('Terms of Service', $this->text_domain)?></strong></label>
						<div class="terms"><?php echo nl2br( $options['tos_txt'] ); ?></div>
						<label><input type="checkbox" id="tos_agree" value="1" class="required"  tabindex="103" /> <?php _e('I agree with the Terms of Service', $this->text_domain); ?></label>
					</div>
					<?php endif; ?>

					<div class="login_fields">
						<?php do_action('register_form'); ?>
						<input type="submit" name="user-submit" value="<?php _e('Sign up!', $this->text_domain); ?>" class="user-submit" tabindex="104" />
						<?php if($register == true): ?>
						<p><?php _e('Check your email for the password!', $this->text_domain); ?></p>
						````````````<?php endif; ?>
						<input type="hidden" name="redirect_to" value="<?php echo $redirect; ?>?register=true" />
						<input type="hidden" name="user-cookie" value="1" />
					</div>
				</form>
			</div>

			<div id="tab3_login" class="dr_tab_content" style="display:none;">
				<h3><?php _e('Lose something?', $this->text_domain); ?></h3>
				<p><?php _e('Enter your username or email to reset your password.', $this->text_domain); ?></p>
				<form method="post" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" class="wp-user-form">
					<div class="username">
						<label for="user_login" class="hide"><?php _e('Username or Email', $this->text_domain); ?>: </label>
						<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1001" />
					</div>
					<div class="login_fields">
						<?php do_action('login_form', 'resetpass'); ?>
						<input type="submit" name="user-submit" value="<?php _e('Reset my password', $this->text_domain); ?>" class="user-submit" tabindex="1002" />
						<?php if($reset == true): ?>
						<p><?php _e('A message will be sent to your email address.', $this->text_domain); ?></p>
						<?php endif; ?>
						<input type="hidden" name="redirect_to" value="<?php echo $redirect; ?>?reset=true" />
						<input type="hidden" name="user-cookie" value="1" />
					</div>
				</form>
			</div>
		</div>

		<?php else: // is logged in ?>

		<div class="sidebox">
			<h3><?php echo sprintf(__('Welcome, %s', $this->text_domain), $user_identity); ?></h3>
			<div class="usericon">
				<?php echo get_avatar($userdata->ID, 60); ?>
			</div>
			<div class="userinfo">
				<p><?php echo sprintf(__('You&rsquo;re logged in as <strong>%s</strong>',$this->text_domain),$user_identity); ?></p>
				<p>
					<a href="<?php echo wp_logout_url('index.php'); ?>"><?php _e('Log out', $this->text_domain); ?></a> |
					<?php if (current_user_can('manage_options')) {
					echo '<a href="' . admin_url() . '">' . __('Admin') . '</a>'; } else {
					echo '<a href="' . admin_url() . 'profile.php">' . __('Profile') . '</a>'; } ?>
				</p>
			</div>
		</div>
		<?php endif; ?>
	</div>

	<script type="text/javascript">
		(function($) {

			$(document).ready(function() {

				$(".dr_tab_content").hide();
				$("ul.dr_tabs li:first").addClass("dr_active").show();
				$(".dr_tab_content:first").show();
				$("ul.dr_tabs li").click(function() {
					$("ul.dr_tabs li").removeClass("dr_active");
					$(this).addClass("dr_active");
					$(".dr_tab_content").hide();
					var activeTab = $(this).find("a").attr("href");
					$(activeTab).show();
					return false;
				});
			});
			<?php if(! empty($options['tos_txt']) ): ?>
			$("#register_frm").submit(function(){
				if( ! $('#tos_agree').prop('checked') ) {alert("<?php echo __('Please accept the Terms of Service', $this->text_domain); ?>"); return false;}
			});
			<?php endif; ?>

		})(jQuery);
	</script>
