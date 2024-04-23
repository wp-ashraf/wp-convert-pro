<?php

namespace ConvertPro\Classes;

class ConvertProinit
{
    public function init()
    {
        // write a code here
        if (!is_admin()) {
            return;
        }

        // phpcs:ignore
        if (!isset($_GET['page'])) {
            return;
        }
        // phpcs:ignore
        if (($_GET['page'] != "convert-pro-settings")) {
            return;
        }
        // phpcs:ignore
        if (!isset($_GET['scope'])) {
            return;
        }
        // phpcs:ignore
        if ($_GET['scope'] == "test") {

            $controller = new ConvertProStore();
            // phpcs:ignore
            if ($_GET['action'] == "store") {
                $controller->ConvertProrepoStore();
                // phpcs:ignore
            } else if ($_GET['action'] == "delete") {
                $controller->ConvertProrepoDelete();
                // phpcs:ignore
            } else if ($_GET['action'] == "update") {
                $controller->ConvertProrepoupdate();
            }
        }
    }
}
