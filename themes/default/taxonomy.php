<?php
/**
 * The template for displaying Taxonomy pages.
 */
?>

<?php get_header() ?>
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="breadcrumbtrail">
            <h1 class="page-title dp-taxonomy-name">
                <?php /* <a href="<?php echo get_bloginfo('url') . $wp_rewrite->front . dp_get_taxonomy_vars('slug') . '/'; ?>"> */ ?> <?php echo dp_get_taxonomy_vars('name'); ?> <?php //</a> ?> /
            </h1>
	        <?php dp_list_categories('sub'); ?>  <div class="clear"></div>
        </div>

    <div class="clear"></div>
    <div class="dp-widgets-stra"></div>

	<?php wpmu_directoryloop(); ?>
	</div>
</div><!-- end #content -->
<?php get_footer() ?>
