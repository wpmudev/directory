<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
 		<?php //include (TEMPLATEPATH . '/library/options/options.php'); ?>
				<?php $themeseo = get_option('dev_directory_seo_theme');
				if ($themeseo == "yes"){
					$seotitle = get_option('dev_directory_seo_title');
					$seodescription = get_option('dev_directory_seo_description');
					$seokeywords = get_option('dev_directory_seo_keywords');
				?>
				<title>
					<?php if ($seotitle == ""){?>
					<?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?>
					<?php } else {?>
					<?php echo stripslashes($seotitle); ?>
					<?php } ?>
				</title>
				<meta name="description" content="<?php echo stripslashes($seodescription); ?>" />
				<meta name="keywords" content="<?php echo stripslashes($seokeywords); ?>" />
				<?php } else {?>
				<title>
				<?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?>
				</title>
				<?php } ?>
		<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/_inc/css/reset.css" type="text/css" media="all" />

				<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
			<?php
			$get_current_scheme = get_option('dev_directory_custom_style');
			?>
			<?php 
			if (($get_current_scheme == "") || ($get_current_scheme == 'basic.css') || ($get_current_scheme == 'default.css')) { ?>
			<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/library/styles/basic.css" type="text/css" media="all" />
			<?php
			}	else { ?>
			<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/library/styles/<?php echo $get_current_scheme; ?>" type="text/css" media="all" />
			<?php }?>
			
			<?php print "<style type='text/css' media='screen'>"; ?>
			<?php include (TEMPLATEPATH . '/library/options/theme-options.php'); ?>
			<?php print "</style>"; ?>
			
		<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
		<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="icon" href="<?php bloginfo('stylesheet_directory');?>/favicon.ico" type="images/x-icon" />
		<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class() ?>>
					<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
		<div id="site-wrapper"><!-- start #site-wrapper -->
			<div id="header"><!-- start #header -->
				<?php locate_template( array( '/library/components/branding-header.php' ), true ); ?>
			</div><!-- end #header -->
				<?php locate_template( array( '/library/components/searchcontainer.php' ), true ); ?>
				
							<?php locate_template( array( '/library/components/actionbuttons.php' ), true ); ?>
					
			<div id="container"><!-- start #container -->