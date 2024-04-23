jQuery(document).ready(function() {
    var adminPageUrl = window.location.href;
    var previousUrl = document.referrer;

    jQuery.ajax({
        url: convertpro_object.ajaxurl, // Use passed ajaxurl
        type: 'POST',
        data: {
            action: 'convertpro_ajax_action',
            previous_url: previousUrl,
            security: convertpro_object.nonce // Use passed nonce
        },
        success: function(response) {
            // Handle the response
            console.log('AJAX request successful:', response);
        },
        error: function(xhr, status, error) {
            console.error('AJAX request failed:', error);
        }
    });
});
