(function ($) {

    $(document).ready(function ($) {
        // bind functions
        var file_frame;
        var that;
        var parent;
        $('body').on('click', '.ct-upload-btn', function () {
            that = $(this);
            parent = that.closest('td');
            if (file_frame) {
                // Open frame
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: wp.media.view.l10n.editGalleryTitle,
                multiple: false  // Set to true to allow multiple files to be selected
            });
            // When an image is selected, run a callback.
            file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                // Do something with attachment.id and/or attachment.url here
                $(that.data('target')).first().val(attachment.id);
                var url = attachment.url;
                if (attachment.type != 'image') {
                    url = attachment.icon;
                }
                //image url
                if (parent.find('img').size() > 0) {
                    var img = parent.find('img');
                    img.attr('src', url);
                } else {
                    var img = $('<img/>').attr('src', url).css({
                        'max-width': '150px',
                        'height': 'auto',
                        'max-height': '150px',
                        'clear': 'both',
                        'margin-bottom': '5px',
                        'display': 'block'
                    });
                    that.before(img);
                }
                //append size dropdown
                if (attachment.type == 'image') {
                    if (parent.find('select').size() == 0) {
                        var sizes = $('<select/>').addClass('ct-upload-image-size').css({
                            'display': 'block'
                        });
                        sizes.append('<option value="thumbnail">Thumbnail</option>');
                        sizes.append('<option value="medium">Medium</option>');
                        sizes.append('<option value="large">Large</option>');
                        sizes.append('<option value="original">Original</option>');
                        that.before(sizes);
                    }
                } else {
                    parent.find('select').remove();
                }
            });

            file_frame.on('open', function () {
                file_frame.uploader.uploader.param("igu_uploading", "1");
            });
            // Finally, open the modal
            file_frame.open();
        });
        $('body').on('change', '.ct-upload-image-size', function () {
            var p = $(this).closest('td');
            var input = p.find('input[type="hidden"]');
            var value = input.val().split('|');
            value[1] = $(this).val();
            input.val(value.join('|'));
        })
    });

})(jQuery);

