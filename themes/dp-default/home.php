<?php
/* Template Name: Home Page */
?>

<?php get_header(); ?>

		<div id="container">
			<div id="content" role="main">
                
            <?php dp_list_categories('home'); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_footer(); ?>
