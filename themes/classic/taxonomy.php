<?php
/**
 * The template for displaying Taxonomy pages.
 */

get_header(); ?>

    <div id="container">
        <div id="content" role="main">
            <?php
                /* Queue the first post, that way we know
                 * what date we're dealing with (if that is the case).
                 *
                 * We reset this later so we can run the loop
                 * properly with a call to rewind_posts().
                 */
                if ( have_posts() ) the_post();
            ?>

            <h1 class="page-title dp-taxonomy-name">
                <?php /* <a href="<?php echo get_bloginfo('url') . $wp_rewrite->front . dp_get_taxonomy_vars('slug') . '/'; ?>"> */ ?> <?php echo dp_get_taxonomy_vars('name'); ?> <?php //</a> ?> /
            </h1>

            <?php dp_list_categories('sub'); ?>

            <div class="clear"></div>
            <div class="dp-widgets-stra"></div>

            <?php
                /* Since we called the_post() above, we need to
                 * rewind the loop back to the beginning that way
                 * we can run the loop properly, in full.
                 */
                rewind_posts();

                /* Run the loop for the archives page to output the posts.
                 * If you want to overload this in a child theme then include a file
                 * called loop-archives.php and that will be used instead.
                 */
                 get_template_part( 'loop', 'directory' );
            ?>

        </div><!-- #content -->
    </div><!-- #container -->

<?php get_footer(); ?>
