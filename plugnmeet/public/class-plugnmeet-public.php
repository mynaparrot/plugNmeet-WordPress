<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/public
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */
if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

class Plugnmeet_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The unique prefix of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_prefix The string used to uniquely prefix technical functions of this plugin.
     */
    private $plugin_prefix;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $plugin_prefix The unique prefix of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $plugin_prefix, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->plugin_prefix = $plugin_prefix;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/plugnmeet-public.css');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/plugnmeet-public.js', array('jquery'), false, true);

        $nonce = wp_create_nonce('plugnmeet_frontend');
        $script = array('nonce' => $nonce, 'ajaxurl' => admin_url('admin-ajax.php'));
        wp_localize_script($this->plugin_name, 'plugnmeet_frontend', $script);
    }

    public function start_session()
    {
        if (!session_id()) {
            session_start();
        }
    }

    public function setQueryVar($vars)
    {
        $vars[] = 'Plug-N-Meet-Conference';

        return $vars;
    }

    public function custom_add_rewrite_rule()
    {
        add_rewrite_rule('^Plug-N-Meet-conference$', 'index.php?Plug-N-Meet-Conference=1', 'top');
    }

    public function on_display_plugnmeet_conference($template)
    {
        if (!get_query_var('Plug-N-Meet-Conference')) {
            return $template;
        }

        require plugin_dir_path(dirname(__FILE__)) . 'public/partials/plugnmeet-public-display-client.php';

        exit();
    }

    /**
     * Example of Shortcode processing function.
     *
     * Shortcode can take attributes like [plugnmeet_room_view id='123']
     * Shortcodes can be enclosing content [plugnmeet_room_view id='123']custom content[/plugnmeet_room_view].
     *
     * @see https://developer.wordpress.org/plugins/shortcodes/enclosing-shortcodes/
     *
     * @since    1.0.0
     * @param array $atts ShortCode Attributes.
     * @param mixed $content ShortCode enclosed content.
     * @param string $tag The Shortcode tag.
     */
    public function plugnmeet_shortcode_room_view($atts, $content = null, $tag)
    {

        /**
         * Combine user attributes with known attributes.
         *
         * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
         *
         * Pass third paramter $shortcode to enable ShortCode Attribute Filtering.
         * @see https://developer.wordpress.org/reference/hooks/shortcode_atts_shortcode/
         */

        $atts = shortcode_atts(
            array(
                'id' => 1,
            ),
            $atts,
            $this->plugin_prefix . 'room_view'
        );

        /**
         * Build our ShortCode output.
         * Remember to sanitize all user input.
         * In this case, we expect a integer value to be passed to the ShortCode attribute.
         *
         * @see https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
         */
        $id = intval($atts['id']);

        /**
         * If the shortcode is enclosing, we may want to do something with $content
         */
        if (!is_null($content) && !empty($content)) {
            $id = do_shortcode($content);// We can parse shortcodes inside $content.
            $id = intval($atts['id']) . ' ' . sanitize_text_field($id);// Remember to sanitize your user input.
        }

        if (!$id) {
            return null;
        }

        // ShortCodes are filters and should always return, never echo.
        return $this->formatRoomViewForShortCode($id);

    }

    private function formatRoomViewForShortCode($roomId)
    {
        if (!class_exists('Plugnmeet_MenusPages')) {
            require PLUGNMEET_ROOT_PATH . "/admin/class-plugnmeet-menu-pages.php";
        }

        $class = new Plugnmeet_MenusPages();
        $roomInfo = $class->getRoomById($roomId);

        if (!$roomInfo) {
            return __('no room found', 'plugnmeet');
        }

        ob_start();
        require plugin_dir_path(dirname(__FILE__)) . 'public/partials/plugnmeet-public-display.php';
        $return_html = ob_get_clean();

        return $return_html;
    }

    public function login_to_room()
    {
        $output = new stdClass();
        $output->status = false;
        $output->msg = __("Token mismatched", 'plugnmeet');

        if (!wp_verify_nonce($_REQUEST['nonce'], 'plugnmeet_login_to_room')) {
            wp_send_json($output);
        }

        $id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : 0;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : "";
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : "";

        if (empty($id)) {
            $output->msg = __("room Id is missing", 'plugnmeet');
            wp_send_json($output);
        }

        if (empty($name) || empty($password)) {
            $output->msg = __("both name & password are required", 'plugnmeet');
            wp_send_json($output);
        }

        if (!class_exists('Plugnmeet_MenusPages')) {
            require PLUGNMEET_ROOT_PATH . "/admin/class-plugnmeet-menu-pages.php";
        }

        $class = new Plugnmeet_MenusPages();
        $roomInfo = $class->getRoomById($id);

        if (!$roomInfo) {
            $output->msg = __("no room found", 'plugnmeet');
            wp_send_json($output);
        } elseif ($roomInfo->published !== "1") {
            $output->msg = __("room not active", 'plugnmeet');
            wp_send_json($output);
        }

        if ($password === $roomInfo->moderator_pass) {
            $isAdmin = true;
        } elseif ($password === $roomInfo->attendee_pass) {
            $isAdmin = false;
        } else {
            $output->msg = __("password didn't match", 'plugnmeet');
            wp_send_json($output);
        }

        if (!class_exists("plugNmeetConnect")) {
            include PLUGNMEET_ROOT_PATH . "/helpers/plugNmeetConnect.php";
        }
        $options = get_option("plugnmeet_settings");
        $connect = new plugNmeetConnect((object)$options);
        $isRoomActive = false;
        $room_metadata = json_decode($roomInfo->room_metadata, true);

        try {
            $res = $connect->isRoomActive($roomInfo->room_id);
            $isRoomActive = $res->status;
            $output->msg = $res->msg;
        } catch (Exception $e) {
            $output->msg = $e->getMessage();
            wp_send_json($output);
        }

        if (!$isRoomActive) {
            try {
                $create = $connect->createRoom($roomInfo->room_id, $roomInfo->room_title, $roomInfo->welcome_message, $roomInfo->max_participants, "", $room_metadata);

                $isRoomActive = $create->status;
                $output->msg = $create->msg;
            } catch (Exception $e) {
                $output->msg = $e->getMessage();
                wp_send_json($output);
            }
        }
        $useId = get_current_user_id();
        if (!$useId) {
            if (!isset($_SESSION['PLUG_N_MEET_USER_ID'])) {
                $_SESSION['PLUG_N_MEET_USER_ID'] = $connect->getUUID();
            }
            $useId = $_SESSION['PLUG_N_MEET_USER_ID'];
        }

        if ($isRoomActive) {
            try {
                $join = $connect->getJoinToken($roomInfo->room_id, $name, $useId, $isAdmin);

                $output->url = get_site_url() . "/index.php?Plug-N-Meet-Conference=1&access_token=" . $join->token . "&room_title=" . $roomInfo->room_title;
                $output->status = $join->status;
                $output->msg = $join->msg;
            } catch (Exception $e) {
                $output->msg = $e->getMessage();
                wp_send_json($output);
            }
        }

        wp_send_json($output);
    }

}
