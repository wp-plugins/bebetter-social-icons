jQuery(function($) {

    // Loads the color pickers
    $('.bb-color').wpColorPicker();

    jQuery('body').on('click', '.bb-add-new', function() {
        field = '<div class="bb-new-fields"><input type="text" name="bbsi[socials][icon][]" class="bb-icon-picker" value=""><input type="text" name="bbsi[socials][link][]" placeholder="Enter your url here" class="social_link" value=""><input type="button" value="Remove" class="bb-remove-field button"></div>';
        jQuery('.bb-social-fields').append(field);
        createIconpicker();
    });


    jQuery('body').on('click', '.bb-new-fields .button', function() {
        if (confirm('Do you really want to remove this social link?'))
            jQuery(this).parent().remove();
    });


    function createIconpicker() {
        var iconPicker = $('.bb-icon-picker').fontIconPicker({
                theme: 'fip-bootstrap'
            }),
            icomoon_json_icons = [],
            icomoon_json_search = [];
        // Get the JSON file
        $.ajax({
            url: bbsi.options_path + '/icons/selection.json',
            type: 'GET',
            dataType: 'json'
        })
            .done(function(response) {
                // Get the class prefix
                var classPrefix = response.preferences.fontPref.prefix;

                $.each(response.icons, function(i, v) {
                    // Set the source
                    icomoon_json_icons.push(classPrefix + v.properties.name);

                    // Create and set the search source
                    if (v.icon && v.icon.tags && v.icon.tags.length) {
                        icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
                    } else {
                        icomoon_json_search.push(v.properties.name);
                    }
                });

                setTimeout(function() {
                    // Set new fonts
                    iconPicker.setIcons(icomoon_json_icons, icomoon_json_search);

                }, 1000);
            })
            .fail(function() {
                // Show error message and enable
                alert('Failed to load the icons, Please check file permission.');
            });
    }
    createIconpicker();
});