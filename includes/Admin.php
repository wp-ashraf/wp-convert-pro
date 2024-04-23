<?php

namespace ConvertPro;

use ConvertPro\Controller\Controller;

/**
 * Admin Pages Handler
 */
class Admin
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu()
    {
        global $submenu;

        $capability = 'manage_options';
        $slug       = 'convert-pro-settings';

        $hook = add_menu_page(__('ConvertPro', 'convert-pro'), __('ConvertPro', 'convert-pro'), $capability, $slug, [$this, 'ab_tester_settings'], 'dashicons-text');
        // add_submenu_page($slug, __('Settings', 'convert-pro'), __('Settings', 'convert-pro'), $capability, 'convert-pro-settings', [$this, 'ab_tester_settings']);
        // if (current_user_can($capability)) {
        //     $submenu[$slug][] = array(__('App', 'convert-pro'), $capability, 'admin.php?page=' . $slug . '#/');
        //     $submenu[$slug][] = array(__('Settings', 'convert-pro'), $capability, 'admin.php?page=' . $slug . '#/settings');
        // }

        // add_action('load-' . $hook, [$this, 'init_hooks']);
    }


    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('convertpro-admin');
        wp_enqueue_style('select2-style');
        wp_enqueue_script('convertpro-admin');
        wp_enqueue_script('test-variations-admin');
        wp_enqueue_script('ab-tester-select2');
        // wp_enqueue_script('test-variations-admin', CONVERTPRO_ASSETS . '/js/test-variation.js', ['jquery'], CONVERTPRO_VERSION, true);
    }
    public function wp_enqueue_scripts()
    {
        // write a code here
        wp_enqueue_script('convertpro-frontend');
    }
    /**
     * ab_tester_settings
     * settings page include
     * @return void
     */
    public function ab_tester_settings()
    {
        $testcon = new Controller();
        $testcon->Run();
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page()
    {
        echo '<div class="wrap"><div id="vue-admin-app">Heelo</div></div>';
    }

    // admin fronted js
}
