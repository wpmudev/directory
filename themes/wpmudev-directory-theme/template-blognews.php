<?php
/*
Template Name: blog news
*/
?>

<?php get_header() ?>

<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-latest"><!-- start #blog-latest -->

			<?php if ( have_posts() ) : ?>
				<?php locate_template( array( '/loops/loop-blog-page.php' ), true ); ?>
				<?php locate_template( array( '/components/pagination.php' ), true ); ?>
			<?php else : ?>
				<?php locate_template( array( '/components/messages.php' ), true ); ?>
			<?php endif; ?>

		</div><!-- end #blog-latest -->
	</div>
</div><!-- end #content -->

<?php get_footer() ?>
