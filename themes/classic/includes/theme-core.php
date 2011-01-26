<?php

/**
 * Setup the Colors page inside WordPress Appearance Menu
 */
if ( !class_exists('Directory_Theme_Core') ):
class Directory_Theme_Core
{
    /** @var string The current page. Used for custom hooks. */
    var $page;
    /** @var array Array with values used for determining post quality. */
    var $quality = array( 1 => 'Not so great', 2 => 'Quite good', 3 => 'Good', 4 => 'Great!', 5 => 'Excellent!' );

    /**
     * Class constructor. 
     */
    function Directory_Theme_Core() {
		$this->init();
	}

    /**
     * Init hooks.
     */
	function init() {
        add_action( 'after_setup_theme', array( &$this, 'theme_setup' ) );
        add_action( 'widgets_init', array( &$this, 'register_sidebars' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
        //add_action( 'wp_print_styles', array( &$this, 'print_styles' ));
        add_action( 'wp_head', array( &$this, 'print_scripts' ) );
        add_action( 'sr_avg_rating', array( &$this, 'render_avg_rating' ) );
        add_action( 'sr_rate_this', array( &$this, 'render_rate_this' ) );
        add_action( 'wp_ajax_sr_save_vote', array( &$this, 'handle_ajax_requests' ) );
        add_action( 'wp_ajax_nopriv_sr_save_vote', array( &$this, 'handle_ajax_requests' ) );
	}

    /**
     * Setup theme.
     *
     * @return void
     **/
    function theme_setup() {
        add_theme_support( 'post-thumbnails', array( 'directory_listing' ) );
        add_theme_support( 'automatic-feed-links', array( 'directory_listing' ) );
        // Translations can be filed in the /languages/ directory
        load_theme_textdomain( 'directory', TEMPLATEPATH . '/languages' );
        $locale = get_locale();
        $locale_file = TEMPLATEPATH . "/languages/$locale.php";
        if ( is_readable( $locale_file ) )
            require_once( $locale_file );
        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
            'primary' => __( 'Primary Navigation', 'directory' ),
        ) );
        // This theme allows users to set a custom background
        add_custom_background();
    }

