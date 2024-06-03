<?php

namespace ConvertPro\Classes;

class ConvertProStore
{

    /**
     * insert all value into
     * database
     * @return void
     */
    public function ConvertProrepoStore()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'convert-pro-nonce') || !is_user_logged_in()) {
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=security_error'));
            exit;
        }

        if (!isset($_POST['test-id'])) {
            // LOW@kberlau Log Error
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=error_update_data_missing'));
            return;
        }

        if ($_POST['test-conversion-page'] == "" || $_POST['test-conversion-page'] == null || $_POST['test-conversion-page'] == "null") {
            // LOW@kberlau Log Error
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=conversion_page_missing'));
            return;
        }

        // Proceed with data storage
        $db = new Storedatabase();
        $id = $db->CreateTest();
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (isset($_POST['test-variation']) && is_array($_POST['test-variation'])) {
            $test_variations = $_POST['test-variation'];
            foreach ($test_variations as $variation) {
                $variation['pageId'] = filter_var($variation['page-id'], FILTER_VALIDATE_INT);
                $db->CreateTestVariation($id, $variation);
            }
        }
        // Check if the data was stored successfully
        wp_redirect(admin_url('admin.php?page=convert-pro-settings&scope=test&action=edit&id=' . $id . '&message=store_success'));
    }

    /**
     * delete value from database
     * by the id
     * @return void
     */
    public function ConvertProrepoDelete()
    {
        // write a code here
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'convert-pro-nonce') || !is_user_logged_in()) {
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=security_error'));
            exit;
        }
        if (!isset($_GET['id'])) {
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=error_delete'));
        }

        $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : '';

        $db = new ConvertProrepo();
        $db->TestDelete($id);

        wp_redirect(admin_url('admin.php?page=convert-pro-settings&scope=test&action=index&message=delete_success'));
    }

    /**
     * update test repo
     * from database
     * @return void
     */
    public function ConvertProrepoupdate()
    {
        // write a code here
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'convert-pro-nonce') || !is_user_logged_in()) {
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=security_error'));
            return;
        }
        if (!isset($_POST['test-id'])) {
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=error_update_data_missing'));
            return;
        }

        if ($_POST['test-conversion-page'] == "" || $_POST['test-conversion-page'] == null || $_POST['test-conversion-page'] == "null") {
            // LOW@kberlau Log Error
            wp_redirect(admin_url('admin.php?page=convert-pro-settings&message=conversion_page_missing'));
            return;
        }

        $db = new Storedatabase();

        $db->updateTest(filter_var($_POST['test-id'], FILTER_VALIDATE_INT));
        if (isset($_POST['test-variation']) && is_array($_POST['test-variation'])) {
            $test_variations = sanitize_text_field($_POST['test-variation']);
            foreach ($test_variations as $variation) {

                $variation['postId'] = (int) $variation['page-id'];

                if ((int) $variation['id'] !== null) {
                    $db->updateTestVariation((int) $variation['id'], $variation);
                }
            }
        }

        wp_redirect(admin_url('admin.php?page=convert-pro-settings&scope=test&action=edit&id=' . sanitize_text_field( $_POST['test-id']) . '&message=save_success'));
    }
}
