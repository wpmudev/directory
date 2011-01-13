// Handle show embed code clicks
var embed_code = {
    toggle_embed_code: function(key) {
        jQuery('#embed-code-'+key).toggle();
//        jQuery('.action-links-'+key).hide();
//        jQuery('.separators-'+key).hide();
//        jQuery('input[name="action"]').val('end');
    },
    cancel: function(key) {
        jQuery('#form-'+key).hide();
        jQuery('.action-links-'+key).show();
        jQuery('.separators-'+key).show();
    }
};

(function($) {
    
    $(document).ready(function($) {

        $('.embed-code').hide();

        $('.ct-toggle').toggle(
            function() { $(this).next().hide(); },
            function() { $(this).next().show(); }
        );
        $('.ct-arrow').toggle(
            function() { $(this).next().next().hide(); },
            function() { $(this).next().next().show(); }
        );

        // bind functions
        $(window).bind('load', init_public_checked_post_type);
        $('.ct-post-type input[name="public"]').bind('change', init_public_checked_post_type);
        $(window).bind('load', init_rewrite_checked_post_type);
        $('.ct-post-type input[name="rewrite"]').bind('change', init_rewrite_checked_post_type);
        $(window).bind('load', init_capability_checked_post_type);
        $('.ct-post-type input[name="capability_type_edit"]').bind('change', init_capability_checked_post_type);
        $(window).bind('load', init_public_checked_taxonomy);
        $('.ct-taxonomy input[name="public"]').bind('change', init_public_checked_taxonomy);
        $(window).bind('load', init_rewrite_checked_taxonomy);
        $('.ct-taxonomy input[name="rewrite"]').bind('change', init_rewrite_checked_taxonomy);
        $(window).bind('load', field_type_options);
        $('.ct-custom-fields select[name="field_type"]').bind('change', field_type_options);

        // custom fields add options
        $('.ct-field-add-option').click(function() {
            $('.ct-field-additional-options').append(function() {
                
                var count = parseInt($('input[name="track_number"]').val()) + 1;
                $('input[name="track_number"]').val(count);

                return '<p>Option ' + count + ': ' +
                            '<input type="text" name="field_options[' + count + ']"> ' +
                            '<input type="radio" value="' + count + '" name="field_default_option"> ' +
                            'Default Value ' +
                            '<a href="#" class="ct-field-delete-option">[x]</a>' +
                        '</p>'; 
            });
        });
        // custom fields remove options
        $('.ct-field-delete-option').live('click', function() {
            $(this).parent().remove();
        });

        $('#embed-code-link').click(function() {

            
        });

    });

    // initiate the values associated with the post type public field
    function init_public_checked_post_type() {
        if ( $('.ct-post-type input[name="public"]:checked').val() === '0' ) {
            $('.ct-post-type input[name="show_ui"][value="0"]').attr( 'checked', true );
            $('.ct-post-type input[name="show_in_nav_menus"][value="0"]').attr( 'checked', true );
            $('.ct-post-type input[name="publicly_queryable"][value="0"]').attr( 'checked', true );
            $('.ct-post-type input[name="exclude_from_search"][value="1"]').attr( 'checked', true );
            $('.ct-post-type input[name="show_ui"]').attr( 'disabled', true );
            $('.ct-post-type input[name="show_in_nav_menus"]').attr( 'disabled', true );
            $('.ct-post-type input[name="publicly_queryable"]').attr( 'disabled', true );
            $('.ct-post-type input[name="exclude_from_search"]').attr( 'disabled', true );
        }
        else if ( $('.ct-post-type input[name="public"]:checked').val() === '1' ) {
            $('.ct-post-type input[name="show_ui"][value="1"]').attr( 'checked', true );
            $('.ct-post-type input[name="show_in_nav_menus"][value="1"]').attr( 'checked', true );
            $('.ct-post-type input[name="publicly_queryable"][value="1"]').attr( 'checked', true );
            $('.ct-post-type input[name="exclude_from_search"][value="0"]').attr( 'checked', true );
            $('.ct-post-type input[name="show_ui"]').attr( 'disabled', true );
            $('.ct-post-type input[name="show_in_nav_menus"]').attr( 'disabled', true );
            $('.ct-post-type input[name="publicly_queryable"]').attr('disabled', true );
            $('.ct-post-type input[name="exclude_from_search"]').attr( 'disabled', true );
        }
        else if ( $('.ct-post-type input[name="public"]:checked').val() === 'advanced' ) {
            $('.ct-post-type input[name="show_ui"]').attr( 'disabled', false );
            $('.ct-post-type input[name="show_in_nav_menus"]').attr( 'disabled', false );
            $('.ct-post-type input[name="publicly_queryable"]').attr( 'disabled', false );
            $('.ct-post-type input[name="exclude_from_search"]').attr( 'disabled', false );
        }
    }

    // initiate the values for the post type rewrite field
    function init_rewrite_checked_post_type() {
        if ( $('.ct-post-type input[name="rewrite"]:checked').val() === '1'
          || $('.ct-post-type input[name="rewrite"]:checked').val() === '0' ) {
            $('.ct-post-type input[name="rewrite_slug"]').attr( 'disabled', true );
        } else if ( $('.ct-post-type input[name="rewrite"]:checked').val() === 'advanced' ) {
            $('.ct-post-type input[name="rewrite_slug"]').attr( 'disabled', false );
        }
    }

    // initiate the values for the post type capability field
    function init_capability_checked_post_type() {
        if ( $('.ct-post-type input[name="capability_type"]').val() != 'post' ) {
            $('.ct-post-type input[name="capability_type"]').attr( 'disabled', false );
        } else if ( $('.ct-post-type input[name="capability_type_edit"]:checked').val() === '1' ) {
            $('.ct-post-type input[name="capability_type"]').attr( 'disabled', false );
        } else {
            $('.ct-post-type input[name="capability_type"]').attr( 'disabled', true );
        }
    }

    // initiate the values for the taxonomy public field
    function init_public_checked_taxonomy() {
        if ( $('.ct-taxonomy input[name="public"]:checked').val() === '0' ) {
            $('.ct-taxonomy input[name="show_ui"][value="0"]').attr( 'checked', true );
            $('.ct-taxonomy input[name="show_in_nav_menus"][value="0"]').attr( 'checked', true );
            $('.ct-taxonomy input[name="show_tagcloud"][value="0"]').attr( 'checked', true );
            $('.ct-taxonomy input[name="show_ui"]').attr( 'disabled', true );
            $('.ct-taxonomy input[name="show_in_nav_menus"]').attr( 'disabled', true );
            $('.ct-taxonomy input[name="show_tagcloud"]').attr( 'disabled', true );
        }
        else if ( $('.ct-taxonomy input[name="public"]:checked').val() === '1' ) {
            $('.ct-taxonomy input[name="show_ui"][value="1"]').attr( 'checked', true );
            $('.ct-taxonomy input[name="show_in_nav_menus"][value="1"]').attr( 'checked', true );
            $('.ct-taxonomy input[name="show_tagcloud"][value="1"]').attr( 'checked', true );
            $('.ct-taxonomy input[name="show_ui"]').attr( 'disabled', true );
            $('.ct-taxonomy input[name="show_in_nav_menus"]').attr( 'disabled', true );
            $('.ct-taxonomy input[name="show_tagcloud"]').attr('disabled', true );
        }
        else if ( $('.ct-taxonomy input[name="public"]:checked').val() === 'advanced' ) {
            $('.ct-taxonomy input[name="show_ui"]').attr( 'disabled', false );
            $('.ct-taxonomy input[name="show_in_nav_menus"]').attr( 'disabled', false );
            $('.ct-taxonomy input[name="show_tagcloud"]').attr( 'disabled', false );
        }
    }

    // initiate the value of the taxonomy rewrite field
    function init_rewrite_checked_taxonomy() {
        if ( $('.ct-taxonomy input[name="rewrite"]:checked').val() === '1'
          || $('.ct-taxonomy input[name="rewrite"]:checked').val() === '0' ) {
            $('.ct-taxonomy input[name="rewrite_slug"]').attr( 'disabled', true );
        } else if ( $('.ct-taxonomy input[name="rewrite"]:checked').val() === 'advanced' ) {
            $('.ct-taxonomy input[name="rewrite_slug"]').attr( 'disabled', false );
        }
    }

    // public field values initiation
    function field_type_options() {
        if ( $('.ct-custom-fields select option:selected').val() === 'radio'
          || $('.ct-custom-fields select option:selected').val() === 'selectbox'
          || $('.ct-custom-fields select option:selected').val() === 'multiselectbox'
          || $('.ct-custom-fields select option:selected').val() === 'checkbox' ) {
            $('.ct-field-type-options').show();
        }
        else if ( $('.ct-custom-fields select option:selected').val() === 'text'
               || $('.ct-custom-fields select option:selected').val() === 'textarea' ) {
            $('.ct-field-type-options').hide();
        }
    }
})(jQuery);