<?php
/**
 *
 * @since      1.0.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

class Plugnmeet_RoomPage {
	private $limitPerPage = 20;

	public function roomsPage() {
		// check if user is allowed access
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_GET['task'] ) ) {
			if ( $_GET['task'] === "add" || $_GET['task'] === "edit" ) {
				$fields_values = $this->getFormData();
				require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/plugnmeet-admin-edit-room.php';
			}
		} else {
			$limit         = $this->limitPerPage;
			$rooms         = $this->getRooms( sanitize_text_field( $limit ) );
			$totalNumRooms = $this->getTotalNumRooms();
			require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/plugnmeet-admin-display-rooms.php';
		}
	}

	public function settingsPage() {
		// check if user is allowed access
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>

        <div class="wrap">
            <form action="options.php" method="post">
				<?php
				// output security fields
				settings_fields( 'plugnmeet_settings' );
				// output setting sections
				do_settings_sections( 'plugnmeet-settings' );
				// submit button
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	public function recordingsPage() {
		global $wpdb;

		$rooms = $wpdb->get_results( $wpdb->prepare(
			"SELECT id, room_id, room_title FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE published = %s ORDER BY `room_title` ASC", array( 1 ) ) );

		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/plugnmeet-admin-display-recordings.php';
	}

	private function getFormData() {
		$data = new stdClass();
		if ( isset( $_GET['id'] ) ) {
			$result = $this->getRoomById( sanitize_text_field( $_GET['id'] ) );
			if ( $result ) {
				$data = $result;
			}
		}
		$fields_values = array(
			'id'               => isset( $data->id ) ? $data->id : 0,
			'room_id'          => isset( $data->room_id ) ? $data->room_id : "",
			'room_title'       => isset( $data->room_title ) ? $data->room_title : "",
			'description'      => isset( $data->description ) ? $data->description : "",
			'moderator_pass'   => isset( $data->moderator_pass ) ? $data->moderator_pass : "",
			'attendee_pass'    => isset( $data->attendee_pass ) ? $data->attendee_pass : "",
			'welcome_message'  => isset( $data->welcome_message ) ? $data->welcome_message : "",
			'max_participants' => isset( $data->max_participants ) ? $data->max_participants : 0,
			'roles'            => isset( $data->roles ) && $data->roles !== "" ? json_decode( $data->roles, true ) : array(),
			'published'        => isset( $data->published ) ? $data->published : 1,
		);

		if ( ! class_exists( "PlugnmeetHelper" ) ) {
			require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/helper.php';
		}

		if ( empty( $fields_values['moderator_pass'] ) ) {
			$fields_values['moderator_pass'] = PlugnmeetHelper::secureRandomKey( 10 );
		}
		if ( empty( $fields_values['attendee_pass'] ) ) {
			$fields_values['attendee_pass'] = PlugnmeetHelper::secureRandomKey( 10 );
		}

		if ( isset( $data->room_metadata ) ) {
			$room_metadata = json_decode( $data->room_metadata, true );
			foreach ( PlugnmeetHelper::$roomMetadataItems as $item ) {
				if ( isset( $room_metadata[ $item ] ) ) {
					$fields_values[ $item ] = $room_metadata[ $item ];
				} else {
					$fields_values[ $item ] = [];
				}
			}
		} else {
			foreach ( PlugnmeetHelper::$roomMetadataItems as $item ) {
				$fields_values[ $item ] = [];
			}
		}

		return $fields_values;
	}

	public function getRoomById( $id ) {
		global $wpdb;
		if ( ! $id ) {
			return null;
		}

		return $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE id = %d",
			$id
		) );
	}

	private function getRooms( $limit ) {
		global $wpdb;
		$from  = 0;
		$paged = isset( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : 0;
		if ( $paged > 1 ) {
			$from = ( $paged - 1 ) * $limit;
		}

		$search_term = isset( $_GET['search_term'] ) ? sanitize_text_field( $_GET['search_term'] ) : "";

		if ( ! empty( $search_term ) ) {
			return $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE room_title LIKE %s ORDER BY `id` DESC LIMIT %d, %d", array(
					'%' . $wpdb->esc_like( $search_term ) . '%',
					$from,
					$limit
				)
			) );
		}

		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM " . $wpdb->prefix . "plugnmeet_rooms ORDER BY `id` DESC LIMIT %d, %d", array( $from, $limit )
		) );
	}

	private function getTotalNumRooms() {
		global $wpdb;

		return $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "plugnmeet_rooms" );
	}
}
