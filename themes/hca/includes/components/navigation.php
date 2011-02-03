<div id="navigation"><!-- start .navigation -->
	 <?php //$options = get_site_option('dp_options'); ?>
     <?php wp_page_menu( array( 'sort_column' => 'menu_order, post_title', 'menu_class' => 'menu-header', 'include' => '', 'exclude'=> ( isset( $options['submit_page_id'] ) ) ? $options['submit_page_id'] : NULL, 'echo' => true, 'show_home' => true,'link_before' => '','link_after' => '' )); ?>
<div class="clear"></div>
</div><!-- end .navigation -->
