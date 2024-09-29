<?php
/**
 *
 * @since      1.0.10
 * @package    Plugnmeet
 * @subpackage Plugnmeet/helpers
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

class PlugNmeetAjaxHelper {
	private $setting_params;

	public function __construct() {
		$this->setting_params = (object) get_option( "plugnmeet_settings" );
	}

	public function get_recordings() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Token mismatched', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_get_recordings' ) ) {
			wp_send_json( $output );
		}

		if ( ! class_exists( "plugNmeetConnect" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
		}
		$roomId  = isset( $_POST['roomId'] ) ? sanitize_text_field( $_POST['roomId'] ) : "";
		$from    = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : 0;
		$limit   = isset( $_POST['limit'] ) ? sanitize_text_field( $_POST['limit'] ) : 20;
		$orderBy = isset( $_POST['order_by'] ) ? sanitize_text_field( $_POST['order_by'] ) : "DESC";

		if ( empty( $roomId ) ) {
			$output->msg = __( "room id required", 'plugnmeet' );
			wp_send_json( $output );
		}

		$check = $this->canAccess( $roomId, 'can_view_recording' );
		if ( ! $check->status ) {
			$output->msg = $check->msg;
			wp_send_json( $output );
		}

		$options = $this->setting_params;
		$connect = new plugNmeetConnect( $options );
		$roomIds = array( $roomId );
		$res     = $connect->getRecordings( $roomIds, $from, $limit, $orderBy );

		$output->status = $res->getStatus();
		$output->msg    = $res->getResponseMsg();
		$output->result = $res->getRawResponse()->result;

		wp_send_json( $output );
	}

	public function download_recording() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Token mismatched', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_download_recording' ) ) {
			wp_send_json( $output );
		}

		if ( ! class_exists( "plugNmeetConnect" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
		}

		$recordingId = isset( $_POST['recordingId'] ) ? sanitize_text_field( $_POST['recordingId'] ) : null;
		$roomId      = isset( $_POST['roomId'] ) ? sanitize_text_field( $_POST['roomId'] ) : null;
		$role        = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : 'can_download';

		if ( ! $recordingId || ! $roomId ) {
			$output->msg = __( "both roomId & record id required", 'plugnmeet' );
			wp_send_json( $output );
		}

		$check = $this->canAccess( $roomId, $role );
		if ( ! $check->status ) {
			$output->msg = $check->msg;
			wp_send_json( $output );
		}

		$params         = $this->setting_params;
		$connect        = new plugNmeetConnect( $params );
		$res            = $connect->getRecordingDownloadLink( $recordingId );
		$output->status = $res->getStatus();
		$output->msg    = $res->getResponseMsg();

		if ( $res->getStatus() && $res->getToken() ) {
			$output->url = $params->plugnmeet_server_url . "/download/recording/" . $res->getToken();
		}

		wp_send_json( $output );
	}

	public function delete_recording() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Token mismatched', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_delete_recording' ) ) {
			wp_send_json( $output );
		}

		if ( ! class_exists( "plugNmeetConnect" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
		}

		$recordingId = isset( $_POST['recordingId'] ) ? sanitize_text_field( $_POST['recordingId'] ) : null;
		$roomId      = isset( $_POST['roomId'] ) ? sanitize_text_field( $_POST['roomId'] ) : null;

		if ( ! $recordingId || ! $roomId ) {
			$output->msg = __( "both roomId & record id required", 'plugnmeet' );
			wp_send_json( $output );
		}

		$check = $this->canAccess( $roomId, 'can_delete' );
		if ( ! $check->status ) {
			$output->msg = $check->msg;
			wp_send_json( $output );
		}

		$params         = $this->setting_params;
		$connect        = new plugNmeetConnect( $params );
		$res            = $connect->deleteRecording( $recordingId );
		$output->status = $res->getStatus();
		$output->msg    = $res->getResponseMsg();

		if ( $output->status ) {
			$output->msg = __( "Recording was deleted successfully", 'plugnmeet' );
		}

		wp_send_json( $output );
	}

	public function login_to_room() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( "Token mismatched", 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_login_to_room' ) ) {
			wp_send_json( $output );
		}

		$id          = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : "";
		$password    = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : "";
		$current_url = isset( $_POST['current_url'] ) ? sanitize_url( urldecode( $_POST['current_url'] ) ) : "";

		// create logout url
		$logoutUrl = "";
		if ( ! empty( $current_url ) ) {
			$url        = parse_url( $current_url );
			$logoutUrl  = sprintf( "%s://%s%s",
				$url["scheme"],
				$url["host"],
				$url["path"]
			);
			$parameters = array();
			if ( ! empty( $url["query"] ) ) {
				parse_str( $url["query"], $parameters );
			}
			$parameters["pnm-returned"] = "true";
			$logoutUrl                  = $logoutUrl . "?" . http_build_query( $parameters );
		}

		if ( empty( $id ) ) {
			$output->msg = __( "room Id is missing", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( empty( $name ) ) {
			$output->msg = __( "name is required", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! class_exists( 'Plugnmeet_RoomPage' ) ) {
			require PLUGNMEET_ROOT_PATH . "/admin/class-plugnmeet-room-page.php";
		}

		$class    = new Plugnmeet_RoomPage();
		$roomInfo = $class->getRoomById( $id );

		if ( ! $roomInfo ) {
			$output->msg = __( "no room found", 'plugnmeet' );
			wp_send_json( $output );
		} elseif ( $roomInfo->published !== "1" ) {
			$output->msg = __( "room not active", 'plugnmeet' );
			wp_send_json( $output );
		}

		$roleDetermine = $this->determineUserType( $roomInfo, $password );
		if ( ! $roleDetermine->status ) {
			$output->msg = $roleDetermine->msg;
			wp_send_json( $output );
		}
		$isAdmin = $roleDetermine->isAdmin;

		if ( ! class_exists( "plugNmeetConnect" ) ) {
			include PLUGNMEET_ROOT_PATH . "/helpers/plugNmeetConnect.php";
		}

		$connect       = new plugNmeetConnect( $this->setting_params );
		$isRoomActive  = false;
		$room_metadata = json_decode( $roomInfo->room_metadata, true );

		try {
			$res = $connect->isRoomActive( $roomInfo->room_id );
			if ( ! $res->getStatus() ) {
				$output->msg = $res->getResponseMsg();
				wp_send_json( $output );
			}
			$isRoomActive = $res->isActive();
			$output->msg  = $res->getResponseMsg();
		} catch ( Exception $e ) {
			$output->msg = $e->getMessage();
			wp_send_json( $output );
		}

		if ( ! $isRoomActive
		     && ! $isAdmin
		     && isset( $room_metadata["room_features"]["moderator_join_first"] )
		     && $room_metadata["room_features"]["moderator_join_first"] == 1 ) {
			$output->msg = __( "The meeting has not started yet, please come back later.", "plugnmeet" );
			wp_send_json( $output );
		}

		if ( ! $isRoomActive ) {
			try {
				$extraData = json_encode( array(
					"platform"       => "wordpress",
					"php-version"    => phpversion(),
					"plugin-version" => constant( 'PLUGNMEET_VERSION' )
				) );
				$config    = (object) get_option( "plugnmeet_settings" );
				if ( isset( $config->copyright_display ) ) {
					$room_metadata["copyright_conf"] = array(
						"display" => $config->copyright_display === "true",
						"text"    => $config->copyright_text
					);
				}

				$create = $connect->createRoom( $roomInfo->room_id, $roomInfo->room_title, $roomInfo->welcome_message, $roomInfo->max_participants, "", $room_metadata, 0, $logoutUrl, $extraData );

				$isRoomActive = $create->getStatus();
				$output->msg  = $create->getResponseMsg();
			} catch ( Exception $e ) {
				$output->msg = $e->getMessage();
				wp_send_json( $output );
			}
		}
		$useId = get_current_user_id();
		if ( ! $useId ) {
			if ( ! isset( $_SESSION['PLUG_N_MEET_USER_ID'] ) ) {
				$_SESSION['PLUG_N_MEET_USER_ID'] = $connect->getUUID();
			}
			$useId = esc_attr( $_SESSION['PLUG_N_MEET_USER_ID'] );
		}

		if ( $isRoomActive ) {
			try {
				$join = $connect->getJoinToken( $roomInfo->room_id, $name, $useId, $isAdmin );

				$output->url    = get_site_url() . "/index.php?access_token=" . $join->getToken() . "&id=" . $id . "&Plug-N-Meet-Conference=1";
				$output->status = $join->getStatus();
				$output->msg    = $join->getResponseMsg();
			} catch ( Exception $e ) {
				$output->msg = $e->getMessage();
				wp_send_json( $output );
			}
		}

		wp_send_json( $output );
	}

	private function determineUserType( $roomInfo, $password ) {
		$output          = new stdClass();
		$output->status  = false;
		$output->isAdmin = false;
		$output->msg     = __( "you don't have permission", 'plugnmeet' );

		if ( ! empty( $password ) ) {
			if ( $password === $roomInfo->moderator_pass ) {
				$output->status  = true;
				$output->isAdmin = true;
			} elseif ( $password === $roomInfo->attendee_pass ) {
				$output->status  = true;
				$output->isAdmin = false;
			} else {
				$output->msg = __( "password didn't match", 'plugnmeet' );
			}

			return $output;
		}

		if ( ! empty( $roomInfo->roles ) ) {
			$user  = wp_get_current_user();
			$roles = json_decode( $roomInfo->roles, true );

			if ( $user->ID ) {
				$userRole = $user->roles[0]; // at present let's consider the first one only
			} else {
				$userRole = 'guest';
			}

			if ( ! isset( $roles[ $userRole ] ) ) {
				return $output;
			}

			$role = $roles[ $userRole ];
			if ( isset( $role['require_password'] ) && $role['require_password'] === "on" ) {
				return $output;
			}

			if ( $role['join_as'] === "moderator" ) {
				$output->status  = true;
				$output->isAdmin = true;
			} else {
				$output->status  = true;
				$output->isAdmin = false;
			}
		}

		return $output;
	}

	private function canAccess( $roomId, $checkFor ) {
		global $wpdb;
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( "you don't have permission", 'plugnmeet' );

		$roomInfo = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE room_id = %s",
			$roomId
		) );

		if ( ! $roomInfo ) {
			$output->msg = __( "no room found", 'plugnmeet' );

			return $output;
		} elseif ( $roomInfo->published !== "1" ) {
			$output->msg = __( "room not active", 'plugnmeet' );

			return $output;
		}

		if ( ! empty( $roomInfo->roles ) ) {
			$user  = wp_get_current_user();
			$roles = json_decode( $roomInfo->roles, true );

			if ( $user->ID ) {
				$userRole = $user->roles[0]; // at present let's consider the first one only
			} else {
				$userRole = 'guest';
			}

			if ( ! isset( $roles[ $userRole ] ) ) {
				return $output;
			}

			$role = $roles[ $userRole ];
			if ( isset( $role[ $checkFor ] ) && $role[ $checkFor ] === "on" ) {
				$output->status = true;
			}
		}

		return $output;
	}
}
