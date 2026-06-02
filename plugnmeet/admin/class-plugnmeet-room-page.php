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

    public function artifactsPage()
    {
        $artifact_id = isset($_GET['artifact_id']) ? sanitize_text_field($_GET['artifact_id']) : '';

        if (!empty($artifact_id)) {
            $this->render_single_artifact($artifact_id);
        } else {
            global $wpdb;

            $rooms = $wpdb->get_results($wpdb->prepare(
                "SELECT id, room_id, room_title FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE published = %s ORDER BY `room_title` ASC",
                array(1)
            ));

            require plugin_dir_path(dirname(__FILE__)) . 'admin/partials/plugnmeet-admin-display-artifacts.php';
        }
    }

    private function render_single_artifact($artifact_id)
    {
        if (!class_exists("plugNmeetConnect")) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'helpers/plugNmeetConnect.php';
        }

        $options = (object) get_option("plugnmeet_settings");
        $pnc = new plugNmeetConnect($options);
        $res = $pnc->getArtifactInfo($artifact_id);

        if (!$res->getStatus()) {
            echo '<div class="notice notice-error"><p>' . $res->getMsg() . '</p></div>';
            return;
        }

        $info = $res->getArtifactInfo();
        $metadata = $info->getMetadata();
        $is_file_based = ($metadata && $metadata->hasFileInfo());

        $context = [
            'is_analytics' => $info->getType() === \Mynaparrot\PlugnmeetProto\RoomArtifactType::MEETING_ANALYTICS,
            'is_meeting_summary' => $info->getType() === \Mynaparrot\PlugnmeetProto\RoomArtifactType::MEETING_SUMMARY,
            'meeting_summary_content' => '',
            'details' => [],
        ];

        $details = [
            __('Artifact ID', 'plugnmeet') => $info->getArtifactId(),
            __('Room ID', 'plugnmeet') => $info->getRoomId(),
            __('Type', 'plugnmeet') => $this->format_type_name($info->getType()),
            __('Created At', 'plugnmeet') => gmdate('Y-m-d H:i:s', strtotime($info->getCreated())),
        ];

        if ($metadata) {
            if ($metadata->hasFileInfo()) {
                $fileinfo = $metadata->getFileInfo();
                $details[__('File Size', 'plugnmeet')] = round($fileinfo->getFileSize() / 1024 / 1024, 2) . ' MB';
                $details[__('MIME Type', 'plugnmeet')] = $fileinfo->getMimeType();
            }
            if ($metadata->hasTokenUsage()) {
                $usage = $metadata->getTokenUsage();
                $details[__('Token Usage', 'plugnmeet')] = $usage->getTotalTokens();
                $details[__('Estimated Cost', 'plugnmeet')] = number_format($usage->getTotalTokensEstimatedCost(), 4);
            }
            if ($metadata->hasDurationUsage()) {
                $usage = $metadata->getDurationUsage();
                $details[__('Duration Usage', 'plugnmeet')] = $usage->getDurationSec() . 's';
                $details[__('Estimated Cost', 'plugnmeet')] = number_format($usage->getDurationSecEstimatedCost(), 4);
            }
            if ($metadata->hasCharacterCountUsage()) {
                $usage = $metadata->getCharacterCountUsage();
                $details[__('Character Count Usage', 'plugnmeet')] = $usage->getTotalCharacters();
                $details[__('Estimated Cost', 'plugnmeet')] = number_format($usage->getTotalCharactersEstimatedCost(), 4);
            }
        }

        foreach ($details as $label => $value) {
            $context['details'][] = ['label' => $label, 'value' => $value];
        }

        if ($context['is_analytics']) {
            if (!class_exists("Plugnmeet_AnalyticsHelper")) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'helpers/analyticsHelper.php';
            }
            try {
                $analyticshelper = new Plugnmeet_AnalyticsHelper($artifact_id);
                $context = array_merge($context, $analyticshelper->get_context_data());
            } catch (\Exception $e) {
                $context['is_analytics'] = false; // Prevent rendering analytics section on error.
                echo '<div class="notice notice-error"><p>' . $e->getMessage() . '</p></div>';
            }
        } else if ($context['is_meeting_summary']) {
            $this->populate_meeting_summary_context($artifact_id, $context);
        }
        
        $artifact_info = $info;

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/plugnmeet-admin-display-artifact-details.php';
    }

    private function populate_meeting_summary_context($artifact_id, &$context)
    {
        if (!class_exists("plugNmeetConnect")) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'helpers/plugNmeetConnect.php';
        }
        
        $options = (object) get_option("plugnmeet_settings");
        $pnc = new plugNmeetConnect($options);
        $res = $pnc->getArtifactDownloadToken($artifact_id);

        if ($res->getStatus()) {
            $server_url = rtrim($options->plugnmeet_server_url, '/');
            $host = $server_url . "/download/artifact/" . $res->getToken();
            $response = wp_remote_get($host, array('timeout' => 60));

            if (is_wp_error($response)) {
                echo '<div class="notice notice-error"><p>' . $response->get_error_message() . '</p></div>';
            } else {
                $context['meeting_summary_content'] = wp_remote_retrieve_body($response);
            }
        } else {
            echo '<div class="notice notice-error"><p>' . $res->getMsg() . '</p></div>';
        }
    }

    private function format_type_name($type)
    {
        $name = \Mynaparrot\PlugnmeetProto\RoomArtifactType::name($type);
        return ucwords(strtolower(str_replace('_', ' ', $name)));
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
                "SELECT * FROM " . $wpdb->prefix . "plugnmeet_rooms ORDER BY `id` DESC LIMIT %d, %d", array(
                        $from,
                        $limit
                )
        ) );
    }

    private function getTotalNumRooms() {
        global $wpdb;

        return $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "plugnmeet_rooms" );
    }
}
