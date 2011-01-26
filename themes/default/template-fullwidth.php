<?php
/*
Template Name: Full width
*/
?>

<?php get_header() ?>
<div id="content-fullwidth"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-latest"><!-- start #blog-latest -->
			<?php wpmu_blogpageloop(); ?>
			<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
		</div><!-- end #blog-latest -->
	</div>
</div><!-- end #content -->
<?php get_footer() ?>
