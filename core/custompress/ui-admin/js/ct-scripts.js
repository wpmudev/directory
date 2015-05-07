// Handle show embed code clicks
var content_types = {
    toggle_embed_code: function (key) {
        jQuery('#embed-code-' + key).toggle();
    },
    toggle_delete: function (key) {
        jQuery('#form-' + key).show();
        jQuery('#row-actions-' + key).hide();
        jQuery('input[name="action"]').val('delete');
    },
    cancel: function (key) {
        jQuery('#form-' + key).hide();
        jQuery('#row-actions-' + key).show();
    }
};


(function ($) {

    // initiate the values associated with the post type public field
    function init_public_checked_post_type() {
        if ($('.ct-post-type input[name="public"]:checked').val() === '0') {
            $('.ct-post-type input[name="show_ui"][value="0"]').prop('checked', true);
            $('.ct-post-type input[name="show_in_nav_menus"][value="0"]').prop('checked', true);
            $('.ct-post-type input[name="publicly_queryable"][value="0"]').prop('checked', true);
            $('.ct-post-type input[name="exclude_from_search"][value="1"]').prop('checked', true);
            $('.ct-post-type input[name="show_ui"]').prop('disabled', true);
            $('.ct-post-type input[name="show_in_nav_menus"]').prop('disabled', true);
            $('.ct-post-type input[name="publicly_queryable"]').prop('disabled', true);
            $('.ct-post-type input[name="exclude_from_search"]').prop('disabled', true);
        }
        else if ($('.ct-post-type input[name="public"]:checked').val() === '1') {
            $('.ct-post-type input[name="show_ui"][value="1"]').prop('checked', true);
            $('.ct-post-type input[name="show_in_nav_menus"][value="1"]').prop('checked', true);
            $('.ct-post-type input[name="publicly_queryable"][value="1"]').prop('checked', true);
            $('.ct-post-type input[name="exclude_from_search"][value="0"]').prop('checked', true);
            $('.ct-post-type input[name="show_ui"]').prop('disabled', true);
            $('.ct-post-type input[name="show_in_nav_menus"]').prop('disabled', true);
            $('.ct-post-type input[name="publicly_queryable"]').prop('disabled', true);
            $('.ct-post-type input[name="exclude_from_search"]').prop('disabled', true);
        }
        else if ($('.ct-post-type input[name="public"]:checked').val() === 'advanced') {
            $('.ct-post-type input[name="show_ui"]').prop('disabled', false);
            $('.ct-post-type input[name="show_in_nav_menus"]').prop('disabled', false);
            $('.ct-post-type input[name="publicly_queryable"]').prop('disabled', false);
            $('.ct-post-type input[name="exclude_from_search"]').prop('disabled', false);
        }
    }

    // initiate the value of the post_type rewrite field
    function init_has_archive_checked_post_type() {
        if ($('.ct-post-type input[name="has_archive"]:checked').val() === '0') {
            $('.ct-post-type input[name="has_archive_slug"]').prop('disabled', true);
        } else if ($('.ct-post-type input[name="has_archive"]:checked').val() === '1') {
            $('.ct-post-type input[name="has_archive_slug"]').prop('disabled', false);
        }
    }

    // initiate the value of the post_type rewrite field
    function init_rewrite_checked_post_type() {
        if ($('.ct-post-type input[name="rewrite"]:checked').val() === '0') {
            $('.ct-post-type input[name="rewrite_slug"]').prop('disabled', true);
            $('.ct-post-type input[name="rewrite_with_front"]').prop('disabled', true);
            $('.ct-post-type input[name="rewrite_feeds"]').prop('disabled', true);
            $('.ct-post-type input[name="rewrite_pages"]').prop('disabled', true);
        } else if ($('.ct-post-type input[name="rewrite"]:checked').val() === '1') {
            $('.ct-post-type input[name="rewrite_slug"]').prop('disabled', false);
            $('.ct-post-type input[name="rewrite_with_front"]').prop('disabled', false);
            $('.ct-post-type input[name="rewrite_feeds"]').prop('disabled', false);
            $('.ct-post-type input[name="rewrite_pages"]').prop('disabled', false);
        }
    }

    // initiate the values for the post type capability field
    function init_capability_checked_post_type() {
        if ($('.ct-post-type input[name="capability_type"]').val() != 'post') {
            $('.ct-post-type input[name="capability_type"]').prop('disabled', false);
        } else if ($('.ct-post-type input[name="capability_type_edit"]:checked').val() === '1') {
            $('.ct-post-type input[name="capability_type"]').prop('disabled', false);
        } else {
            $('.ct-post-type input[name="capability_type"]').prop('disabled', true);
        }
    }

    // initiate the values for the taxonomy public field
    function init_public_checked_taxonomy() {
        if ($('.ct-taxonomy input[name="public"]:checked').val() === '0') {
            $('.ct-taxonomy input[name="show_ui"][value="0"]').prop('checked', true);
            $('.ct-taxonomy input[name="show_in_nav_menus"][value="0"]').prop('checked', true);
            $('.ct-taxonomy input[name="show_tagcloud"][value="0"]').prop('checked', true);
            $('.ct-taxonomy input[name="show_ui"]').prop('disabled', true);
            $('.ct-taxonomy input[name="show_in_nav_menus"]').prop('disabled', true);
            $('.ct-taxonomy input[name="show_tagcloud"]').prop('disabled', true);
        }
        else if ($('.ct-taxonomy input[name="public"]:checked').val() === '1') {
            $('.ct-taxonomy input[name="show_ui"][value="1"]').prop('checked', true);
            $('.ct-taxonomy input[name="show_in_nav_menus"][value="1"]').prop('checked', true);
            $('.ct-taxonomy input[name="show_tagcloud"][value="1"]').prop('checked', true);
            $('.ct-taxonomy input[name="show_ui"]').prop('disabled', true);
            $('.ct-taxonomy input[name="show_in_nav_menus"]').prop('disabled', true);
            $('.ct-taxonomy input[name="show_tagcloud"]').prop('disabled', true);
        }
        else if ($('.ct-taxonomy input[name="public"]:checked').val() === 'advanced') {
            $('.ct-taxonomy input[name="show_ui"]').prop('disabled', false);
            $('.ct-taxonomy input[name="show_in_nav_menus"]').prop('disabled', false);
            $('.ct-taxonomy input[name="show_tagcloud"]').prop('disabled', false);
        }
    }

    // initiate the value of the taxonomy rewrite field
    function init_rewrite_checked_taxonomy() {
        if ($('.ct-taxonomy input[name="rewrite"]:checked').val() === '0') {
            $('.ct-taxonomy input[name="rewrite_slug"]').prop('disabled', true);
            $('.ct-taxonomy input[name="rewrite_with_front"]').prop('disabled', true);
            $('.ct-taxonomy input[name="rewrite_hierarchical"]').prop('disabled', true);
        } else if ($('.ct-taxonomy input[name="rewrite"]:checked').val() === '1') {
            $('.ct-taxonomy input[name="rewrite_slug"]').prop('disabled', false);
            $('.ct-taxonomy input[name="rewrite_with_front"]').prop('disabled', false);
            $('.ct-taxonomy input[name="rewrite_hierarchical"]').prop('disabled', false);
        }
    }

    // public field values initiation
    function field_type_options() {
        if ($('.ct-custom-fields select option:selected').val() === 'radio'
            || $('.ct-custom-fields select option:selected').val() === 'selectbox'
            || $('.ct-custom-fields select option:selected').val() === 'multiselectbox'
            || $('.ct-custom-fields select option:selected').val() === 'checkbox') {
            $('.ct-date-type-options').hide();
            $('.ct-text-type-options').hide();
            $('.ct-field-type-options').show();
        } else if ($('.ct-custom-fields select option:selected').val() === 'text' ||
            $('.ct-custom-fields select option:selected').val() === 'textarea') {
            $('.ct-field-type-options').hide();
            $('.ct-date-type-options').hide();
            $('.ct-text-type-options').show();
        } else if ($('.ct-custom-fields select option:selected').val() === 'datepicker') {
            $('.ct-field-type-options').hide();
            $('.ct-text-type-options').hide();
            $('.ct-date-type-options').show();
        } else if ($('.ct-custom-fields select option:selected').val() === 'upload') {
            $('.ct-field-type-options').hide();
            $('.ct-text-type-options').hide();
            $('.ct-date-type-options').hide();
        }
    }

    function role_checkboxes() {
        if ($('#roles').length) {
            $('#ajax-loader').show();
            // clear checked fields
            $('#capabilities input').prop('checked', false);
            // set data
            var data = {
                action: 'ct_get_caps',
                role: $('#roles option:selected').val(),
                post_type: $('#post_type').val()
            };
            // make the post request and process the response
            $.post(ajaxurl, data, function (response) {
                $('#ajax-loader').hide();
                $.each(response, function (index) {
                    if (index !== null) {
                        $('input[name="capabilities[' + index + ']"]').prop('checked', true);
                    }
                });
            });
        }
    }

    $(document).ready(function ($) {
        // hide embed codes
        $('.embed-code').hide();
        // hide delete forms
        $('.del-form').hide();

        $('.ct-toggle').toggle(
            function () {
                $(this).next().hide();
            },
            function () {
                $(this).next().show();
            }
        );
        $('.ct-arrow').toggle(
            function () {
                $(this).next().next().hide();
            },
            function () {
                $(this).next().next().show();
            }
        );

        // bind functions
        $(window).bind('load', init_public_checked_post_type);
        $('.ct-post-type input[name="public"]').bind('change', init_public_checked_post_type);
        $(window).bind('load', init_has_archive_checked_post_type);
        $('.ct-post-type input[name="has_archive"]').bind('change', init_has_archive_checked_post_type);
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
        $('.ct-field-add-option').click(function () {
            $('.ct-field-additional-options').append(function () {
                var count = parseInt($('input[name="track_number"]').val(), 10) + 1;
                $('input[name="track_number"]').val(count);

                input = '<p>Option ' + count + ': ' +
                '<input type="text" name="field_options[' + count + ']"> ' +
                '<input type="radio" value="' + count + '" name="field_default_option"> ' +
                'Default Value ' +
                '<a href="#" class="ct-field-delete-option">[x]</a>' +
                '</p>';
                return input;
            });

            $(".ct-field-additional-options input[type='text']:last").focus();
            return false;
        });

        // custom fields remove options
        $('.ct-field-delete-option').on('click', function () {
            $(this).parent().remove();
        });

        $('#embed-code-link').click(function () {


        });

        //Make the combo box for date formats
        $('#field_date_format').combobox([
            'mm/dd/yy',
            'mm-dd-yy',
            'mm.dd.yy',
            'dd/mm/yy',
            'dd-mm-yy',
            'dd.mm.yy',
            'yy/mm/dd',
            'yy-mm-dd',
            'yy.mm.dd',
            'M d, y',
            'MM d, yy',
            'd M, yy',
            'd MM, yy',
            'DD, d MM, yy',
            "'day' d 'of' MM 'in the year' yy"
        ]);

        $('#save_roles').click(function () {
            $('#ajax-loader').show();
            var data = $(this).closest('form').serializeArray();
            $.post(ajaxurl, data, function (data) {
                $('#ajax-loader').hide();
            });
            return false;
        });

        $('#roles').change(role_checkboxes);

        role_checkboxes();

    });  //document.ready

})(jQuery);
