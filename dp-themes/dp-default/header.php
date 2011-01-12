<?php

/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
    if ( dp_categories_top_check_slug( $wp_query->query_vars['category_name'] )) {
        echo dp_categories_top_check_slug( $wp_query->query_vars['category_name'] ) . ' | ';
        bloginfo( 'name' );
    }
    else {
        global $page, $paged;

        wp_title( '|', true, 'right' );

        // Add the blog name.
        bloginfo( 'name' );

        // Add the blog description for the home/front page.
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) )
            echo " | $site_description";

        // Add a page number if necessary:
        if ( $paged >= 2 || $page >= 2 )
            echo ' | ' . sprintf( __( 'Page %s', 'directorypress' ), max( $paged, $page ) );
    }

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
</head>

<body <?php if ( dp_categories_top_check_slug( $wp_query->query_vars['categorydp_name'] )) { echo 'class="dp-top-level"'; } else { body_class(); }  ?>>
<div class="dp-header-stra"></div>
<div class="dp-header-strb"></div>
<div id="wrapper" class="hfeed">
	<div id="header">
		<div id="masthead">
			<div id="branding" role="banner">
				<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
				<<?php echo $heading_tag; ?> id="site-title">
					<span>
						<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					</span>
				</<?php echo $heading_tag; ?>>

                <?php $options = get_site_option('dp_options'); ?>
                <div id="h-banner"><?php echo $options['ads']['h_ad']; ?></div>

                 <div id="site-description"><?php bloginfo( 'description' ); ?></div>
			</div><!-- #branding -->

			<div id="access" role="navigation">
			  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
				<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'directorypress' ); ?>"><?php _e( 'Skip to content', 'directorypress' ); ?></a></div>
				<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
                 <?php $options = get_site_option('dp_options'); ?>
                 <?php wp_page_menu( array( 'sort_column' => 'menu_order, post_title', 'menu_class' => 'menu-header', 'include' => '', 'exclude'=> $options['submit_page_id'], 'echo' => true, 'show_home' => true,'link_before' => '','link_after' => '' )); ?>
			</div><!-- #access -->
            <?php if ( !is_user_logged_in()): ?>
                <div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/submit-listing/'; ?>">Submit Listing</a></div>
            <?php else: ?>
                <div id="go-to-profile"><a href="<?php echo get_bloginfo('url') . '/?redirect_admin_profile'; ?>">Go to Profile</a></div>
                <div id="add-listing"><a href="<?php echo get_bloginfo('url') . '/?redirect_admin_listings'; ?>">Add Listing</a></div>
            <?php endif; ?>
		</div><!-- #masthead -->
	</div><!-- #header -->

	<div id="main">
