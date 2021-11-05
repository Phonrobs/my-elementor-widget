<?php

/**
 * Plugin Name: My Elementor Widget
 * Plugin URI: https://example.com
 * Description: A custom elementor widget
 * Version: 1.0.0
 * Author: John Doe
 * Author URI: https://johndoe.me
 * Text Domain: my-elementor-widget
 */

if (!defined('ABSPATH')) exit();

/**
 * Elementor Extension Main Class
 */
final class MY_Elementor_Widget
{
    // Plugin version
    const VERSION = '1.0.0';

    // Minimum Elemetor Version
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    // Minimum PHP Version
    const MINIMUM_PHP_VERSION = '7.0';

    // Instance
    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->define_constants();
        add_action('wp_enqueue_scripts', [$this, 'scripts_styles']);
        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function define_constants()
    {
        define('MYEW_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));
        define('MYEW_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
    }

    public function scripts_styles()
    {
        wp_register_style('myew-style', MYEW_PLUGIN_URL . 'assets/dist/css/public.min.css', [], rand(), 'all');
        wp_register_script('myew-script', MYEW_PLUGIN_URL . 'assets/dist/js/public.min.js', ['jquery'], rand(), true);

        wp_enqueue_style('myew-style');
        wp_enqueue_script('myew-script');
    }

    public function i18n()
    {
        load_plugin_textdomain('my-elementor-widget', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function init()
    {
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices',  [$this, 'admin_notice_missing_main_plugin']);
        }

        add_action('elementor/init', [$this, 'init_category']);
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
    }

    public function init_widgets()
    {
        require_once MYEW_PLUGIN_PATH . '/widgets/example.php';
    }

    public function init_category()
    {
        Elementor\Plugin::instance()->elements_manager->add_category(
            'myew-for-elementor',
            [
                'title' => 'My Elementor Widgets'
            ],
            1
        );
    }

    public function admin_notice_missing_main_plugin()
    {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be intalled and activated', 'my-elementor-widget'),
            '<strong>' . esc_html__('My Elementor Widget', 'my-elementor-widget') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'my-elementor-widget') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

MY_Elementor_Widget::instance();
