<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

class Plugnmeet_Admin {

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

	private $setting_params;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
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
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {
		if ( preg_match( "/plugnmeet/", $hook_suffix ) ) {
			wp_enqueue_style( 'bootstrap-min', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css' );
			wp_enqueue_style( 'bootstrap-colorpicker', plugin_dir_url( __FILE__ ) . 'css/bootstrap-colorpicker.min.css' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugnmeet-admin.css' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {
		wp_enqueue_media();
		if ( preg_match( "/plugnmeet/", $hook_suffix ) ) {
			wp_enqueue_script( "bootstrap-bundle", plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.js', array(), $this->version );
			wp_enqueue_script( "bootstrap-colorpicker", plugin_dir_url( __FILE__ ) . 'js/bootstrap-colorpicker.min.js', array(), $this->version );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugnmeet-admin.js', array( 'jquery' ), $this->version, false );
		}

		$nonce  = wp_create_nonce( 'ajax_admin' );
		$script = array( 'nonce' => $nonce );
		wp_localize_script( $this->plugin_name, 'ajax_admin', $script );
	}

	public function addMenuPages( $hook_suffix ) {
		if ( ! class_exists( "Plugnmeet_RoomPage" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-plugnmeet-room-page.php';
		}
		$menusPage = new Plugnmeet_RoomPage();

		add_menu_page(
			__( 'Plug-N-Meet', 'plugnmeet' ),
			__( 'Plug-N-Meet', 'plugnmeet' ),
			'manage_options',
			'plugnmeet',
			'',
			'dashicons-admin-site-alt',
			null
		);

		add_submenu_page(
			'plugnmeet',
			__( 'Manage Rooms', 'plugnmeet' ),
			__( 'Rooms', 'plugnmeet' ),
			'manage_options',
			'plugnmeet',
			[ $menusPage, 'roomsPage' ],
			1
		);

		add_submenu_page(
			'plugnmeet',
			__( 'Manage recordings', 'plugnmeet' ),
			__( 'Recordings', 'plugnmeet' ),
			'manage_options',
			'plugnmeet-recordings',
			[ $menusPage, 'recordingsPage' ],
			2
		);

		add_submenu_page(
			'plugnmeet',
			__( 'Settings', 'plugnmeet' ),
			__( 'Settings', 'plugnmeet' ),
			'manage_options',
			'plugnmeet-settings',
			[ $menusPage, 'settingsPage' ],
			3
		);
	}

	public function register_settings() {
		if ( ! class_exists( "Plugnmeet_SettingsPage" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-plugnmeet-settings-page.php';
		}

		$settingPage = new Plugnmeet_SettingsPage();
		$settingPage->plugnmeet_register_settings();
	}

	public function update_client() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Token mismatched', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'ajax_admin' ) ) {
			wp_send_json( $output );
		}

		$params              = $this->setting_params;
		$client_download_url = $params->client_download_url;
		if ( empty( $client_download_url ) ) {
			$client_download_url = "https://github.com/mynaparrot/plugNmeet-client/releases/latest/download/client.zip";
		}

		$response = wp_remote_get( $client_download_url, array(
			"timeout" => 60
		) );

		if ( is_wp_error( $response ) ) {
			$output->msg = $response->errors;
			wp_send_json( $output );
		}

		$data          = wp_remote_retrieve_body( $response );
		$clientZipFile = get_temp_dir() . "client.zip";
		$file          = fopen( $clientZipFile, "w+" );

		if ( ! $file ) {
			$output->msg = __( "Can't write file", "plugnmeet" );
			wp_send_json( $output );
		}
		fputs( $file, $data );
		fclose( $file );

		$zip = new ZipArchive;
		$res = $zip->open( $clientZipFile );
		if ( $res === true ) {
			$extractPath = PLUGNMEET_ROOT_PATH . "/public/";
			// for safety let's delete client first
			$this->deleteDir( $extractPath . "client" );

			$zip->extractTo( $extractPath );
			$zip->close();
			unlink( $clientZipFile );

			$output->status = true;
			$output->msg    = __( "Updated client successfully", "plugnmeet" );
		} else {
			$output->msg = __( "Unzip failed", "plugnmeet" );
		}

		wp_send_json( $output );
	}

	private function deleteDir( $dirPath ) {
		if ( ! is_dir( $dirPath ) ) {
			return;
		}
		if ( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ) {
			$dirPath .= '/';
		}
		$it    = new RecursiveDirectoryIterator( $dirPath, RecursiveDirectoryIterator::SKIP_DOTS );
		$files = new RecursiveIteratorIterator( $it,
			RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $files as $file ) {
			if ( $file->isDir() ) {
				rmdir( $file->getRealPath() );
			} else {
				unlink( $file->getRealPath() );
			}
		}

		rmdir( $dirPath );
	}

	public function save_room_data() {
		global $wpdb;
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Token mismatched', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'save_room_data' ) ) {
			wp_send_json( $output );
		}

		if ( ! class_exists( "PlugnmeetHelper" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/helper.php';
		}

		// for preventing display error. Room id should be always unique
		$room_id = "";

		$id               = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : 0;
		$room_title       = isset( $_POST['room_title'] ) ? sanitize_text_field( $_POST['room_title'] ) : "";
		$description      = isset( $_POST['description'] ) ? wp_kses( $_POST['description'], wp_kses_allowed_html( "post" ) ) : "";
		$moderator_pass   = isset( $_POST['moderator_pass'] ) ? sanitize_text_field( $_POST['moderator_pass'] ) : "";
		$attendee_pass    = isset( $_POST['attendee_pass'] ) ? sanitize_text_field( $_POST['attendee_pass'] ) : "";
		$welcome_message  = isset( $_POST['welcome_message'] ) ? sanitize_textarea_field( $_POST['welcome_message'] ) : "";
		$max_participants = isset( $_POST['max_participants'] ) ? sanitize_text_field( $_POST['max_participants'] ) : 0;
		$published        = isset( $_POST['published'] ) ? sanitize_text_field( $_POST['published'] ) : 1;
		$roles            = isset( $_POST['roles'] ) ? $_POST['roles'] : array();

		$room_metadata = [];
		foreach ( PlugnmeetHelper::$roomMetadataItems as $item ) {
			if ( isset( $_POST[ $item ] ) ) {
				$room_metadata[ $item ] = $_POST[ $item ];
			} else {
				$room_metadata[ $item ] = [];
			}
		}

		if ( empty( $moderator_pass ) ) {
			$moderator_pass = PlugnmeetHelper::secureRandomKey( 10 );
		}

		if ( empty( $attendee_pass ) ) {
			$attendee_pass = PlugnmeetHelper::secureRandomKey( 10 );
		}

		if ( $attendee_pass === $moderator_pass ) {
			$output->msg = __( "attendee & moderator password can't be same", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! $id ) {
			if ( ! class_exists( 'plugNmeetConnect' ) ) {
				require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
			}
			$options = $this->setting_params;
			$connect = new plugNmeetConnect( $options );
			$room_id = $connect->getUUID();
		}

		if ( ! $id ) {
			$wpdb->insert(
				$wpdb->prefix . "plugnmeet_rooms",
				array(
					'room_id'          => $room_id,
					'room_title'       => $room_title,
					'description'      => $description,
					'moderator_pass'   => $moderator_pass,
					'attendee_pass'    => $attendee_pass,
					'welcome_message'  => $welcome_message,
					'max_participants' => $max_participants,
					'room_metadata'    => json_encode( $room_metadata ),
					'roles'            => json_encode( $roles ),
					'published'        => $published,
					'created_by'       => get_current_user_id()
				),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d' )
			);

			if ( $wpdb->insert_id ) {
				$output->status = true;
				$output->msg    = __( 'Successfully saved room data', 'plugnmeet' );
			} else {
				$output->msg = $wpdb->last_error;
			}
		} else {
			$result = $wpdb->update(
				$wpdb->prefix . "plugnmeet_rooms",
				array(
					'room_title'       => $room_title,
					'description'      => $description,
					'moderator_pass'   => $moderator_pass,
					'attendee_pass'    => $attendee_pass,
					'welcome_message'  => $welcome_message,
					'max_participants' => $max_participants,
					'room_metadata'    => json_encode( $room_metadata ),
					'roles'            => json_encode( $roles ),
					'published'        => $published,
					'modified_by'      => get_current_user_id()
				),
				array(
					'id' => $id
				),
				array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d' ),
				array( '%d' )
			);

			if ( $result === false ) {
				$output->msg = $wpdb->last_error;
			} else {
				$output->status = true;
				$output->msg    = __( 'Successfully updated room data', 'plugnmeet' );
			}
		}

		wp_send_json( $output );
	}

	public function delete_room() {
		global $wpdb;
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Token mismatched', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'ajax_admin' ) ) {
			wp_send_json( $output );
		}
		$id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : 0;

		if ( ! $id ) {
			$output->msg = __( "No id was sent", 'plugnmeet' );
			wp_send_json( $output );
		}

		$result = $wpdb->delete(
			$wpdb->prefix . 'plugnmeet_rooms',
			[ 'id' => $id ],
			[ '%d' ],
		);

		if ( $result === false ) {
			$output->msg = $wpdb->last_error;
		} else {
			$output->status = true;
			$output->msg    = "success";
		}

		wp_send_json( $output );
	}
}
