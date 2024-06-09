<?php
/*
 * Plugin Name: ConvertPro
 * Plugin URI: https://wpgrids.com/
 * Description: ConvertPro allows you to ab testing.
 * Version: 1.0
 * Author: wpgrids
 * Author URI: https://profiles.wordpress.org/wpgrids/
 * Text Domain: convert-pro
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


// don't call the file directly
if (!defined('ABSPATH'))
    exit;


require_once __DIR__ . '/vendor/autoload.php';

use ConvertPro\Assets;
use ConvertPro\DataBase\Database;
use ConvertPro\Classes\ConvertProinit;

/**
 * ConvertPro class
 *
 * @class ConvertPro The class that holds the entire ConvertPro plugin
 */
final class ConvertPro
{

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.1.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the ConvertPro class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct()
    {

        $this->define_constants();

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('plugins_loaded', array($this, 'init_plugin'));
    }
    public function enqueue_frontend_scripts()
    {
        // Enqueue your JavaScript file
        wp_enqueue_script(
            'frontent-script', // A unique handle for your script
            plugin_dir_url(__FILE__) . 'assets/js/frontent-script.js', // Path to your JavaScript file
            array('jquery'), // Dependencies, if any
            '1.0', // Script version
            true // Whether to load the script in the footer (true) or head (false)
        );

        // Add the inline script with the data
        wp_localize_script('frontent-script', 'convertpro_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('convertpro_nonce')
        ));

