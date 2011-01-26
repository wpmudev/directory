<?php

function wt_get_ID_by_page_name($page_name)
{
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id;
}

function wpmudev_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wpmudev_page_menu_args' );

function font_show(){
	$fonttype = get_option('dev_directory_header_font');
	$bodytype = get_option('dev_directory_body_font');
	if (($fonttype == "")&&($bodytype == "")){
	?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
      h1, h2, h3, h4, h5, h6, #site-logo{
font-family: 'Nobile', arial, serif;
	}
	body{
		font-family: Helvetica, Arial, Sans-serif;
	}
    </style>
	<?php
	}
	else if (($fonttype == "Cantarell, arial, serif") || ($bodytype == "Cantarell, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Cardo, arial, serif") || ($bodytype == "Cardo, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Crimson Text, arial, serif") || ($bodytype == "Crimson Text, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Droid Sans, arial, serif") || ($bodytype == "Droid Sans, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Droid Serif, arial, serif") || ($bodytype == "Droid Serif, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "IM Fell DW Pica, arial, serif") || ($bodytype == "IM Fell DW Pica, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Josefin Sans Std Light, arial, serif") || ($bodytype == "Josefin Sans Std Light, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Lobster, arial, serif") || ($bodytype == "Lobster, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Molengo, arial, serif") || ($bodytype == "Molengo, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Neuton, arial, serif") || ($bodytype == "Neuton, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Nobile, arial, serif") || ($bodytype == "Nobile, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "OFL Sorts Mill Goudy TT, arial, serif") || ($bodytype == "OFL Sorts Mill Goudy TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Reenie Beanie") || ($bodytype == "Reenie Beanie")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}	
	else if (($fonttype == "Tangerine, arial, serif") || ($bodytype == "Tangerine, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Old Standard TT, arial, serif") || ($bodytype == "Old Standard TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Volkorn, arial, serif") || ($bodytype == "Volkorn, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Yanone Kaffessatz, arial, serif") || ($bodytype == "Yanone Kaffessatz, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>;
		}
		body{
			font-family: <?php echo $bodytype; ?>;
		}
	    </style>
	<?php
	}
}
?>