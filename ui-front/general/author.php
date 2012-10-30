<?php
/**
* The template for displaying Author Archive pages.
* You can override this file in your active theme.
*
* @package Directory
* @subpackage UI Front
* @since Directory 2.0
*/
global $query_string;

//reset query for correct $wp_query->max_num_pages in loop-author.php (for pagination)
wp_reset_query();

if ( '' == get_option( 'permalink_structure' ) )
$dr_author_name = $_REQUEST['dr_author'];
else
$dr_author_name = get_query_var( 'dr_author_name' );
$query_string = 'author_name=' . $dr_author_name;
$user_data = get_userdatabylogin( $dr_author_name );

if ( '' == get_option( 'permalink_structure' ) )
$dr_author_url = '?dr_author=' . $user_data->user_login;
else
$dr_author_url = '/dr-author/'. $user_data->user_login .'/';

get_header(); ?>

<div id="container">
	<div id="content" role="main">

		<?php
		/* Queue the first post, that way we know who
		* the author is when we try to get their name,
		* URL, description, avatar, etc.
		*
		* We reset this later so we can run the loop
		* properly with a call to rewind_posts().
		*/
		if ( have_posts() ) the_post();
		?>
		<h1 class="page-title author"><?php printf( __( 'Directory By: %s', DR_TEXT_DOMAIN ), "<span class='vcard'><a class='url fn n' href='" . get_option( 'siteurl' ) . $dr_author_url . "' title='" . esc_attr( $user_data->display_name ) . "' rel='me'>" . $user_data->display_name . "</a></span>" ); ?></h1>

		<?php
		/* Since we called the_post() above, we need to
		* rewind the loop back to the beginning that way
		* we can run the loop properly, in full.
		*/
		rewind_posts();

		/* Run the loop for the author archive page to output the authors posts
		* If you want to overload this in a child theme then include a file
		* called loop-author.php and that will be used instead.
		*/
		if ( file_exists( get_template_directory() . "/loop-author.php" ) )
		get_template_part( 'loop', 'author' );
		else
		load_template( DR_PLUGIN_DIR . 'ui-front/general/loop-author.php' );
		?>

	</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>