        // Add the inline script with the data
        // wp_add_inline_script('your-plugin-script', 'var ajaxurl = "' . esc_js($data['ajaxurl']) . '";');
    }
    /**
     * Initializes the ConvertPro() class
     *
     * Checks for an existing ConvertPro() instance
     * and if it doesn't find one, creates it.
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new ConvertPro();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('CONVERTPRO_VERSION', $this->version);
        define('CONVERTPRO_FILE', __FILE__);
        define('CONVERTPRO_PATH', dirname(CONVERTPRO_FILE));
        define('CONVERTPRO_INCLUDES', CONVERTPRO_PATH . '/includes');
        define('CONVERTPRO_URL', plugins_url('', CONVERTPRO_FILE));
        define('CONVERTPRO_ASSETS', CONVERTPRO_URL . '/assets');
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        new Assets();
        $init = new ConvertProinit();
        $init->init();
        $this->includes();
        $this->init_hooks();
        // $redir = new Redirect();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {

        $installed = get_option('convertpro_installed');
        new Database();
        if (!$installed) {
            update_option('convertpro_installed', time());
        }

        update_option('convertpro_version', CONVERTPRO_VERSION);
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate()
    {
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {

        require_once CONVERTPRO_INCLUDES . '/Assets.php';

        if ($this->is_request('admin')) {
            require_once CONVERTPRO_INCLUDES . '/Admin.php';
        }

        if ($this->is_request('ajax')) {
            // require_once CONVERTPRO_INCLUDES . '/class-ajax.php';
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {

        add_action('init', array($this, 'init_classes'));

        // Localize our plugin
        add_action('init', array($this, 'localization_setup'));
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {

        if ($this->is_request('admin')) {
            $this->container['admin'] = new ConvertPro\Admin();
        }

        if ($this->is_request('ajax')) {
            // $this->container['ajax'] =  new App\Ajax();
        }

        // $this->container['api'] = new AbTest\Api();
        $this->container['assets'] = new Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('convertpro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined('DOING_AJAX');

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }
} // ConvertPro

$convertpro = ConvertPro::init();


// Function to randomly redirect users between two pages based on identifier
if (!function_exists('convertpro_random_redirect')) {

    function convertpro_random_redirect()
    {
        $url_path = wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($url_path === false || $url_path === null) {
            $url_path = '';
        }
        $trimmed_path = trim($url_path, '/');
        $current_slug = sanitize_text_field($trimmed_path);
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}convertpro");

        foreach ($results as $value) {

            if ($current_slug === $value->test_uri) {
                $page_slugs = array_map(function ($result) {
                    return $result->page_slug;
                }, convertpro_query($value->id));

                if (isset($_COOKIE['convert_pro_test_' . $value->id]) && in_array($_COOKIE['convert_pro_test_' . $value->id], $page_slugs)) {
                    // Redirect if the cookie is set and its value matches a page slug
                    wp_redirect(esc_url(home_url('/')) . sanitize_text_field($_COOKIE['convert_pro_test_' . $value->id]));
                    exit;
                } else {
                    $variation1 = convertpro_query($value->id)[0];
                    $variation2 = convertpro_query($value->id)[1];

                    if ($variation1->remaining == 0 && $variation2->remaining == 0) {
                        // Calculate remaining count based on the percentage
                        $remaining1 = str_split($variation1->percentage)[0]; // Assuming the percentage is in the format 'XX%'
                        $remaining2 = str_split($variation2->percentage)[0]; // Assuming the percentage is in the format 'XX%'

                        // Update the remaining count of variations
                        convertpro_updateVariationRemaining($wpdb, $variation1->id, $remaining1);
                        convertpro_updateVariationRemaining($wpdb, $variation2->id, $remaining2);

                        // Retrieve the updated variations
                        $variation1 = convertpro_query($value->id)[0];
                        $variation2 = convertpro_query($value->id)[1];
                    }
                    // Select a variation and set the cookie
                    $variation = convertpro_selectVariation($wpdb, $value->id);
                    if ($variation) {
                        $cookieName = 'convert_pro_test_' . $value->id;
                        convertpro_updateVariationAndRedirect($wpdb, $variation, $cookieName, $value->id);
                    }
                }
            }
        }
    }
}
// Hook the function to a WordPress action or filter
add_action('template_redirect', 'convertpro_random_redirect');

if (!function_exists('convertpro_selectVariation')) {

    function convertpro_selectVariation($wpdb, $test_id)
    {
        $variations = convertpro_query($test_id);
        $available_variations = array_filter($variations, function ($variation) {
            return $variation->remaining > 0;
        });

        if (!empty($available_variations)) {
            // Choose a random variation from the available ones
            $variation = $available_variations[array_rand($available_variations)];
            return $variation;
        }

        return null;
    }
}

if (!function_exists('convertpro_query')) {

    function convertpro_query($id)
    {
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT v.*, p.post_name AS page_slug
            FROM " . $wpdb->prefix . "convertpro_variations v
            LEFT JOIN " . $wpdb->prefix . "posts p ON v.page_id = p.ID
            WHERE v.splittest_id = %d",
                $id
            )
        );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
        return $results;
    }
}

if (!function_exists('convertpro_updateVariationAndRedirect')) {
    function convertpro_updateVariationAndRedirect($wpdb, $variation, $cookieName, $testid)
    {
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        $remaining = $variation->remaining - 1;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $wpdb->prefix . 'convertpro_variations',
            array('remaining' => $remaining),
            array('id' => $variation->id)
        );
        $cookie_value = convertpro_generateuid();

        setcookie($cookieName, $variation->page_slug, time() + (86400 * 30), '/');
        setcookie('convert_pro_test_id', $testid, time() + (86400 * 30), '/');
        setcookie('convert_pro_variation_id', $variation->id, time() + (86400 * 30), '/');
        setcookie('convert_pro_uid', $cookie_value, time() + 3600, "/");
        $_COOKIE['convert_pro_uid'] = $cookie_value;
        // store cookie value
        convertpro_store_visit_data(sanitize_text_field($_COOKIE['convert_pro_uid']), $variation->id, $testid);

        wp_redirect(get_permalink($variation->page_id));
        exit();
    }
}

if (!function_exists('convertpro_updateVariationRemaining')) {
    function convertpro_updateVariationRemaining($wpdb, $variationId, $remainingPercentage)
    {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}convertpro_variations
            SET remaining = %d
            WHERE id = %d",
                $remainingPercentage,
                $variationId
            )
        );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
    }
}
// Function to generate UUID
if (!function_exists('convertpro_generateuid')) {
    function convertpro_generateuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            wp_rand(0, 0xffff),
            wp_rand(0, 0xffff),

            // 16 bits for "time_mid"
            wp_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            wp_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            wp_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            wp_rand(0, 0xffff),
            wp_rand(0, 0xffff),
            wp_rand(0, 0xffff)
        );
    }
}

if (!function_exists('convertpro_store_visit_data')) {
    function convertpro_store_visit_data($cookie_value, $variation, $testid)
    {
        //  phpcs:ignore WordPress.DB.DirectDatabaseQuery
        global $wpdb;
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
        $query = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}convertpro_interactions
     WHERE splittest_id = %d
     AND client_id = %s",
            $testid,
            $cookie_value
        ), OBJECT);

        if (sizeof($query) == 0) {
            $table_name = $wpdb->prefix . 'convertpro_interactions';
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->insert(
                $table_name,
                array(
                    'client_id' => $cookie_value,
                    'splittest_id' => $testid,
                    'variation_id' => $variation,
                )
            );
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        }
    }
}
