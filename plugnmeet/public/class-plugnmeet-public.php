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
		// Don't start a session for WP-CLI or REST API requests.
		if ( ( defined( 'WP_CLI' ) && WP_CLI ) || wp_is_serving_rest_request() ) {
			return;
		}

		// Don't start a session on "true" admin pages, but DO allow it for AJAX requests.
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

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
		$config = $this->setting_params;
		if ( ! isset( $config->client_load ) || $config->client_load === "remote" ) {
			$assetsPath = $config->plugnmeet_server_url . "/assets";
		} else {
			$assetsPath = plugins_url( 'public/client/dist/assets', PLUGNMEET_BASE_NAME );
		}

		$plugNmeetConfig = [
			// The URL of your plugNmeet server.
			'serverUrl'                    => esc_url_raw( $config->plugnmeet_server_url ),

			// This is helpful for external plugin development where images or other files are located
			// in another place.
			'staticAssetsPath'             => esc_url_raw( $assetsPath ),

			// Dynacast dynamically pauses video layers that are not being consumed by any subscribers,
			// significantly reducing publishing CPU and bandwidth usage.
			'enableDynacast'               => filter_var( $config->enable_dynacast, FILTER_VALIDATE_BOOLEAN ),

			// When using simulcast, LiveKit will publish up to three versions of the stream at various resolutions.
			// The client can then pick the most appropriate one.
			'enableSimulcast'              => filter_var( $config->enable_simulcast, FILTER_VALIDATE_BOOLEAN ),

			// Available options: 'vp8' | 'h264' | 'vp9' | 'av1'. Default: 'vp8'.
			'videoCodec'                   => esc_attr( $config->video_codec ),

			// Available options: 'h90' | 'h180' | 'h216' | 'h360' | 'h540' | 'h720' | 'h1080' | 'h1440' | 'h2160'.
			// Default: 'h720'.
			'defaultWebcamResolution'      => esc_attr( $config->default_webcam_resolution ),

			// Available options: 'h360fps3' | 'h720fps5' | 'h720fps15' | 'h1080fps15' | 'h1080fps30'.
			// Default: 'h1080fps15'.
			'defaultScreenShareResolution' => esc_attr( $config->default_screen_share_resolution ),

			// Available options: 'telephone' | 'speech' | 'music' | 'musicStereo' | 'musicHighQuality' | 'musicHighQualityStereo'.
			// Default: 'music'.
			'defaultAudioPreset'           => isset( $config->default_audio_preset ) ? esc_attr( $config->default_audio_preset ) : 'music',

			// For local tracks, stop the underlying MediaStreamTrack when the track is muted (or paused).
			'stopMicTrackOnMute'           => filter_var( $config->stop_mic_track_on_mute, FILTER_VALIDATE_BOOLEAN ),

			// If true, the webcam view will be relocated and arranged based on the active speaker.
			// Default: true.
			'focusActiveSpeakerWebcam'     => true,
		];

		$custom_designs = [];
		foreach ( $custom_design_params as $key => $val ) {
			if ( empty( $val ) ) {
				$custom_designs[ $key ] = isset( $config->$key ) ? $config->$key : '';
			} else {
				$custom_designs[ $key ] = $val;
			}
		}

		$designCustomization = [];
		if ( ! empty( $custom_designs['primary_color'] ) ) {
			$designCustomization['primary_color'] = esc_attr( $custom_designs['primary_color'] );
		}
		if ( ! empty( $custom_designs['secondary_color'] ) ) {
			$designCustomization['secondary_color'] = esc_attr( $custom_designs['secondary_color'] );
		}
		if ( ! empty( $custom_designs['background_color'] ) ) {
			$designCustomization['background_color'] = esc_attr( $custom_designs['background_color'] );
		}
		if ( ! empty( $custom_designs['background_image'] ) ) {
			$designCustomization['background_image'] = esc_url_raw( $custom_designs['background_image'] );
		}
		if ( ! empty( $custom_designs['header_color'] ) ) {
			$designCustomization['header_bg_color'] = esc_attr( $custom_designs['header_color'] );
		}
		if ( ! empty( $custom_designs['footer_color'] ) ) {
			$designCustomization['footer_bg_color'] = esc_attr( $custom_designs['footer_color'] );
		}
		if ( ! empty( $custom_designs['left_color'] ) ) {
			$designCustomization['left_side_bg_color'] = esc_attr( $custom_designs['left_color'] );
		}
		if ( ! empty( $custom_designs['right_color'] ) ) {
			$designCustomization['right_side_bg_color'] = esc_attr( $custom_designs['right_color'] );
		}
		if ( ! empty( $custom_designs['custom_css_url'] ) ) {
			$designCustomization['custom_css_url'] = esc_url_raw( $custom_designs['custom_css_url'] );
		}
		if ( ! empty( $custom_designs['column_camera_position'] ) ) {
			$designCustomization['column_camera_position'] = esc_attr( $custom_designs['column_camera_position'] );
		}
		if ( ! empty( $custom_designs['column_camera_width'] ) ) {
			$designCustomization['column_camera_width'] = esc_attr( $custom_designs['column_camera_width'] );
		}

		if ( ! empty( $custom_design_params['logo'] ) ) {
			$designCustomization['custom_logo'] = esc_url_raw( $custom_design_params['logo'] );
		} else if ( ! empty( $config->logo ) ) {
			$designCustomization['custom_logo'] = esc_url_raw( $config->logo );
		}

		if ( ! empty( $designCustomization ) ) {
			$plugNmeetConfig['designCustomization'] = $designCustomization;
		}

		$jsonConfig = json_encode( $plugNmeetConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		return "window.plugNmeetConfig = JSON.parse(`" . addslashes( $jsonConfig ) . "`);";
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

		$atts = shortcode_atts(
			array(
				'id' => 0, // Default to 0 to better handle cases where no ID is provided.
			),
			$atts,
			$this->plugin_prefix . 'room_view'
		);

		$id = intval( $atts['id'] );

		/**
		 * If the shortcode is enclosing, the content will be used as the ID,
		 * overriding the 'id' attribute.
		 * e.g. [plugnmeet_room_view]123[/plugnmeet_room_view]
		 */
		if ( ! empty( $content ) ) {
			$id = intval( do_shortcode( $content ) );
		}

		if ( ! $id ) {
			// If no ID is provided via attribute or content, there's nothing to show.
			return __( 'Room ID not provided for the shortcode.', 'plugnmeet' );
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
