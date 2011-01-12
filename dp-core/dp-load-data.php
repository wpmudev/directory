<?php

function dp_load_data() {
    global $wpdb;

    $options = get_site_option( 'dp_options' );

    if ( isset( $options['load_data'] ))
        return;
    
    if ( is_multisite() ) {
        
        $wpdb->insert( $wpdb->prefix . 'sitemeta', array(
            'site_id'    => 1,
            'meta_key'   => 'ct_custom_post_types_tmp',
            'meta_value' => 'a:1:{s:17:"directory_listing";a:10:{s:6:"labels";a:1:{s:4:"name";s:8:"Listings";}s:8:"supports";a:7:{s:5:"title";s:5:"title";s:6:"editor";s:6:"editor";s:6:"author";s:6:"author";s:9:"thumbnail";s:9:"thumbnail";s:7:"excerpt";s:7:"excerpt";s:13:"custom_fields";s:13:"custom_fields";s:15:"page_attributes";s:15:"page_attributes";}s:15:"capability_type";s:7:"listing";s:11:"description";s:0:"";s:13:"menu_position";i:50;s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"listing";}s:9:"query_var";b:1;s:10:"can_export";b:1;}}'
            ), array( '%d', '%s', '%s' ));

        $wpdb->insert( $wpdb->prefix . 'sitemeta', array(
            'site_id'    => 1,
            'meta_key'   => 'ct_custom_taxonomies_tmp',
            'meta_value' => 'a:16:{s:7:"dp_arts";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:2:{s:4:"name";s:4:"Arts";s:13:"singular_name";s:3:"Art";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"arts";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:7:"dp_home";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:2:{s:4:"name";s:4:"Home";s:13:"singular_name";s:4:"Home";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"home";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_regional";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:8:"Regional";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"regional";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_business";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Business ";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"business";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:17:"dp_kids_and_teens";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:14:"Kids and Teens";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:14:"kids-and-teens";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:10:"dp_science";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:7:"Science";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"science";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:12:"dp_computers";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Computers";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:9:"computers";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:7:"dp_news";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:4:"News";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"News";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_shopping";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:8:"Shopping";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"shopping";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:8:"dp_games";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:5:"Games";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:5:"games";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:13:"dp_recreation";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:10:"Recreation";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:10:"recreation";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:10:"dp_society";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:7:"Society";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"society";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:9:"dp_health";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:6:"Health";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:6:"health";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:12:"dp_reference";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Reference";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:9:"reference";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:9:"dp_sports";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:5:{s:6:"labels";a:1:{s:4:"name";s:6:"Sports";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:6:"sports";}s:9:"query_var";b:1;}}s:8:"dp_world";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:5:"World";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:5:"world";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}}'
            ), array( '%d', '%s', '%s' ));

        $wpdb->insert( $wpdb->prefix . 'sitemeta', array(
            'site_id'    => 1,
            'meta_key'   => 'ct_custom_fields_tmp',
            'meta_value' => 'a:1:{s:18:"text_4ccc5fd023950";a:8:{s:11:"field_title";s:8:"Site URL";s:10:"field_type";s:4:"text";s:16:"field_sort_order";s:7:"default";s:20:"field_default_option";N;s:17:"field_description";s:99:"URL stands for Uniform Resource Locator, which means your site address. Example: http://example.com";s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:8:"required";N;s:8:"field_id";s:18:"text_4ccc5fd023950";}}'
            ), array( '%d', '%s', '%s' ));    
    }
    else {
        
        $wpdb->insert( $wpdb->options, array(
            'blog_id'      => 0,
            'option_name'  => 'ct_custom_post_types_tmp',
            'option_value' => 'a:1:{s:17:"directory_listing";a:10:{s:6:"labels";a:1:{s:4:"name";s:8:"Listings";}s:8:"supports";a:7:{s:5:"title";s:5:"title";s:6:"editor";s:6:"editor";s:6:"author";s:6:"author";s:9:"thumbnail";s:9:"thumbnail";s:7:"excerpt";s:7:"excerpt";s:13:"custom_fields";s:13:"custom_fields";s:15:"page_attributes";s:15:"page_attributes";}s:15:"capability_type";s:7:"listing";s:11:"description";s:0:"";s:13:"menu_position";i:50;s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"listing";}s:9:"query_var";b:1;s:10:"can_export";b:1;}}'
            ), array( '%d', '%s', '%s' ));

        $wpdb->insert( $wpdb->options, array(
            'blog_id'      => 0,
            'option_name'  => 'ct_custom_taxonomies_tmp',
            'option_value' => 'a:16:{s:7:"dp_arts";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:2:{s:4:"name";s:4:"Arts";s:13:"singular_name";s:3:"Art";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"arts";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:7:"dp_home";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:2:{s:4:"name";s:4:"Home";s:13:"singular_name";s:4:"Home";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"home";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_regional";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:8:"Regional";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"regional";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_business";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Business ";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"business";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:17:"dp_kids_and_teens";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:14:"Kids and Teens";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:14:"kids-and-teens";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:10:"dp_science";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:7:"Science";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"science";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:12:"dp_computers";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Computers";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:9:"computers";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:7:"dp_news";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:4:"News";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"News";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_shopping";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:8:"Shopping";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"shopping";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:8:"dp_games";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:5:"Games";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:5:"games";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:13:"dp_recreation";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:10:"Recreation";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:10:"recreation";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:10:"dp_society";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:7:"Society";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"society";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:9:"dp_health";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:6:"Health";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:6:"health";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:12:"dp_reference";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Reference";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:9:"reference";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:9:"dp_sports";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:5:{s:6:"labels";a:1:{s:4:"name";s:6:"Sports";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:6:"sports";}s:9:"query_var";b:1;}}s:8:"dp_world";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:5:"World";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:5:"world";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}}'
            ), array( '%d', '%s', '%s' ));

        $wpdb->insert( $wpdb->options, array(
            'blog_id'      => 0,
            'option_name'  => 'ct_custom_fields_tmp',
            'option_value' => 'a:1:{s:18:"text_4ccc5fd023950";a:8:{s:11:"field_title";s:8:"Site URL";s:10:"field_type";s:4:"text";s:16:"field_sort_order";s:7:"default";s:20:"field_default_option";N;s:17:"field_description";s:99:"URL stands for Uniform Resource Locator, which means your site address. Example: http://example.com";s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:8:"required";N;s:8:"field_id";s:18:"text_4ccc5fd023950";}}'
            ), array( '%d', '%s', '%s' ));

    }

    $available_post_types = get_site_option( 'ct_custom_post_types' );
    $available_taxonomies = get_site_option( 'ct_custom_taxonomies' );
    $available_fields     = get_site_option( 'ct_custom_fields' );


    if ( !empty( $available_post_types )) {
        $imported_post_types  = get_site_option( 'ct_custom_post_types_tmp' );
        $new_post_types       = array_merge( $available_post_types, $imported_post_types );
        update_site_option( 'ct_custom_post_types', $new_post_types );
    }
    else {
        $imported_post_types  = get_site_option( 'ct_custom_post_types_tmp' );
        update_site_option( 'ct_custom_post_types', $imported_post_types );
    }

    if ( !empty( $available_taxonomies )) {
        $imported_taxonomies  = get_site_option( 'ct_custom_taxonomies_tmp' );
        $new_taxonomies       = array_merge( $available_taxonomies, $imported_taxonomies );
        update_site_option( 'ct_custom_taxonomies', $new_taxonomies );
    }
    else {
        $imported_taxonomies  = get_site_option( 'ct_custom_taxonomies_tmp' );
        update_site_option( 'ct_custom_taxonomies', $imported_taxonomies );
    }

    if ( !empty( $available_fields )) {
        $imported_fields  = get_site_option( 'ct_custom_fields_tmp' );
        $new_fields       = array_merge( $available_fields, $imported_fields );
        update_site_option( 'ct_custom_fields', $new_fields );
    }
    else {
        $imported_fields  = get_site_option( 'ct_custom_fields_tmp' );
        update_site_option( 'ct_custom_fields', $imported_fields );
    }

    delete_site_option( 'ct_custom_post_types_tmp' );
    delete_site_option( 'ct_custom_taxonomies_tmp' );
    delete_site_option( 'ct_custom_fields_tmp' );

    $load_data   = array( 'load_data' => true );
    $new_options = array_merge( $options, $load_data );
    update_site_option( 'dp_options', $new_options );
}
add_action( 'init', 'dp_load_data' );

?>
