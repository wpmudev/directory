<?php

if ( !class_exists('Directory_Core_Data') ):

/**
 * Directory_Core_Data 
 * 
 * @uses Directory_Core
 * @package Directory
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class Directory_Core_Data extends Directory_Core {

    /**
     * Constructor.
     */
    function Directory_Core_Data() {
        add_action( 'init', array( &$this, 'load_data' ), 0 );
    }

    /**
     * Load initial Content Types data for plugin
     *
     * @return void
     */
    function load_data() {
        // Get sete options. If empty return an array 
        $options = $this->get_options();

        // Check whether post types data is loaded 
        if ( !isset( $options['data']['post_types_loaded'] ) ) {

            // Unserialize raw array data 
            $post_types_tmp = unserialize( 'a:1:{s:17:"directory_listing";a:10:{s:6:"labels";a:11:{s:4:"name";s:8:"Listings";s:13:"singular_name";s:7:"Listing";s:7:"add_new";s:7:"Add New";s:12:"add_new_item";s:15:"Add New Listing";s:9:"edit_item";s:12:"Edit Listing";s:8:"new_item";s:11:"New Listing";s:9:"view_item";s:12:"View Listing";s:12:"search_items";s:15:"Search Listings";s:9:"not_found";s:17:"No listings found";s:18:"not_found_in_trash";s:26:"No listings found in Trash";s:17:"parent_item_colon";s:14:"Parent Listing";}s:8:"supports";a:7:{s:5:"title";s:5:"title";s:6:"editor";s:6:"editor";s:6:"author";s:6:"author";s:9:"thumbnail";s:9:"thumbnail";s:7:"excerpt";s:7:"excerpt";s:13:"custom_fields";s:13:"custom_fields";s:15:"page_attributes";s:15:"page_attributes";}s:15:"capability_type";s:7:"listing";s:11:"description";s:26:"Listings custom post type.";s:13:"menu_position";i:50;s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"listing";}s:9:"query_var";b:1;s:10:"can_export";b:1;}}' );

            // Get available post types 
            $post_types = ( get_site_option( 'ct_custom_post_types' ) ) 
                ? array_merge( get_site_option( 'ct_custom_post_types' ), $post_types_tmp ) 
                : $post_types_tmp;

            // Update post types and delete tmp options 
            if ( isset( $options['general_settings']['allow_per_site_content_types'] ) ) {
                update_option( 'ct_custom_post_types', $post_types );
                update_option( 'ct_flush_rewrite_rules', true );
            } else {
                update_site_option( 'ct_custom_post_types', $post_types );
                update_site_option( 'ct_flush_rewrite_rules', true );
            }

            // Create data loaded flag so we don't load the data twice 
            $data_loaded = array( 'data' => array( 'post_types_loaded' => true ));
            $options = array_merge_recursive( $options, $data_loaded );

            update_option( $this->options_name , $options );
        }

        // Check whether taxonomies data is loaded 
        if (   !empty( $options['general_settings']['import_taxonomies'] ) 
            && !isset( $options['data']['taxonomies_loaded'] ) 
        )  {
            // Unserialize raw array data 
            $taxonomies_tmp = unserialize( 'a:16:{s:7:"dp_arts";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:2:{s:4:"name";s:4:"Arts";s:13:"singular_name";s:3:"Art";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"arts";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:7:"dp_home";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:2:{s:4:"name";s:4:"Home";s:13:"singular_name";s:4:"Home";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"home";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_regional";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:8:"Regional";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"regional";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_business";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Business ";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"business";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:17:"dp_kids_and_teens";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:14:"Kids and Teens";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:14:"kids-and-teens";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:10:"dp_science";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:7:"Science";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"science";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:12:"dp_computers";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Computers";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:9:"computers";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:7:"dp_news";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:4:"News";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:4:"News";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:11:"dp_shopping";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:8:"Shopping";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:8:"shopping";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:8:"dp_games";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:5:"Games";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:5:"games";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:13:"dp_recreation";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:10:"Recreation";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:10:"recreation";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:10:"dp_society";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:7:"Society";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:7:"society";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:9:"dp_health";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:6:"Health";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:6:"health";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:12:"dp_reference";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:9:"Reference";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:9:"reference";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}s:9:"dp_sports";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:5:{s:6:"labels";a:1:{s:4:"name";s:6:"Sports";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:6:"sports";}s:9:"query_var";b:1;}}s:8:"dp_world";a:2:{s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:4:"args";a:6:{s:6:"labels";a:1:{s:4:"name";s:5:"World";}s:6:"public";b:1;s:12:"hierarchical";b:1;s:7:"rewrite";a:1:{s:4:"slug";s:5:"world";}s:9:"query_var";b:1;s:12:"capabilities";a:1:{s:12:"assign_terms";s:13:"edit_listings";}}}}' );

            // Get available taxonomies 
            $taxonomies = ( get_site_option( 'ct_custom_taxonomies' ) ) 
                ? array_merge( get_site_option( 'ct_custom_taxonomies' ), $taxonomies_tmp ) 
                : $taxonomies_tmp;

            // Update taxonomies and delete tmp options 
            if ( isset( $options['general_settings']['allow_per_site_content_types'] ) ) {
                update_option( 'ct_custom_taxonomies', $taxonomies );
                update_option( 'ct_flush_rewrite_rules', true );
            } else {
                update_site_option( 'ct_custom_taxonomies', $taxonomies );
                update_site_option( 'ct_flush_rewrite_rules', true );
            }

            // Create data loaded flag so we don't load the data twice 
            $data_loaded = array( 'data' => array( 'taxonomies_loaded' => true ));
            $options = array_merge_recursive( $options, $data_loaded );

            update_option( $this->options_name, $options );
        }

        /* Check whether custom fields data is loaded */
        if (   !empty( $options['general_settings']['import_custom_fields'] ) 
            && !isset( $options['data']['custom_fields_loaded'] ) 
        ) {
            /* Unserialize raw array data */
            $custom_fields_tmp = unserialize( 'a:1:{s:18:"text_4ccc5fd023950";a:8:{s:11:"field_title";s:8:"Site URL";s:10:"field_type";s:4:"text";s:16:"field_sort_order";s:7:"default";s:20:"field_default_option";N;s:17:"field_description";s:99:"URL stands for Uniform Resource Locator, which means your site address. Example: http://example.com";s:11:"object_type";a:1:{i:0;s:17:"directory_listing";}s:8:"required";N;s:8:"field_id";s:18:"text_4ccc5fd023950";}}' );

            /* Get available custom fields */
            $custom_fields = ( get_site_option( 'ct_custom_fields' ) ) 
                ? array_merge( get_site_option( 'ct_custom_fields' ), $custom_fields_tmp ) 
                : $custom_fields_tmp;

            /* Update custom fields options */
            if ( isset( $options['general_settings']['allow_per_site_content_types'] ) )
                update_option( 'ct_custom_fields', $custom_fields );
            else
                update_site_option( 'ct_custom_fields', $custom_fields );

            /* Create data loaded flag so we don't load the data twice */
            $data_loaded = array( 'data' => array( 'custom_fields_loaded' => true ));
            $options = array_merge_recursive( $options, $data_loaded );

            update_option( $this->options_name, $options );
        }
    }
}
endif;

?>
