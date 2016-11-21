<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

		<title>
			<?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?>
		</title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" media="all" />
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<link rel="icon" href="<?php bloginfo('stylesheet_directory');?>/favicon.ico" type="images/x-icon" />
        <?php do_action('dir_theme_colors'); ?>
        <?php do_action('dir_theme_options'); ?>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/custom.css" type="text/css" media="all" />
		<?php if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' ); ?>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class() ?>>

		<?php locate_template( array( '/components/navigation.php' ), true ); ?>

		<div id="site-wrapper"><!-- start #site-wrapper -->

			<div id="header"><!-- start #header -->
                <?php locate_template( array( '/components/banner-header.php' ), true ) ?>
				<?php locate_template( array( '/components/branding-header.php' ), true ); ?>
			</div><!-- end #header -->

			<?php locate_template( array( '/components/searchcontainer.php' ), true ); ?>
		    <?php locate_template( array( '/components/actionbuttons.php' ), true ); ?>

			<div id="container"><!-- start #container -->
