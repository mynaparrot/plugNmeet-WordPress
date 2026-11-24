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
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-plugnmeet-rooms-list-table.php';

            $rooms_list_table = new Plugnmeet_Rooms_List_Table();
            $rooms_list_table->prepare_items();
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
                // output setting sections as collapsible cards
                $this->renderSettingsCards( 'plugnmeet-settings' );
                // submit button
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

	/**
	 * Renders registered settings sections as collapsible pnm-cards.
	 *
	 * Mirrors core do_settings_sections()/do_settings_fields() but wraps each
	 * section in a card. Sections start expanded unless listed in $collapsed_sections.
	 *
	 * @param string $page Settings page slug.
	 */
	public function renderSettingsCards( $page ) {
		if ( ! isset( $GLOBALS['wp_settings_sections'][ $page ] ) ) {
			return;
		}

		$subtitles = array(
			'plugnmeet_settings_config_section'        => __( 'Connection details for your plugNmeet server and client delivery.', 'plugnmeet' ),
			'plugnmeet_settings_options_section'       => __( 'Default technical configurations applied to every new session.', 'plugnmeet' ),
			'plugnmeet_settings_frontend_design_section' => __( 'Styling for the room page on your site.', 'plugnmeet' ),
			'plugnmeet_settings_client_design_section'  => __( 'Global defaults for the meeting client. Rooms can override the design and logo from their Design tab; copyright is always global.', 'plugnmeet' ),
			'plugnmeet_settings_ai_agent_section'       => __( 'Global limits and defaults for AI agents in web conferences and 1-on-1 autonomous sessions.', 'plugnmeet' ),
			'plugnmeet_settings_ai_byok_section'        => __( 'Optional custom API credentials and model overrides for AI agent sessions.', 'plugnmeet' ),
			'plugnmeet_settings_ai_mcp_section'         => __( 'Optional Model Context Protocol (MCP) tool servers available to AI agents and AI text chat.', 'plugnmeet' ),
		);

		// Advanced sections start collapsed to keep the page compact.
		$collapsed_sections = array(
			'plugnmeet_settings_ai_byok_section',
			'plugnmeet_settings_ai_mcp_section',
		);

		foreach ( (array) $GLOBALS['wp_settings_sections'][ $page ] as $section ) {
			if ( ! isset( $section['id'] ) ) {
				continue;
			}
			$section_id = $section['id'];
			$collapsed  = in_array( $section_id, $collapsed_sections, true );

			echo '<div class="pnm-card pnm-collapsible" data-collapsed="' . ( $collapsed ? 'true' : 'false' ) . '">';
			echo '<div class="pnm-card-header">';
			echo '<button type="button" class="pnm-card-toggle" aria-expanded="' . ( $collapsed ? 'false' : 'true' ) . '">';
			echo '<span class="pnm-card-title">' . esc_html( $section['title'] ) . '</span>';
			echo '<span class="pnm-card-subtitle">' . esc_html( isset( $subtitles[ $section_id ] ) ? $subtitles[ $section_id ] : '' ) . '</span>';
			echo '<span class="pnm-toggle-indicator" aria-hidden="true"></span>';
			echo '</button>';
			echo '</div>';
			echo '<div class="pnm-card-body">';

			if ( isset( $section['callback'] ) && is_callable( $section['callback'] ) ) {
				call_user_func( $section['callback'], $section );
			}

			if ( isset( $GLOBALS['wp_settings_fields'][ $page ][ $section_id ] ) ) {
				echo '<table class="form-table" role="presentation">';
				foreach ( (array) $GLOBALS['wp_settings_fields'][ $page ][ $section_id ] as $field ) {
					if ( ! isset( $field['id'], $field['callback'] ) ) {
						continue;
					}
					echo '<tr>';
					echo '<th scope="row">';
					if ( isset( $field['args']['label_for'] ) ) {
						echo '<label for="' . esc_attr( $field['args']['label_for'] ) . '">' . esc_html( $field['title'] ) . '</label>';
					} else {
						echo esc_html( $field['title'] );
					}
					echo '</th>';
					echo '<td>';
					if ( is_callable( $field['callback'] ) ) {
						call_user_func( $field['callback'], $field['args'] );
					}
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}

			echo '</div>';
			echo '</div>';
		}
	}

    public function recordingsPage() {
        global $wpdb;

        $rooms = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, room_id, room_title FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE published = %s ORDER BY `room_title` ASC", array( 1 ) ) );

        require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/plugnmeet-admin-display-recordings.php';
    }

    public function artifactsPage() {
        $artifact_id = isset( $_GET['artifact_id'] ) ? sanitize_text_field( $_GET['artifact_id'] ) : '';

        if ( ! empty( $artifact_id ) ) {
            $this->render_single_artifact( $artifact_id );
        } else {
            global $wpdb;

            $rooms = $wpdb->get_results( $wpdb->prepare(
                    "SELECT id, room_id, room_title FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE published = %s ORDER BY `room_title` ASC",
                    array( 1 )
            ) );

            require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/plugnmeet-admin-display-artifacts.php';
        }
    }

    private function render_single_artifact( $artifact_id ) {
        if ( ! class_exists( "plugNmeetConnect" ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
        }

        $options = (object) get_option( "plugnmeet_settings" );
        $pnc     = new plugNmeetConnect( $options );
        $res     = $pnc->getArtifactInfo( $artifact_id );

        if ( ! $res->getStatus() ) {
            echo '<div class="notice notice-error"><p>' . $res->getMsg() . '</p></div>';

            return;
        }

        $info          = $res->getArtifactInfo();
        $metadata      = $info->getMetadata();
        $is_file_based = ( $metadata && $metadata->hasFileInfo() );

        $context = [
                'is_analytics'            => $info->getType() === \Mynaparrot\PlugnmeetProto\RoomArtifactType::MEETING_ANALYTICS,
                'is_meeting_summary'      => $info->getType() === \Mynaparrot\PlugnmeetProto\RoomArtifactType::MEETING_SUMMARY,
                'is_ai_session_summary'   => $info->getType() === \Mynaparrot\PlugnmeetProto\RoomArtifactType::AI_AGENT_SESSION_SUMMARY,
                'meeting_summary_content' => '',
                'ai_summary'              => [],
                'details'                 => [],
        ];

        $details = [
                __( 'Artifact ID', 'plugnmeet' ) => $info->getArtifactId(),
                __( 'Room ID', 'plugnmeet' )     => $info->getRoomId(),
                __( 'Type', 'plugnmeet' )        => $this->format_type_name( $info->getType() ),
                __( 'Created At', 'plugnmeet' )  => gmdate( 'Y-m-d H:i:s', strtotime( $info->getCreated() ) ),
        ];

        if ( $metadata ) {
            if ( $metadata->hasFileInfo() ) {
                $fileinfo                                  = $metadata->getFileInfo();
                $details[ __( 'File Size', 'plugnmeet' ) ] = round( $fileinfo->getFileSize() / 1024, 2 ) . ' KB';
                $details[ __( 'MIME Type', 'plugnmeet' ) ] = $fileinfo->getMimeType();
            }
            if ( $metadata->hasTokenUsage() ) {
                $usage                                          = $metadata->getTokenUsage();
                $details[ __( 'Token Usage', 'plugnmeet' ) ]    = $usage->getTotalTokens();
                $details[ __( 'Estimated Cost', 'plugnmeet' ) ] = number_format( $usage->getTotalTokensEstimatedCost(), 4 );
            }
            if ( $metadata->hasDurationUsage() ) {
                $usage                                          = $metadata->getDurationUsage();
                $details[ __( 'Duration Usage', 'plugnmeet' ) ] = $usage->getDurationSec() . 's';
                $details[ __( 'Estimated Cost', 'plugnmeet' ) ] = number_format( $usage->getDurationSecEstimatedCost(), 4 );
            }
            if ( $metadata->hasCharacterCountUsage() ) {
                $usage                                                 = $metadata->getCharacterCountUsage();
                $details[ __( 'Character Count Usage', 'plugnmeet' ) ] = $usage->getTotalCharacters();
                $details[ __( 'Estimated Cost', 'plugnmeet' ) ]        = number_format( $usage->getTotalCharactersEstimatedCost(), 4 );
            }
        }

        foreach ( $details as $label => $value ) {
            $context['details'][] = [ 'label' => $label, 'value' => $value ];
        }

        if ( $context['is_analytics'] ) {
            if ( ! class_exists( "Plugnmeet_AnalyticsHelper" ) ) {
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/analyticsHelper.php';
            }
            try {
                $analyticshelper = new Plugnmeet_AnalyticsHelper( $artifact_id );
                $context         = array_merge( $context, $analyticshelper->get_context_data() );
            } catch ( \Exception $e ) {
                $context['is_analytics'] = false; // Prevent rendering analytics section on error.
                echo '<div class="notice notice-error"><p>' . esc_html( $e->getMessage() ) . '</p></div>';
            }
        } else if ( $context['is_meeting_summary'] ) {
            $this->populate_meeting_summary_context( $artifact_id, $context );
        } else if ( $context['is_ai_session_summary'] ) {
            $this->populate_ai_session_summary_context( $artifact_id, $context );
        }

        $artifact_info = $info;

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/plugnmeet-admin-display-artifact-details.php';
    }

    private function populate_meeting_summary_context( $artifact_id, &$context ) {
        if ( ! class_exists( "plugNmeetConnect" ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
        }

        $options = (object) get_option( "plugnmeet_settings" );
        $pnc     = new plugNmeetConnect( $options );
        $res     = $pnc->getArtifactDownloadToken( $artifact_id );

        if ( $res->getStatus() ) {
            $server_url = rtrim( trim( (string) $options->plugnmeet_server_url ), '/' );
            $host       = $server_url . "/download/artifact/" . $res->getToken();
            $response   = wp_remote_get( $host, array( 'timeout' => 60 ) );

            if ( is_wp_error( $response ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html( $response->get_error_message() ) . '</p></div>';
            } else {
                $summarycontent = wp_remote_retrieve_body( $response );
                // Remove the header "Meeting Summary for: [UUID]\n---\n" using regex.
                $cleancontent                       = preg_replace(
                        '~^Meeting Summary for:.*\R+\R+---\R+\R+~s',
                        '',
                        $summarycontent
                );
                $cleancontent                       = str_replace( "h3>", "h6>", $cleancontent );
                $context['meeting_summary_content'] = $cleancontent;
            }
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html( $res->getMsg() ) . '</p></div>';
        }
    }

    private function populate_ai_session_summary_context( $artifact_id, &$context ) {
        if ( ! class_exists( "plugNmeetConnect" ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
        }

        $options = (object) get_option( "plugnmeet_settings" );
        $pnc     = new plugNmeetConnect( $options );
        $res     = $pnc->getArtifactDownloadToken( $artifact_id );

        if ( ! $res->getStatus() ) {
            echo '<div class="notice notice-error"><p>' . esc_html( $res->getMsg() ) . '</p></div>';

            return;
        }

        $server_url = rtrim( trim( (string) $options->plugnmeet_server_url ), '/' );
        $host       = $server_url . "/download/artifact/" . $res->getToken();
        $response   = wp_remote_get( $host, array( 'timeout' => 60 ) );

        if ( is_wp_error( $response ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html( $response->get_error_message() ) . '</p></div>';

            return;
        }

        $summary = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $summary ) || ! is_array( $summary ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'The AI session summary could not be read.', 'plugnmeet' ) . '</p></div>';

            return;
        }

        $user_info        = (array) ( $summary['user_info'] ?? [] );
        $session          = (array) ( $summary['session'] ?? [] );
        $evaluation       = (array) ( $summary['evaluation'] ?? [] );
        $quiz             = (array) ( $summary['quiz_evaluation'] ?? [] );
        $workspace        = (array) ( $summary['workspace_activity'] ?? [] );
        $continuation     = (array) ( $summary['continuation'] ?? [] );
        $ex_user_id       = (string) ( $user_info['ex_user_id'] ?? '' );
        $mastery_level    = (string) ( $evaluation['mastery_level'] ?? '' );
        $duration_seconds = (int) ( $session['duration_seconds'] ?? 0 );

        $student_name = (string) ( $user_info['name'] ?? '' );
        if ( ctype_digit( $ex_user_id ) ) {
            $user = get_userdata( (int) $ex_user_id );
            if ( $user ) {
                $student_name = $user->display_name . ' (' . $user->user_email . ')';
            }
        }
        if ( '' === $student_name ) {
            $student_name = '' !== $ex_user_id ? $ex_user_id : (string) ( $user_info['user_id'] ?? '' );
        }

        $questions = [];
        foreach ( (array) ( $quiz['questions'] ?? [] ) as $question ) {
            $questions[] = [
                    'question'       => (string) ( $question['question'] ?? '' ),
                    'correct_option' => (string) ( $question['correct_option'] ?? '' ),
                    'total_votes'    => (int) ( $question['total_votes'] ?? 0 ),
                    'correct_votes'  => (int) ( $question['correct_votes'] ?? 0 ),
                    'passed'         => ! empty( $question['passed'] ),
            ];
        }

        $progress_log = [];
        foreach ( (array) ( $summary['progress_log'] ?? [] ) as $entry ) {
            $progress_log[] = [
                    'concept' => (string) ( $entry['concept'] ?? '' ),
                    'status'  => (string) ( $entry['status'] ?? '' ),
                    'notes'   => (string) ( $entry['notes'] ?? '' ),
            ];
        }

        $tools_used = [];
        foreach ( (array) ( $workspace['tools_used'] ?? [] ) as $tool => $count ) {
            $tools_used[] = [
                    'name'  => (string) $tool,
                    'count' => (int) $count,
            ];
        }

        $total_tokens   = 0;
        $estimated_cost = 0;
        foreach ( (array) ( $summary['usage'] ?? [] ) as $usage ) {
            $total_tokens   += (int) ( $usage['total_tokens'] ?? 0 );
            $estimated_cost += (float) ( $usage['total_tokens_estimated_cost'] ?? 0 );
        }

        $unfinished_subtopics = array_values( (array) ( $continuation['unfinished_subtopics'] ?? [] ) );
        $whiteboard_used      = ! empty( $workspace['whiteboard_used'] );
        $notepad_used         = ! empty( $workspace['notepad_used'] );

        $context['ai_summary'] = [
                'student_name'        => $student_name,
                'ex_user_id'          => '' !== $ex_user_id ? $ex_user_id : (string) ( $user_info['user_id'] ?? '' ),
                'agent_name'          => (string) ( $session['agent_display_name'] ?? '' ),
                'topic'               => (string) ( $session['topic'] ?? '' ),
                'started_at'          => $this->format_artifact_time( $session['started_at'] ?? 0 ),
                'ended_at'            => $this->format_artifact_time( $session['ended_at'] ?? 0 ),
                'duration_minutes'    => (int) round( $duration_seconds / 60 ),
                'turn_count'          => (int) ( $session['turns_count'] ?? 0 ),
                'end_reason'          => (string) ( $session['end_reason'] ?? '' ),
                'off_topic_redirects' => (int) ( $session['off_topic_redirects_count'] ?? 0 ),

                'overall_score'       => max( 0, min( 100, (int) ( $evaluation['overall_score'] ?? 0 ) ) ),
                'mastery_level'       => ucfirst( $mastery_level ),
                'mastery_badge_class' => $this->mastery_badge_class( $mastery_level ),
                'participation_level' => ucfirst( (string) ( $evaluation['participation_level'] ?? '' ) ),
                'narrative_summary'   => (string) ( $summary['narrative_summary'] ?? '' ),
                'rubric_notes'        => (string) ( $evaluation['rubric_notes'] ?? '' ),
                'concepts_mastered'   => array_values( (array) ( $evaluation['concepts_mastered'] ?? [] ) ),
                'concepts_practiced'  => array_values( (array) ( $evaluation['concepts_practiced'] ?? [] ) ),
                'struggled_with'      => array_values( (array) ( $evaluation['struggled_with'] ?? [] ) ),
                'vocabulary'          => array_values( (array) ( $evaluation['vocabulary_or_key_terms'] ?? [] ) ),

                'has_quiz'            => (int) ( $quiz['total_quizzes'] ?? 0 ) > 0,
                'quiz_total'          => (int) ( $quiz['total_quizzes'] ?? 0 ),
                'quiz_correct'        => (int) ( $quiz['correct_responses'] ?? 0 ),
                'quiz_accuracy'       => (int) ( $quiz['accuracy_percent'] ?? 0 ),
                'quiz_questions'      => $questions,

                'has_progress_log'    => ! empty( $progress_log ),
                'progress_log'        => $progress_log,
                'has_workspace'       => ! empty( $tools_used ) || $whiteboard_used || $notepad_used,
                'whiteboard_used'     => $whiteboard_used,
                'notepad_used'        => $notepad_used,
                'tools_used'          => $tools_used,

                'has_continuation'                => '' !== (string) ( $continuation['next_session_focus'] ?? '' )
                        || '' !== (string) ( $continuation['recommended_warm_up'] ?? '' )
                        || ! empty( $unfinished_subtopics ),
                'next_session_focus'              => (string) ( $continuation['next_session_focus'] ?? '' ),
                'recommended_warm_up'             => (string) ( $continuation['recommended_warm_up'] ?? '' ),
                'unfinished_subtopics'            => $unfinished_subtopics,
                'previous_session_context_prompt' => (string) ( $continuation['previous_session_context_prompt'] ?? '' ),

                'has_usage'            => $total_tokens > 0,
                'total_tokens'         => $total_tokens,
                'estimated_cost'       => number_format( $estimated_cost, 4 ),
        ];
    }

    private function format_artifact_time( $milliseconds ) {
        $milliseconds = (int) $milliseconds;
        if ( $milliseconds <= 0 ) {
            return '';
        }

        return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) floor( $milliseconds / 1000 ) );
    }

    private function mastery_badge_class( $level ) {
        switch ( strtolower( trim( (string) $level ) ) ) {
            case 'advanced':
                return 'pnm-badge-success';
            case 'proficient':
                return 'pnm-badge-info';
            case 'developing':
                return 'pnm-badge-warning';
        }

        return 'pnm-badge-neutral';
    }

    private function format_type_name( $type ) {
        if ( \Mynaparrot\PlugnmeetProto\RoomArtifactType::AI_AGENT_SESSION_SUMMARY === $type ) {
            return __( 'AI Session Summary', 'plugnmeet' );
        }
        if ( \Mynaparrot\PlugnmeetProto\RoomArtifactType::AI_AGENT_USAGE === $type ) {
            return __( 'AI Agent Usage', 'plugnmeet' );
        }
        if ( \Mynaparrot\PlugnmeetProto\RoomArtifactType::AI_AGENT_TRANSCRIPT === $type ) {
            return __( 'AI Session Transcript', 'plugnmeet' );
        }

        $name = \Mynaparrot\PlugnmeetProto\RoomArtifactType::name( $type );

        return ucwords( strtolower( str_replace( '_', ' ', $name ) ) );
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

        $room_metadata = isset( $data->room_metadata ) ? (array) json_decode( $data->room_metadata, true ) : [];
        foreach ( PlugnmeetHelper::$roomMetadataItems as $item ) {
            $fields_values[ $item ] = isset( $room_metadata[ $item ] ) ? $room_metadata[ $item ] : [];
        }
        $fields_values['room_mode'] = PlugnmeetHelper::getRoomMode( $room_metadata );

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
}
