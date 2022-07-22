<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/includes
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

class Plugnmeet {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Plugnmeet_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The unique prefix of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_prefix The string used to uniquely prefix technical functions of this plugin.
     */
    protected $plugin_prefix;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        if (defined('PLUGNMEET_VERSION')) {

            $this->version = PLUGNMEET_VERSION;

        } else {

            $this->version = '1.0.0';

        }

        $this->plugin_name = 'plugnmeet';
        $this->plugin_prefix = 'plugnmeet_';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->version_update_checker();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Plugnmeet_Loader. Orchestrates the hooks of the plugin.
     * - Plugnmeet_i18n. Defines internationalization functionality.
     * - Plugnmeet_Admin. Defines all hooks for the admin area.
     * - Plugnmeet_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugnmeet-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugnmeet-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-plugnmeet-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-plugnmeet-public.php';

        // ajax helper call to use in share
        require_once plugin_dir_path(dirname(__FILE__)) . 'helpers/ajaxHelper.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-plugnmeet-on-after-update.php';

        $this->loader = new Plugnmeet_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugnmeet_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Plugnmeet_I18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Plugnmeet_Admin($this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        $this->loader->add_action('admin_menu', $plugin_admin, 'addMenuPages');

        $this->loader->add_action('wp_ajax_plugnmeet_update_client', $plugin_admin, 'update_client');

        $this->loader->add_action('wp_ajax_plugnmeet_save_room_data', $plugin_admin, 'save_room_data');
        $this->loader->add_action('wp_ajax_plugnmeet_delete_room', $plugin_admin, 'delete_room');

        $ajaxHelper = new PlugNmeetAjaxHelper();

        $this->loader->add_action('wp_ajax_plugnmeet_get_recordings', $ajaxHelper, 'get_recordings');
        $this->loader->add_action('wp_ajax_plugnmeet_download_recording', $ajaxHelper, 'download_recording');
        $this->loader->add_action('wp_ajax_plugnmeet_delete_recording', $ajaxHelper, 'delete_recording');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Plugnmeet_Public($this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('init', $plugin_public, 'custom_add_rewrite_rule');
        $this->loader->add_action('query_vars', $plugin_public, 'setQueryVar');
        $this->loader->add_action('template_include', $plugin_public, 'on_display_plugnmeet_conference');

        // we need session to store user's info before login.
        $this->loader->add_action('init', $plugin_public, 'start_session');

        // Shortcode name must be the same as in shortcode_atts() third parameter.
        $this->loader->add_shortcode($this->get_plugin_prefix() . 'room_view', $plugin_public, 'plugnmeet_shortcode_room_view');

        $ajaxHelper = new PlugNmeetAjaxHelper();
        $this->loader->add_action('wp_ajax_nopriv_plugnmeet_login_to_room', $ajaxHelper, 'login_to_room');
        $this->loader->add_action('wp_ajax_nopriv_plugnmeet_get_recordings', $ajaxHelper, 'get_recordings');
        $this->loader->add_action('wp_ajax_nopriv_plugnmeet_download_recording', $ajaxHelper, 'download_recording');

        $this->loader->add_action('wp_ajax_plugnmeet_login_to_room', $ajaxHelper, 'login_to_room');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The unique prefix of the plugin used to uniquely prefix technical functions.
     *
     * @return    string    The prefix of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_prefix() {
        return $this->plugin_prefix;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Plugnmeet_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version() {
        return $this->version;
    }

    private function version_update_checker() {
        new PlugNmeetOnAfterUpdate();
    }

}
