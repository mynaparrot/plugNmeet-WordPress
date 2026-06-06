<?php
/**
 *
 * @since      1.0.10
 * @package    Plugnmeet
 * @subpackage Plugnmeet/helpers
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

use Mynaparrot\PlugnmeetProto\MergeRecordingsByIds;
use Mynaparrot\PlugnmeetProto\MergeRecordingsReq;
use Mynaparrot\PlugnmeetProto\RoomArtifactType;

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

if ( ! class_exists( "plugNmeetConnect" ) ) {
	require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
}

class PlugNmeetAjaxHelper {
	private $setting_params;

	public function __construct() {
		$this->setting_params = (object) get_option( "plugnmeet_settings" );
	}

	/**
	 * Helper to safely retrieve and sanitize POST parameters.
	 *
	 * @param string $key The key of the POST parameter.
	 * @param mixed $default The default value if the parameter is not set.
	 * @param callable|null $sanitizer A callable function for custom sanitization.
	 *
	 * @return mixed The sanitized POST parameter or the default value.
	 */
	private function get_post_param( string $key, $default = '', ?callable $sanitizer = null ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			return $default;
		}
		$value = wp_unslash( $_POST[ $key ] ); // Always unslash first

		if ( $sanitizer ) {
			return call_user_func( $sanitizer, $value );
		}

		// Default sanitization for simple text fields
		return sanitize_text_field( $value );
	}

	public function get_recordings() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_get_recordings' ) ) {
			wp_send_json( $output );
		}

		$roomId  = $this->get_post_param( 'roomId' );
		$from    = (int) $this->get_post_param( 'from', 0 );
		$limit   = (int) $this->get_post_param( 'limit', 20 );
		$orderBy = $this->get_post_param( 'order_by', 'DESC' );

		if ( empty( $roomId ) ) {
			$output->msg = __( "We couldn't find the room you're looking for. Please check the link and try again.", 'plugnmeet' );
			wp_send_json( $output );
		}

		$check = $this->canAccess( $roomId, 'can_view_recording' );
		if ( ! $check->status ) {
			$output->msg = $check->msg;
			wp_send_json( $output );
		}

		$cache_key   = 'pnm_recordings_' . $roomId . '_' . $from . '_' . $limit . '_' . $orderBy;
		$cached_data = wp_cache_get( $cache_key, 'pnm_recordings' );

		if ( false !== $cached_data ) {
			wp_send_json( $cached_data );
		}

		$options = $this->setting_params;
		$connect = new plugNmeetConnect( $options );
		$roomIds = array( $roomId );
		$res     = $connect->getRecordings( $roomIds, null, (int) $from, (int) $limit, $orderBy );

		$output->status = $res->getStatus();
		$output->msg    = $res->getMsg();
		if ( $res->getStatus() ) {
			$output->result = $res->getResult()->serializeToJsonString();
			wp_cache_set( $cache_key, $output, 'pnm_recordings', HOUR_IN_SECONDS );
		}

		wp_send_json( $output );
	}

	public function get_artifacts() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_get_artifacts' ) ) {
			wp_send_json( $output );
		}

		$roomId  = $this->get_post_param( 'roomId' );
		$from    = (int) $this->get_post_param( 'from', 0 );
		$limit   = (int) $this->get_post_param( 'limit', 20 );
		$orderBy = $this->get_post_param( 'order_by', 'DESC' );

		if ( empty( $roomId ) ) {
			$output->msg = __( "We couldn't find the room you're looking for. Please check the link and try again.", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$output->msg = __( "You do not have permission to view this page.", "plugnmeet" );
			wp_send_json( $output );
		}

		$cache_key   = 'pnm_artifacts_' . $roomId . '_' . $from . '_' . $limit . '_' . $orderBy;
		$cached_data = wp_cache_get( $cache_key, 'pnm_artifacts' );

		if ( false !== $cached_data ) {
			wp_send_json( $cached_data );
		}

		$options = $this->setting_params;
		$connect = new plugNmeetConnect( $options );
		$roomIds = array( $roomId );
		$res     = $connect->getArtifacts( $roomIds, null, null, (int) $from, (int) $limit, $orderBy );

		$output->status = $res->getStatus();
		$output->msg    = $res->getMsg();
		if ( $res->getStatus() ) {
			$artifacts        = $res->getResult()->getArtifactsList();
			$result_artifacts = [];
			$page             = (int) ( $from / $limit ) + 1;

			foreach ( $artifacts as $artifact ) {
				$result_artifacts[] = [
					'artifact_id' => $artifact->getArtifactId(),
					'type'        => $this->format_type_name( $artifact->getType() ),
					'created'     => gmdate( "Y-m-d H:i:s", strtotime( $artifact->getCreated() ) ),
					'view_url'    => admin_url( 'admin.php?page=plugnmeet-artifacts&artifact_id=' . $artifact->getArtifactId() . "&room_id=" . $roomId . "&paged=" . $page ),
				];
			}

			$result_obj                 = new stdClass();
			$result_obj->artifactsList  = $result_artifacts;
			$result_obj->totalArtifacts = $res->getResult()->getTotalArtifacts();

			$output->result = json_encode( $result_obj );
			wp_cache_set( $cache_key, $output, 'pnm_artifacts', HOUR_IN_SECONDS );
		}

		wp_send_json( $output );
	}

	public function download_artifact() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_download_artifact' ) ) {
			wp_send_json( $output );
		}

		$artifact_id = $this->get_post_param( 'artifact_id' );

		if ( empty( $artifact_id ) ) {
			$output->msg = __( "We couldn't find the item you're trying to download. Please try again.", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$output->msg = __( "You do not have permission to perform this action.", "plugnmeet" );
			wp_send_json( $output );
		}

		$params         = $this->setting_params;
		$connect        = new plugNmeetConnect( $params );
		$res            = $connect->getArtifactDownloadToken( $artifact_id );
		$output->status = $res->getStatus();
		$output->msg    = $res->getMsg();

		if ( $res->getStatus() && $res->getToken() ) {
			$output->url = $params->plugnmeet_server_url . "/download/artifact/" . $res->getToken();
		}

		wp_send_json( $output );
	}

	public function download_analytics() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_download_analytics' ) ) {
			wp_send_json( $output );
		}

		if ( ! class_exists( "Plugnmeet_AnalyticsHelper" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/analyticsHelper.php';
		}

		$artifact_id = $this->get_post_param( 'artifact_id' );

		if ( empty( $artifact_id ) ) {
			$output->msg = __( "We couldn't find the item you're trying to download. Please try again.", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$output->msg = __( "You do not have permission to perform this action.", "plugnmeet" );
			wp_send_json( $output );
		}

		try {
			$analyticsHelper = new Plugnmeet_AnalyticsHelper( $artifact_id );
			$file            = $analyticsHelper->generate_xlsx_file();
			$output->status  = true;
			$output->msg     = 'Success! Your download will begin shortly.';
			$output->url     = $file['url'];
		} catch ( Exception $e ) {
			$output->msg = $e->getMessage();
		}

		wp_send_json( $output );
	}

	public function delete_artifact() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_delete_artifact' ) ) {
			wp_send_json( $output );
		}

		$artifact_id = $this->get_post_param( 'artifact_id' );

		if ( empty( $artifact_id ) ) {
			$output->msg = __( "We couldn't find the item you're trying to delete. Please try again.", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$output->msg = __( "You do not have permission to perform this action.", "plugnmeet" );
			wp_send_json( $output );
		}

		$options = (object) get_option( "plugnmeet_settings" );
		$connect = new plugNmeetConnect( $options );
		$res     = $connect->deleteArtifact( $artifact_id );

		$output->status = $res->getStatus();
		$output->msg    = $res->getMsg();

		if ( $output->status ) {
			$output->msg = __( "The item was deleted successfully.", "plugnmeet" );
			wp_cache_flush_group( 'pnm_artifacts' );

			// delete single artifact cache as well
			$key = sprintf( "artifact-%s", $artifact_id );
			wp_cache_delete( $key, "pnm_artifact" );
		}

		wp_send_json( $output );
	}

	public function download_recording() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_download_recording' ) ) {
			wp_send_json( $output );
		}

		$recordingId = $this->get_post_param( 'recordingId' );
		$roomId      = $this->get_post_param( 'roomId' );
		$role        = $this->get_post_param( 'role', 'can_download' );

		if ( empty( $recordingId ) || empty( $roomId ) ) {
			$output->msg = __( "We couldn't find the recording you're trying to download. Please try again.", 'plugnmeet' );
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
		$output->msg    = $res->getMsg();

		if ( $res->getStatus() && $res->getToken() ) {
			$output->url = $params->plugnmeet_server_url . "/download/recording/" . $res->getToken();
		}

		wp_send_json( $output );
	}

	public function delete_recording() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_delete_recording' ) ) {
			wp_send_json( $output );
		}

		$recordingId = $this->get_post_param( 'recordingId' );
		$roomId      = $this->get_post_param( 'roomId' );

		if ( empty( $recordingId ) || empty( $roomId ) ) {
			$output->msg = __( "We couldn't find the recording you're trying to delete. Please try again.", 'plugnmeet' );
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
		$output->msg    = $res->getMsg();

		if ( $output->status ) {
			$output->msg = __( "The recording was deleted successfully.", "plugnmeet" );
			wp_cache_flush_group( 'pnm_recordings' );
		}

		wp_send_json( $output );
	}

	public function merge_recordings() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_merge_recordings' ) ) {
			wp_send_json( $output );
		}

		$recordings = $this->get_post_param( 'recordings', [] );
		$roomId     = $this->get_post_param( 'roomId' );

		if ( count( $recordings ) < 2 || empty( $roomId ) ) {
			$output->msg = __( "You must select at least two recordings to merge.", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$output->msg = __( "You do not have permission to perform this action.", "plugnmeet" );
			wp_send_json( $output );
		}

		$params = $this->setting_params;
		try {
			$connect = new plugNmeetConnect( $params );

			$byId = new MergeRecordingsByIds();
			$byId->setRoomId( $roomId );
			$byId->setRecordingIds( $recordings );

			$mergeReq = new MergeRecordingsReq();
			$mergeReq->setByIds( $byId );

			$res            = $connect->mergeRecordings( $mergeReq );
			$output->status = $res->getStatus();
			$output->msg    = $res->getMsg();
		} catch ( Exception $e ) {
			$output->msg = $e->getMessage();
		}

		if ( $output->status ) {
			$output->msg = __( "The recordings are being merged. This may take a few moments.", 'plugnmeet' );
		}

		wp_send_json( $output );
	}

	public function login_to_room() {
		$output         = new stdClass();
		$output->status = false;
		$output->msg    = __( 'Your session has expired. Please refresh the page and try again.', 'plugnmeet' );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'plugnmeet_login_to_room' ) ) {
			wp_send_json( $output );
		}

		$id          = (int) $this->get_post_param( 'id', 0 );
		$name        = $this->get_post_param( 'name' );
		$password    = $this->get_post_param( 'password' );
		$current_url = $this->get_post_param( 'current_url', '', 'urldecode' );
		$current_url = sanitize_url( $current_url );

		if ( empty( $id ) ) {
			$output->msg = __( "We couldn't find the room you're trying to join. Please check the link and try again.", 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( empty( $name ) ) {
			$output->msg = __( 'Please enter your name to join the meeting.', 'plugnmeet' );
			wp_send_json( $output );
		}

		if ( ! class_exists( 'Plugnmeet_RoomPage' ) ) {
			require PLUGNMEET_ROOT_PATH . "/admin/class-plugnmeet-room-page.php";
		}

		$class    = new Plugnmeet_RoomPage();
		$roomInfo = $class->getRoomById( $id );

		if ( ! $roomInfo ) {
			$output->msg = __( 'The room you are trying to join does not exist.', 'plugnmeet' );
			wp_send_json( $output );
		} elseif ( $roomInfo->published !== "1" ) {
			$output->msg = __( 'This room is not currently active. Please contact the administrator.', 'plugnmeet' );
			wp_send_json( $output );
		}

		$roleDetermine = $this->determineUserType( $roomInfo, $password );
		if ( ! $roleDetermine->status ) {
			$output->msg = $roleDetermine->msg;
			wp_send_json( $output );
		}
		$isAdmin = $roleDetermine->isAdmin;

		$logoutUrl = ! empty( $current_url ) ? add_query_arg( 'pnm-returned', 'true', $current_url ) : '';

		$connect       = new plugNmeetConnect( $this->setting_params );
		$isRoomActive  = false;
		$room_metadata = json_decode( $roomInfo->room_metadata, true );

		try {
			$res = $connect->isRoomActive( $roomInfo->room_id );
			if ( ! $res->getStatus() ) {
				$output->msg = $res->getMsg();
				wp_send_json( $output );
			}
			$isRoomActive = $res->getIsActive();
		} catch ( Exception $e ) {
			$output->msg = $e->getMessage();
			wp_send_json( $output );
		}

		if ( ! $isRoomActive
		     && ! $isAdmin
		     && ! empty( $room_metadata["room_features"]["moderator_join_first"] ) ) {
			$output->msg = __( "The meeting has not started yet. Please wait for a moderator to begin the session.", "plugnmeet" );
			wp_send_json( $output );
		}

		if ( ! $isRoomActive ) {
			try {
				global $wp_version;
				$extraData = [
					"platform"       => "wordpress-{$wp_version}",
					"php-version"    => phpversion(),
					"plugin-version" => PLUGNMEET_VERSION,
				];
				if ( ! empty( $this->setting_params->copyright_display ) ) {
					$room_metadata["copyright_conf"] = [
						"display" => filter_var( $this->setting_params->copyright_display, FILTER_VALIDATE_BOOLEAN ),
						"text"    => $this->setting_params->copyright_text,
					];
				}

				$webHookUrl = get_rest_url( null, 'plugnmeet/webhook' );
				$create     = $connect->createRoom( $roomInfo->room_id, $roomInfo->room_title, $room_metadata, $roomInfo->welcome_message, $logoutUrl, $webHookUrl, $roomInfo->max_participants, 0, $extraData );

				if ( ! $create->getStatus() ) {
					$output->msg = $create->getMsg();
					wp_send_json( $output );
				}
				$isRoomActive = true;
			} catch ( Exception $e ) {
				$output->msg = $e->getMessage();
				wp_send_json( $output );
			}
		}

		$userId = get_current_user_id();
		if ( ! $userId ) {
			if ( ! isset( $_SESSION['PLUG_N_MEET_USER_ID'] ) ) {
				$_SESSION['PLUG_N_MEET_USER_ID'] = $connect->getUUID();
			}
			$userId = esc_attr( $_SESSION['PLUG_N_MEET_USER_ID'] );
		}

		if ( $isRoomActive ) {
			try {
				$join = $connect->getJoinToken( $roomInfo->room_id, $name, $userId, $isAdmin );

				if ( ! $join->getStatus() ) {
					$output->msg = $join->getMsg();
					wp_send_json( $output );
				}

				if ( $this->setting_params->client_load === "redirect" ) {
					$output->url = rtrim( $this->setting_params->plugnmeet_server_url, "/" ) . "/?access_token=" . $join->getToken();
				} else {
					$output->url = add_query_arg( [
						'access_token'           => $join->getToken(),
						'id'                     => $id,
						'Plug-N-Meet-Conference' => 1,
					], site_url( '/index.php' ) );
				}

				$output->status = true;
				$output->msg    = __( 'Redirecting you to the meeting...', 'plugnmeet' );

			} catch ( Exception $e ) {
				$output->msg = $e->getMessage();
				wp_send_json( $output );
			}
		}

		wp_send_json( $output );
	}

	private function get_user_role_for_room( $roomId ) {
		global $wpdb;
		$output         = new stdClass();
		$output->status = false;
		$output->role   = null;
		$output->msg    = __( "You do not have permission to join this room.", 'plugnmeet' );

		$roomInfo = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE room_id = %s",
			$roomId
		) );

		if ( ! $roomInfo ) {
			$output->msg = __( "The room you are trying to join does not exist.", 'plugnmeet' );

			return $output;
		} elseif ( $roomInfo->published !== "1" ) {
			$output->msg = __( "This room is not currently active. Please contact the administrator.", 'plugnmeet' );

			return $output;
		}

		if ( ! empty( $roomInfo->roles ) ) {
			$user     = wp_get_current_user();
			$roles    = json_decode( $roomInfo->roles, true );
			$userRole = ( $user->ID ) ? $user->roles[0] : 'guest';

			if ( isset( $roles[ $userRole ] ) ) {
				$output->status = true;
				$output->role   = $roles[ $userRole ];
			}
		}

		return $output;
	}

	private function determineUserType( $roomInfo, $password ) {
		$output          = new stdClass();
		$output->status  = false;
		$output->isAdmin = false;
		$output->msg     = __( "You do not have permission to join this room.", 'plugnmeet' );

		if ( ! empty( $password ) ) {
			if ( $password === $roomInfo->moderator_pass ) {
				$output->status  = true;
				$output->isAdmin = true;
			} elseif ( $password === $roomInfo->attendee_pass ) {
				$output->status  = true;
				$output->isAdmin = false;
			} else {
				$output->msg = __( "The password you entered is incorrect. Please try again.", 'plugnmeet' );
			}

			return $output;
		}

		$roleInfo = $this->get_user_role_for_room( $roomInfo->room_id );
		if ( ! $roleInfo->status || ! $roleInfo->role ) {
			return $output; // No role found, permission denied
		}

		$role = $roleInfo->role;
		if ( ! empty( $role['require_password'] ) ) {
			$output->msg = __( 'A password is required to join this room.', 'plugnmeet' );

			return $output; // Password is required but not provided
		}

		$output->status  = true;
		$output->isAdmin = ( $role['join_as'] === "moderator" );

		return $output;
	}

	private function canAccess( $roomId, $checkFor ) {
		$roleInfo       = $this->get_user_role_for_room( $roomId );
		$output         = new stdClass();
		$output->status = $roleInfo->status && ! empty( $roleInfo->role[ $checkFor ] );
		$output->msg    = $roleInfo->msg;

		if ( current_user_can( 'manage_options' ) ) {
			$output->status = true;
		}

		return $output;
	}

	private function format_type_name( $type ) {
		$name = RoomArtifactType::name( $type );

		return ucwords( strtolower( str_replace( '_', ' ', $name ) ) );
	}
}