    /**
     * Register sidebars by running on the widgets_init hook.
     *
     * @return void
     **/
    function register_sidebars() {
        // Area 1, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'First Footer Widget Area', 'directory' ),
            'id' => 'first-footer-widget-area',
            'description' => __( 'The first footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
        // Area 2, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'Second Footer Widget Area', 'directory' ),
            'id' => 'second-footer-widget-area',
            'description' => __( 'The second footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
        // Area 3, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'Third Footer Widget Area', 'directory' ),
            'id' => 'third-footer-widget-area',
            'description' => __( 'The third footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
        // Area 4, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'Fourth Footer Widget Area', 'directory' ),
            'id' => 'fourth-footer-widget-area',
            'description' => __( 'The fourth footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
    }

    /**
     *
     * @param <type> $val
     * @param <type> $from
     * @param <type> $to
     * @return <type> 
     */
    function in_range( $val, $from = 0, $to = 100 ) {
        return min( $to, max( $from, (int) $val ) );
    }

    /**
     *
     * @global <type> $post
     * @return <type> 
     */
    function get_rating( $post_id ) {
        $votes = get_post_meta( $post_id, '_sr_post_votes', true ) ? get_post_meta( $post_id, '_sr_post_votes', true ) : '0';
        $rating = get_post_meta( $post_id, '_sr_post_rating', true ) ? get_post_meta( $post_id, '_sr_post_rating', true ) : '0';
        $avg = ( !empty( $rating ) && !empty( $votes ) ) ? round( (int) $rating / (int) $votes ) : '0';
        return array( 'votes' => $votes, 'rating' => $rating, 'avg' => $avg );
    }

    /**
     *
     * @global <type> $post
     * @param <type> $rating 
     */
    function save_rating( $post_id, $rating ) {
        $votes = get_post_meta( $post_id, '_sr_post_votes', true );
        $current_rating = get_post_meta( $post_id, '_sr_post_rating', true );
        $votes++;
        $rating = $current_rating + $rating;
        update_post_meta( $post_id, '_sr_post_votes', $votes  );
        update_post_meta( $post_id, '_sr_post_rating', $rating  );
    }

    /**
     * Ajax callback which gets the post types associated with each page.
     *
     * @return JSON Encoded data
     **/
    function handle_ajax_requests() {
        // veriffy user input!
        $rating = $this->in_range( $_POST['rate'], 1, 5 );
        // update statistic and save to file
        $this->save_rating( $_POST['post_id'], $rating );
        $respons = $this->get_rating( $_POST['post_id'] );
        // return json object
        header( "Content-Type: application/json" );
        echo json_encode( $respons );
        exit;
    }

    /**
     * Enqueue styles.
     */
    function enqueue_styles() {
        wp_enqueue_style( 'jquery-ui-stars',
                           get_template_directory_uri() . '/js/jquery-ui-stars/jquery-ui-stars.css');
    }

    /**
     * Enqueue scripts.
     */
    function enqueue_scripts() {
        wp_register_script( 'jquery-ui-core-1.8',
                            get_template_directory_uri() . '/js/jquery-ui-stars/jquery-ui.custom.min.js' );
        wp_enqueue_script( 'jquery-ui-stars-script',
                            get_template_directory_uri() . '/js/jquery-ui-stars/jquery-ui-stars.js',
                            array( 'jquery', 'jquery-ui-core-1.8', 'jquery-form' ) );
    }

    /**
     * Print document styles.
     */
    function print_style() { ?>
        <style type="text/css">
            div.colorpicker { z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none; }
            input.use_default { margin-left: 10px; }
        </style> <?php
    }

    /**
     * Print document scripts. Handles the colorpickers.
     */
    function print_scripts() {
        global $post;
        $ajaxurl = admin_url( 'admin-ajax.php' ); ?>
        <script type="text/javascript">
        //<![CDATA[
        jQuery(function($) {
			$("#avg").children().not(":input").hide();
			$("#rat").children().not("select, #messages").hide();
            // Create stars for: Average rating
			$("#avg").stars();
			// Create stars for: Rate this
			$("#rat").stars({
				inputType: "select",
				cancelShow: false,
				captionEl: $("#caption"),
				callback: function(ui, type, value) {
					// Disable Stars while AJAX connection is active
					ui.disable();
					// Display message to the user at the begining of request
					$("#messages").text("Saving...").stop().css("opacity", 1).fadeIn(30);
					// Send request to the server using POST method
					$.post("<?php echo $ajaxurl; ?>", { action: 'sr_save_vote', post_id: <?php echo $post->ID; ?>, rate: value }, function(response) {
                        // Select stars from "Average rating" control to match the returned average rating value
                        $("#avg").stars("select", Math.round(response.avg));
                        // Update other text controls...
                        $("#all_votes").text(response.votes);
                        $("#all_avg").text(response.avg);
                        // Display confirmation message to the user
                        $("#messages").text("Rating saved (" + value + "). Thanks!").stop().css("opacity", 1).fadeIn(30);
                        // Hide confirmation message and enable stars for "Rate this" control, after 2 sec...
                        setTimeout(function(){
                            $("#messages").fadeOut(1000, function(){ui.enable()})
                        }, 2000);
					}, "json" );
				}
			});
			// Since the <option value="3"> was selected by default, we must remove selection from Stars.
			$("#rat").stars("selectID", -1);
			// Create element to use for confirmation messages
			$('<div id="messages"/>').appendTo("#rat");
		});
        //]]>
        </script> <?php
    }

    /*
     *
     */
    function render_rate_this() {
        global $post;
        $rating = $this->get_rating( $post->ID ); ?>
        <div class="clear">
            <?php /*
            <?php if (isset($post_message)): ?>
                <div class="message-box ok">Thanks, vote saved: <?php echo $post_message ?></div>
            <?php endif; ?>
            */ ?>
            <div class="sr-avg-rating"><strong>Rate this:</strong> <span id="caption"></span>
                <form id="rat" action="" method="post">
                    <select name="rate">
                    <?php foreach ( $this->quality as $scale => $text ): ?>
                        <option <?php echo $scale == 3 ? 'selected="selected"' : '' ?> value="<?php echo $scale; ?>"><?php echo $text; ?></option>
                    <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Rate it!" />
                </form>
            </div>
        </div> <?php 
    }

    /*
     * 
     */
    function render_avg_rating() {
        global $post;
        $rating = $this->get_rating( $post->ID ); ?>
        <div class="sr-rate-this"><strong>Average rating</strong>
        <span>(<span id="all_votes"><?php echo $rating['votes']; ?></span> votes; <span id="all_avg"><?php echo $rating['avg'] ?></span>)</span>
            <form id="avg" style="float: left; padding: 3px 8px 0 0;">
            <?php foreach ( $this->quality as $scale => $text ): ?>
                <input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>" disabled="disabled" <?php echo $scale == $rating['avg'] ? 'checked="checked"' : '' ?> />
            <?php endforeach; ?>
            </form>
        </div> <?php
    }
}
endif;

if ( class_exists('Directory_Theme_Core') )
	$directory_theme_core = new Directory_Theme_Core();

?>