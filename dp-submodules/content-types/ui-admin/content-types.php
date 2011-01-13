<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<div class="wrap ct-wrap ct-content-types">
    
    <div class="icon32" id="icon-edit"><br></div>
    <h2>
        <a class="nav-tab <?php if ( isset( $_GET['ct_content_type'] ) && $_GET['ct_content_type'] == 'post_type' || !isset( $_GET['ct_content_type'] )) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=post_type"><?php _e('Post Types', 'content_types'); ?></a>
        <a class="nav-tab <?php if ( isset( $_GET['ct_content_type'] ) && $_GET['ct_content_type'] == 'taxonomy' ) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=taxonomy"><?php _e('Taxonomies', 'content_types'); ?></a>
        <a class="nav-tab <?php if ( isset( $_GET['ct_content_type'] ) && $_GET['ct_content_type'] == 'custom_field' ) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=custom_field"><?php _e('Custom Fields', 'content_types'); ?></a>
    </h2>
    
</div>