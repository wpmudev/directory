<div id="navigation-wrapper">
	<div id="navigation"><!-- start .navigation -->
	 <?php

     //get page menu
     $page_menu = wp_page_menu( array(
         'sort_column' => 'menu_order, post_title',
         'menu_class'  => 'menu-header',
         'include'     => '',
         'exclude'     => ( isset( $options['submit_page_id'] ) ) ? $options['submit_page_id'] : null,
         'echo'        => false,
         'show_home'   => true,
         'link_before' => '',
         'link_after'  => ''
     ));

     //get template menu
     $temple_menu = wp_nav_menu( array(
		 'menu_class'   => 'menu-header',
		 'echo'         => false,
         'container'    => '',
         'fallback_cb'  => '',
		 'link_before'  => '',
		 'link_after'   => ''
	 ));

     //display menu
    if ( $temple_menu )
        echo (  str_replace( "</div>", "", $page_menu ) . $temple_menu . "</div>" );
    else
        echo ( $page_menu );

     ?>
	</div><!-- end .navigation -->
</div>
