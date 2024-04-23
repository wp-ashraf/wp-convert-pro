<?php

// AJAX handler to handle requests
add_action('wp_ajax_convertpro_ajax_action', 'convertpro_ajax_request');
add_action('wp_ajax_nopriv_convertpro_ajax_action', 'convertpro_ajax_request');

function convertpro_ajax_request()
{
    check_ajax_referer('convertpro_nonce', 'security');

    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
    global $wpdb;

    $testId = $_COOKIE['convert_pro_test_id'];
    $variationid = $_COOKIE['convert_pro_variation_id'];
    $clientId = isset($_COOKIE['convert_pro_uid']) ? $_COOKIE['convert_pro_uid'] : '';
    $pageslug = $_COOKIE['convert_pro_test_' . $testId];
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "convertpro" . " WHERE id =%d", $testId));
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
    foreach ($results as $result) {
        $pageId = $result->conversion_page_id;
    }

    $permalink = get_permalink($pageId);

    $purl = isset($_POST['previous_url']) ? $_POST['previous_url'] : '';

    $parsedUrl = wp_parse_url($purl);
    $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
    $path = trim($path, '/');

    // Get the last segment (page slug)
    $segments = explode('/', $path);
    $pageSlug = end($segments);


    $fpath = $_SERVER['HTTP_REFERER'];

    $message = '';
    if ($pageSlug == $pageslug) {

        if ($fpath === $permalink) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
            $query = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}convertpro_interactions
                WHERE splittest_id = %d
                AND client_id = %s",
                $testId,
                $clientId
            ), OBJECT);
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            if (sizeof($query) > 0) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
                $query = $wpdb->query($wpdb->prepare(
                    "UPDATE {$wpdb->prefix}convertpro_interactions
                    SET type = 'conversion', variation_id = %d
                    WHERE splittest_id = %d
                    AND client_id = %s",
                    $variationid,
                    $testId,
                    $clientId
                ));
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            }
        }
    }

    wp_die();
}
