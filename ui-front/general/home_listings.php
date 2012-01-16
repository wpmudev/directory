<?php get_header() ?>

<?php require_once( DR_PLUGIN_DIR . 'ui-front/general/components/actionbuttons.php' ); ?>

<div id="content"><!-- start #content -->
    <div class="padder">
        <?php the_dr_categories_home(); ?>
    </div>
</div><!-- end #content -->

<?php get_footer() ?>
