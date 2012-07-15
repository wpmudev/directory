<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
global $post;

$custom_fields = get_option( 'ct_custom_fields' );
if (empty($custom_fields)) $custom_fields = array();

$dr_options = get_option( 'dr_options' );


$val = 'div';
if ( isset( $dr_options['general']['custom_fields_structure'] ) )
$val = $dr_options['general']['custom_fields_structure'];

echo do_shortcode("[custom_fields_block wrap='{$val}']");
