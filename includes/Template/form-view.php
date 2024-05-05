<?php

if (!defined('ABSPATH')) exit;

if ($scope == "edit") {
    $formUrl = admin_url('admin.php?page=convert-pro-settings&scope=test&action=update&id=' . $test->id);
} else {
    $formUrl = admin_url('admin.php?page=convert-pro-settings&scope=test&action=store');
}
?>
<div class="meassage">
    <?php
    // phpcs:ignore
    if (isset($_GET['message']) && ($_GET['message'] == "save_success" || $_GET['message'] == "store_success")) {
        // Verify nonce

        // phpcs:ignore
        if ($_GET['message'] == "save_success") {
    ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Update successfully saved', 'convert-pro'); ?></p>
            </div>
        <?php
        }
        // phpcs:ignore
        elseif ($_GET['message'] == "store_success") {
        ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Test successfully created', 'convert-pro'); ?></p>
            </div>
    <?php
        }
    }
    ?>

</div>

<script>
    var homeUrl = '<?php echo esc_url(home_url('/')); ?>';

    function onTestUriChanged(value) {
        var url = homeUrl + value;
        jQuery("#test-page-url").attr("href", url);
    }

    var ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';

    jQuery(document).ready(function() {
        jQuery('select[name="test-conversion-page"]').change(function() {
            // Get the selected option value (which is the page ID)
            var pageid = jQuery(this).val();
            jQuery.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: {
                    action: 'get_permalink_by_id',
                    page_id: pageid
                },
                success: function(permalink) {
                    // Handle the retrieved permalink
                    console.log('Permalink:', permalink);

                    // Redirect to the permalink with the query string parameter
                    // window.location.href = permalinkWithQuery;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });


        // Get the current URL

    });



    // coversion page id find
</script>

<div class="create-content-wrapper">
    <div class="test-top-area">
        <div class="back-test">
            <a href="<?php echo esc_url(admin_url('admin.php?page=convert-pro-settings')); ?>"><svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.5 14.75L2.25 9.5M2.25 9.5L7.5 4.25M2.25 9.5L15.75 9.5" stroke="#080E13" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg><?php echo esc_html__('Back to All Tests', 'convert-pro') ?></a>
        </div>
        <div class="create-update-test">
            <h4><?php echo esc_html($text); ?></h4>
        </div>
    </div>

    <form method="post" action="<?php echo esc_url($formUrl); ?>" id="test-form">
        <input name="nonce" type="hidden" value="<?php echo esc_attr(wp_create_nonce('convert-pro-nonce')); ?>" />
        <div class="test-top-wrap">
            <div class="test-name-wrap">
                <label for="test-name"><?php esc_html_e('Test name', 'convert-pro'); ?></label>
                <p><?php echo esc_html__('This is for your reference and will only visible to you', 'convert-pro') ?></p>
                <input id="test-name" class="text-name" name="test-name" type="text" value="<?php echo (isset($test->name) ? esc_attr($test->name) : ""); ?>" placeholder="<?php esc_attr_e('Add a name', 'convert-pro'); ?>" required />
            </div>
            <!-- /.select page showing this content start -->
            <div class="convertpro-uri-wrapper">
                <div class="convertpro-headline" style="margin-top: 14px;">
                    <label><?php esc_html_e('Test URL', 'convert-pro'); ?></label>
                    <p><?php echo esc_html__('Visitors will visit this page for this performing this test', 'convert-pro') ?></p>
                </div>
                <div class="url-identfier">
                    <span><?php echo esc_url(home_url('/')); ?></span>
                    <input name="test-uri" type="text" placeholder="identifier" pattern="^([A-Za-z0-9\-_\/]*)$" title="Only input letters, numbers, dashes and underscores" value="<?php echo isset($test->test_uri) ? esc_attr($test->test_uri) : ''; ?>" />

                </div>
                <?php if (isset($test->test_uri)) : ?>
                    <div class="convertpro-pageview">
                        <div class="convertpro-pageview">
                            <a class="pageview-btn" id="test-page-url" target="_blank" href="<?php echo esc_url(home_url() . '/' . $test->test_uri . '/'); ?>"><?php esc_html_e('View Page', 'convertpro'); ?></a>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="test-variations">
            <div class="headline">
                <div class="name"><?php esc_html_e('Variation Name', 'convert-pro'); ?>:</div>
                <div class="post"><?php esc_html_e('Page', 'convert-pro'); ?>:</div>
                <div class="percentage"><?php esc_html_e('Percentage', 'convert-pro'); ?>:</div>
            </div>
            <div id="variations-container">
                <?php
                $i = 0;
                foreach ($test->variations as $variation) {

                ?>
                    <div class="data-variation" data-variation-id="<?php echo esc_attr($variation->id); ?>">
                        <input type="hidden" name="test-variation[<?php echo esc_attr($i); ?>][id]" value="<?php echo esc_attr($variation->id); ?>">
                        <div class="name">
                            <input id="test-name" name="test-variation[<?php echo esc_attr($i); ?>][name]" type="text" value="<?php echo esc_attr($variation->name); ?>" />
                        </div>
                        <div class="post">
                            <select name="test-variation[<?php echo esc_attr($i); ?>][page-id]">
                                <option value="null" disabled selected><?php echo esc_attr('Select Page'); ?></option>
                                <?php foreach ($pages as $page) { ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php if (isset($variation->page_id) && $variation->page_id == $page->ID) {
                                                                                            echo 'selected="selected"';
                                                                                        } ?>><?php echo esc_html($page->post_title); ?></option>

                                <?php } ?>
                            </select>
                        </div>

                        <div class="percentage">
                            <input id="test-name" name="test-variation[<?php echo esc_attr($i); ?>][percentage]" type="number" value="<?php echo esc_attr($variation->percentage); ?>" placeholder="<?php esc_html_e('Percentage', 'convert-pro'); ?>" required />
                        </div>

                    </div>
                <?php $i++;
                } ?>
            </div>
            <div class="variation-btn">
                <a class="vari-btn" href="#">
                    <span><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 4.5V9M9 9V13.5M9 9H13.5M9 9L4.5 9" stroke="#F9FAFB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <?php echo esc_html__('Upgrade to Add More Variation', 'convert-pro') ?>
                </a>
            </div>
        </div>



        <div class="test-conversion-page-wrapper">
            <div for="test-conversion-page" class="test-conversion-page">
                <h4><?php esc_html_e('Conversion Page', 'convert-pro'); ?></h4>
                <p><?php echo esc_html__('When customer visits this page, we track that as a conversion', 'convert-pro'); ?></p>
            </div>
            <select name="test-conversion-page" id="test-conversion-page" style="width: 100%;">
                <option value="null" disabled selected><?php echo esc_attr('Select Conversion Page'); ?></option>
                <?php
                foreach ($pages as $page) { ?>
                    <option value="<?php echo esc_attr($page->ID); ?>" <?php if (isset($test->conversion_page_id) && $test->conversion_page_id == $page->ID) {
                                                                            echo ('selected="selected"');
                                                                        } ?>><?php echo esc_html($page->post_title); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="submit-btn">
            <input type="hidden" name="variation-count" value="<?php echo esc_attr($i); ?>" />
            <input type="hidden" name="test-id" value="<?php echo (isset($test->id) ? esc_attr($test->id) : ''); ?>" />
            <input class="test-button-save" type="submit" value="<?php esc_html_e('Save Test', 'convert-pro'); ?>" />

        </div>
    </form>

</div>