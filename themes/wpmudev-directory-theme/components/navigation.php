<div id="navigation-wrapper">
	<div id="navigation"><!-- start .navigation -->
		<?php
		//get template menu
		$temple_menu = wp_nav_menu( array(
		'theme_location'   => 'top_menu',
		'menu_class'   => 'menu-header',
		'echo'         => true,
		'container'    => '',
		'fallback_cb'  => '',
		'link_before'  => '',
		'link_after'   => ''
		));

		?>
	</div><!-- end .navigation -->
</div>
