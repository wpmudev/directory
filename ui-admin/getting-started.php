<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<div id="dr-plugin-setting" class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Getting started', $this->text_domain );?></h2>

	<div class="metabox-holder">

		<!-- Getting Started box -->
		<div class="postbox">
			<h3 class="hndle"><span><?php _e( 'Getting Started Guide', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<div class="note">
					<p><?php _e( 'Welcome to the <b>Directory</b> Getting Started Guide.', $this->text_domain  ); ?></p>
				</div>
				<p><?php echo '' .
					'<p>' . __( 'The Directory plugin transforms your WordPress install from a blogging platform into a powerful online directory with loads of features and built in payment gateways.', $this->text_domain ) . '</p>' .
					'<ul>' .
					'<li>' . __( 'You can make your site available free to create lists, or to charge money for it.', $this->text_domain ) . '</li>' .
					'</ul>' .
				''; ?></p>
				<ol class="dr-steps">
					<li>
						<?php if ( isset( $dr_tutorial['settings'] ) && 1 == $dr_tutorial['settings'] ) { ?>
						<span class="dr_del">
							<?php } ?>
							<?php _e( 'First up, you need to configure your settings. This is where you can set the payment type, price and other settings.', $this->text_domain ); ?>
							<?php if ( isset( $dr_tutorial['settings'] ) && 1 == $dr_tutorial['settings'] ) { ?>
						</span>
						<?php } ?>
						<a href="admin.php?page=dr-get_started&amp;dr_intent=settings" class="button"><?php _e( 'Configure your settings', $this->text_domain ); ?></a>
					</li>
					<li>
						<?php if ( isset( $dr_tutorial['category'] ) && 1 == $dr_tutorial['category'] ) { ?>
						<span class="dr_del">
							<?php } ?>
							<?php _e( 'Next, create new categories.', $this->text_domain ); ?>
							<?php if ( isset( $dr_tutorial['category'] ) && 1 == $dr_tutorial['category'] ) { ?>
						</span>
						<?php } ?>
						<a href="admin.php?page=dr-get_started&amp;dr_intent=category" class="button"><?php _e( 'Create Categories', $this->text_domain ); ?></a>
					</li>
					<li>
						<?php if ( isset( $dr_tutorial['listing'] ) && 1 == $dr_tutorial['listing'] ) { ?>
						<span class="dr_del">
							<?php } ?>
							<?php _e( 'Finally, you can create your own listings.', $this->text_domain ); ?>
							<?php if ( isset( $dr_tutorial['listing'] ) && 1 == $dr_tutorial['listing'] ) { ?>
						</span>
						<?php } ?>
						<a href="admin.php?page=dr-get_started&amp;dr_intent=listing" class="button"><?php _e( 'Create Listings', $this->text_domain ); ?></a>
					</li>
				</ol>
			</div>
		</div>

		<?php if ( !defined( 'WPMUDEV_REMOVE_BRANDING' ) || !constant( 'WPMUDEV_REMOVE_BRANDING' ) ) { ?>
		<!-- More Help box -->
		<div class="postbox">
			<h3 class="hndle"><span><?php _e( 'Need More Help?', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<ul>
					<li><a href="http://premium.wpmudev.org/project/wordpress-directory" target="_blank"><?php _e( 'Plugin project page', $this->text_domain ); ?></a></li>
					<li><a href="http://premium.wpmudev.org/project/wordpress-directory/installation/" target="_blank"><?php _e( 'Installation and instructions page', $this->text_domain ); ?></a></li>
					<!--<li><a href="#" target="_blank"><?php _e( 'Video tutorial', $this->text_domain ); ?></a></li>-->
					<li><a href="http://premium.wpmudev.org/forums/tags/directory/" target="_blank"><?php _e( 'Support forum', $this->text_domain ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php } ?>
	</div>

</div>