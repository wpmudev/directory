<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
global $post;

$custom_fields = get_option( 'ct_custom_fields' );
if (empty($custom_fields)) $custom_fields = array();

$dr_options = get_option( 'dr_options' );


$val = 'div';
if ( isset( $dr_options['general_settings']['custom_fields_structure'] ) )
$val = $dr_options['general_settings']['custom_fields_structure'];

$field_template['table']['open'] = '<table>';
$field_template['table']['close'] = '</table>';
$field_template['table']['open_line'] = '<tr>';
$field_template['table']['close_line'] = '</tr>';
$field_template['table']['open_title'] = '<th>';
$field_template['table']['close_title'] = '</th>';
$field_template['table']['open_value'] = '<td>';
$field_template['table']['close_value'] = '</td>';

$field_template['ul']['open'] = "<ul>\n";
$field_template['ul']['close'] = "</ul>\n";
$field_template['ul']['open_line'] = '<li>';
$field_template['ul']['close_line'] = "</li>\n";
$field_template['ul']['open_title'] = '<span>';
$field_template['ul']['close_title'] = '</span>';
$field_template['ul']['open_value'] = ' ';
$field_template['ul']['close_value'] = '';

$field_template['div']['open'] = '<div>';
$field_template['div']['close'] = "</div>\n";
$field_template['div']['open_line'] = '<p>';
$field_template['div']['close_line'] = "</p>\n";
$field_template['div']['open_title'] = '<span>';
$field_template['div']['close_title'] = '</span>';
$field_template['div']['open_value'] = ' ';
$field_template['div']['close_value'] = '';

echo $field_template[$val]['open'];

foreach ( $custom_fields as $custom_field ){
	$output = false;

	foreach ( $custom_field['object_type'] as $custom_field_object_type ){
		if ( $custom_field_object_type == $post->post_type ){
			$output = true; break;
		}
	}

	if ( isset( $custom_field['field_wp_allow'] ) && 1 == $custom_field['field_wp_allow'] )
	$prefix = 'ct_';
	else
	$prefix = '_ct_';

	if ( $output ){

		echo $field_template[$val]['open_line'];
		echo $field_template[$val]['open_title'];
		echo ( $custom_field['field_title'] );
		echo $field_template[$val]['close_title'];
		echo $field_template[$val]['open_value'];
		echo ( get_post_meta( $post->ID, $prefix . $custom_field['field_id'], true ));
		echo $field_template[$val]['close_value'];
		echo $field_template[$val]['close_line'];

	}
}
echo $field_template[$val]['close'];
