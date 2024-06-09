jQuery(document).ready(function () {

    testconversiontype();

    jQuery('input[name="test-uri"]').keypress(function() {
		onTestUriChanged(jQuery(this).val());
	});

	jQuery('input[name="test-uri"]').change(function() {
		onTestUriChanged(jQuery(this).val());
	});
    // Define your query variables and values
    registerFormSubmit();
     delete_button_alert();
});

/**
 * show alert when delete btn click
 */
  function delete_button_alert() {
            jQuery(".delete-button").click(function(e) {
                e.preventDefault();
                if (confirm("Are you sure you want to delete it?")) {
                    console.log("Delete confirmed");
                    jQuery(this).closest('form').submit();
                }
            });
        }

function testconversiontype() {
    let conversionType = jQuery('select[name="test-conversion-type"]');

    if ( jQuery(conversionType).val() === "url") {
        jQuery(".test-conversion-url-wrapper").show();
        jQuery(".test-conversion-page-wrapper").hide();
    } else {
        jQuery(".test-conversion-page-wrapper").show();
        jQuery(".test-conversion-url-wrapper").hide();
    }

    jQuery(conversionType).change(function() {
		if (jQuery(this).val() === "url") {
			jQuery(".test-conversion-page-wrapper").slideUp();
			jQuery('select[name="test-conversion-page"]').val("null");
			jQuery(".test-conversion-url-wrapper").slideDown();
		} else {
			jQuery(".test-conversion-page-wrapper").slideDown();
			jQuery(".test-conversion-url-wrapper").slideUp();
		}
    });
}

function registerFormSubmit() {
    jQuery("#test-form").submit(function(e) {

        var inputs = jQuery(this).find(".data-variation .percentage input");
        var percentageCount = 0;

        // Iterate over the inputs
        jQuery.each(inputs, function(index, value) {
            // Log the integer value of each input
            percentageCount+= (getInt(jQuery(value).val()));
        });

        if (percentageCount != 100) {
            var message = 'All variations are %PERCENTAGE% counted together but it has to be 100';
            alert(message.replace("%PERCENTAGE%", percentageCount));
            return false;
		}
    });
}
function getInt(value) {
    // Convert value to an integer (dummy implementation)
    return parseInt(value, 10);
}

// In your Javascript (external .js resource or <script> tag)
// jQuery(document).ready(function() {
//     jQuery('select').select2();
// });


// In your Javascript (external .js resource or <script> tag)
