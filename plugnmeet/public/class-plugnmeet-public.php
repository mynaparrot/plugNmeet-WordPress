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
if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

class Plugnmeet_Public {

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
	 * @var object
	 */
	private $setting_params;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $plugin_prefix The unique prefix of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_prefix  = $plugin_prefix;
		$this->version        = $version;
		$this->setting_params = (object) get_option( "plugnmeet_settings" );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugnmeet-public.css', [], $this->version );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugnmeet-public.js', array( 'jquery' ), $this->version, true );
		add_thickbox();

		$nonce  = wp_create_nonce( 'plugnmeet_frontend' );
		$script = array( 'nonce' => $nonce, 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		wp_localize_script( $this->plugin_name, 'plugnmeet_frontend', $script );
	}

	public function start_session() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	public function setQueryVar( $vars ) {
		$vars[] = 'Plug-N-Meet-Conference';

		return $vars;
	}

	public function custom_add_rewrite_rule() {
		add_rewrite_rule( '^Plug-N-Meet-conference$', 'index.php?Plug-N-Meet-Conference=1', 'top' );
	}

	public function on_display_plugnmeet_conference( $template ) {
		if ( ! get_query_var( 'Plug-N-Meet-Conference' ) ) {
			return $template;
		}
		$id = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : 0;
		if ( empty( $id ) ) {
			die( __( "room Id is missing", 'plugnmeet' ) );
		}

		if ( ! class_exists( 'Plugnmeet_RoomPage' ) ) {
			require PLUGNMEET_ROOT_PATH . "/admin/class-plugnmeet-room-page.php";
		}

		$class         = new Plugnmeet_RoomPage();
		$roomInfo      = $class->getRoomById( $id );
		$room_metadata = json_decode( $roomInfo->room_metadata, true );

		$custom_design_params = isset( $room_metadata['custom_design'] ) ? $room_metadata['custom_design'] : [];
		$jsOptions            = $this->getJsOptions( $custom_design_params );

		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugnmeet-public-display-client.php';

		exit();
	}

	private function getJsOptions( $custom_design_params ) {
		$params = $this->setting_params;
		if ( ! isset( $params->client_load ) || $params->client_load === "remote" ) {
			$assets_path = $params->plugnmeet_server_url . "/assets";
		} else {
			$assets_path = plugins_url( 'public/client/dist/assets', PLUGNMEET_BASE_NAME );
		}

		$customLogo = "";
		if ( ! empty( $custom_design_params['logo'] ) ) {
			$customLogo = 'window.CUSTOM_LOGO = "' . esc_url_raw( $custom_design_params['logo'] ) . '";';
		} else if ( $params->logo ) {
			$customLogo = 'window.CUSTOM_LOGO = "' . esc_url_raw( $params->logo ) . '";';
		}

		$js = 'window.PLUG_N_MEET_SERVER_URL = "' . esc_url_raw( $params->plugnmeet_server_url ) . '";';
		$js .= 'window.STATIC_ASSETS_PATH = "' . esc_url_raw( $assets_path ) . '";';
		$js .= $customLogo;
		$js .= 'window.ENABLE_DYNACAST = "' . filter_var( $params->enable_dynacast, FILTER_VALIDATE_BOOLEAN ) . '";';
		$js .= 'window.ENABLE_SIMULCAST = "' . filter_var( $params->enable_simulcast, FILTER_VALIDATE_BOOLEAN ) . '";';

		$js .= 'window.VIDEO_CODEC = "' . esc_attr( $params->video_codec ) . '";';
		$js .= 'window.DEFAULT_WEBCAM_RESOLUTION = "' . esc_attr( $params->default_webcam_resolution ) . '";';
		$js .= 'window.DEFAULT_SCREEN_SHARE_RESOLUTION = "' . esc_attr( $params->default_screen_share_resolution ) . '";';

		$audioPreset = 'music';
		if ( isset( $params->default_audio_preset ) ) {
			$audioPreset = $params->default_audio_preset;
		}
		$js .= 'window.DEFAULT_AUDIO_PRESET = "' . esc_attr( $audioPreset ) . '";';

		$js .= 'window.STOP_MIC_TRACK_ON_MUTE = "' . filter_var( $params->stop_mic_track_on_mute, FILTER_VALIDATE_BOOLEAN ) . '";';

		$custom_designs = [];
		foreach ( $custom_design_params as $key => $val ) {
			if ( empty( $val ) ) {
				$custom_designs[ $key ] = $params->$key;
			} else {
				$custom_designs[ $key ] = $val;
			}
		}

		$custom_design_items = [];
		if ( ! empty( $custom_designs['primary_color'] ) ) {
			$custom_design_items['primary_color'] = esc_attr( $custom_designs['primary_color'] );
		}
		if ( ! empty( $custom_designs['secondary_color'] ) ) {
			$custom_design_items['secondary_color'] = esc_attr( $custom_designs['secondary_color'] );
		}
		if ( ! empty( $custom_designs['background_color'] ) ) {
			$custom_design_items['background_color'] = esc_attr( $custom_designs['background_color'] );
		}
		if ( ! empty( $custom_designs['background_image'] ) ) {
			$custom_design_items['background_image'] = esc_attr( $custom_designs['background_image'] );
		}
		if ( ! empty( $custom_designs['header_color'] ) ) {
			$custom_design_items['header_bg_color'] = esc_attr( $custom_designs['header_color'] );
		}
		if ( ! empty( $custom_designs['footer_color'] ) ) {
			$custom_design_items['footer_bg_color'] = esc_attr( $custom_designs['footer_color'] );
		}
		if ( ! empty( $custom_designs['left_color'] ) ) {
			$custom_design_items['left_side_bg_color'] = esc_attr( $custom_designs['left_color'] );
		}
		if ( ! empty( $custom_designs['right_color'] ) ) {
			$custom_design_items['right_side_bg_color'] = esc_attr( $custom_designs['right_color'] );
		}
		if ( ! empty( $custom_designs['custom_css_url'] ) ) {
			$custom_design_items['custom_css_url'] = esc_attr( $custom_designs['custom_css_url'] );
		}
		if ( ! empty( $custom_designs['column_camera_position'] ) ) {
			$custom_design_items['column_camera_position'] = esc_attr( $custom_designs['column_camera_position'] );
		}
		if ( ! empty( $custom_designs['column_camera_width'] ) ) {
			$custom_design_items['column_camera_width'] = esc_attr( $custom_designs['column_camera_width'] );
		}

		if ( count( $custom_design_items ) > 0 ) {
			$js .= 'window.DESIGN_CUSTOMIZATION = `' . json_encode( $custom_design_items ) . '`;';
		}

		return $js;
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
	 *
	 * @param array $atts ShortCode Attributes.
	 * @param mixed $content ShortCode enclosed content.
	 * @param string $tag The Shortcode tag.
	 */
	public function plugnmeet_shortcode_room_view( $atts, $content = null, $tag = "" ) {

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
		$id = intval( $atts['id'] );

		/**
		 * If the shortcode is enclosing, we may want to do something with $content
		 */
		if ( ! empty( $content ) ) {
			$id = do_shortcode( $content );// We can parse shortcodes inside $content.
			$id = intval( $atts['id'] ) . ' ' . sanitize_text_field( $id );// Remember to sanitize your user input.
		}

		if ( ! $id ) {
			return null;
		}

		// ShortCodes are filters and should always return, never echo.
		return $this->formatRoomViewForShortCode( $id );

	}

	private function formatRoomViewForShortCode( $roomId ) {
		if ( ! class_exists( 'Plugnmeet_RoomPage' ) ) {
			require PLUGNMEET_ROOT_PATH . "/admin/class-plugnmeet-room-page.php";
		}

		$class    = new Plugnmeet_RoomPage();
		$roomInfo = $class->getRoomById( $roomId );

		if ( ! $roomInfo ) {
			return __( 'no room found', 'plugnmeet' );
		}

		ob_start();
		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugnmeet-public-display.php';
		$return_html = ob_get_clean();

		return $return_html;
	}
}
