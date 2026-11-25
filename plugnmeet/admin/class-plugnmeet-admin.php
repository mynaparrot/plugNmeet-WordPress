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
        add_action( 'admin_notices', array( $this, 'display_plugnmeet_notices' ) );
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
            wp_enqueue_style( 'wp-color-picker' );
            $css_ver = (string) @filemtime( plugin_dir_path( __FILE__ ) . 'css/plugnmeet-admin.css' );
            if ( false === $css_ver || '' === $css_ver ) {
                $css_ver = $this->version;
            }
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugnmeet-admin.css', array(), $css_ver );

            if ( $hook_suffix === 'plug-n-meet_page_plugnmeet-recordings' ) {
                wp_enqueue_style( $this->plugin_name . '-recordings', plugin_dir_url( __FILE__ ) . 'css/plugnmeet-admin-recordings.css', array(), $this->version );
            }

            if ( $hook_suffix === 'plug-n-meet_page_plugnmeet-artifacts' ) {
                $artifacts_css_ver = (string) @filemtime( plugin_dir_path( __FILE__ ) . 'css/plugnmeet-admin-artifacts.css' );
                if ( '' === $artifacts_css_ver ) {
                    $artifacts_css_ver = $this->version;
                }
                wp_enqueue_style( $this->plugin_name . '-artifacts', plugin_dir_url( __FILE__ ) . 'css/plugnmeet-admin-artifacts.css', array(), $artifacts_css_ver );
            }
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
            wp_enqueue_script( 'wp-color-picker' );
            $js_ver = (string) @filemtime( plugin_dir_path( __FILE__ ) . 'js/plugnmeet-admin.js' );
            if ( false === $js_ver || '' === $js_ver ) {
                $js_ver = $this->version;
            }
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugnmeet-admin.js', array( 'jquery' ), $js_ver );

            if ( $hook_suffix === 'plug-n-meet_page_plugnmeet-recordings' ) {
                wp_enqueue_script( $this->plugin_name . '-recordings', plugin_dir_url( __FILE__ ) . 'js/plugnmeet-admin-recordings.js', array( 'jquery' ), $this->version );
                wp_localize_script( $this->plugin_name . '-recordings', 'plugnmeet_recordings_data', array(
                        'nonce' => array(
                                'get_recordings'     => wp_create_nonce( 'plugnmeet_get_recordings' ),
                                'download_recording' => wp_create_nonce( 'plugnmeet_download_recording' ),
                                'delete_recording'   => wp_create_nonce( 'plugnmeet_delete_recording' ),
                                'merge_recordings'   => wp_create_nonce( 'plugnmeet_merge_recordings' ),
                        ),
                        'i18n'  => array(
                                'confirm_delete'   => __( 'Are you sure to delete?', 'plugnmeet' ),
                                'download'         => __( 'Download', 'plugnmeet' ),
                                'delete'           => __( 'Delete', 'plugnmeet' ),
                                'total_recordings' => __( 'Total recordings', 'plugnmeet' ),
                                'page'             => __( 'Page', 'plugnmeet' ),
                        ),
                ) );
            }

            if ( $hook_suffix === 'plug-n-meet_page_plugnmeet-artifacts' ) {
                $artifacts_js_ver = (string) @filemtime( plugin_dir_path( __FILE__ ) . 'js/plugnmeet-admin-artifacts.js' );
                if ( '' === $artifacts_js_ver ) {
                    $artifacts_js_ver = $this->version;
                }
                wp_enqueue_script( $this->plugin_name . '-artifacts', plugin_dir_url( __FILE__ ) . 'js/plugnmeet-admin-artifacts.js', array( 'jquery' ), $artifacts_js_ver );
                wp_localize_script( $this->plugin_name . '-artifacts', 'plugnmeet_artifacts_data', array(
                        'nonce' => array(
                                'get_artifacts'       => wp_create_nonce( 'plugnmeet_get_artifacts' ),
                                'download_artifact'   => wp_create_nonce( 'plugnmeet_download_artifact' ),
                                'download_analytics'  => wp_create_nonce( 'plugnmeet_download_analytics' ),
                                'download_ai_summary' => wp_create_nonce( 'plugnmeet_download_ai_summary' ),
                                'delete_artifact'     => wp_create_nonce( 'plugnmeet_delete_artifact' ),
                        ),
                        'i18n'  => array(
                                'view'            => __( 'View', 'plugnmeet' ),
                                'confirm_delete'  => __( 'Are you sure you want to delete this artifact?', 'plugnmeet' ),
                                'total_artifacts' => __( 'Total artifacts', 'plugnmeet' ),
                                'page'            => __( 'Page', 'plugnmeet' ),
                        ),
                ) );
            }
        }

        $ai_presets = array();
        if ( preg_match( "/plugnmeet/", $hook_suffix ) ) {
            if ( ! class_exists( 'PlugnmeetHelper' ) ) {
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/helper.php';
            }
            foreach ( PlugnmeetHelper::getAiAgentPresets() as $preset_key => $preset ) {
                $ai_presets[ $preset_key ] = $preset['prompt'];
            }
        }

        $nonce  = wp_create_nonce( 'ajax_admin' );
        $script = array(
                'nonce'              => $nonce,
                'nonce_search_users' => wp_create_nonce( 'plugnmeet_search_users' ),
                'ai_presets'         => $ai_presets,
                'i18n'               => array(
                        'confirm_delete'  => __( 'Are you sure to delete?', 'plugnmeet' ),
                        'insights_tab'    => __( 'Insights AI', 'plugnmeet' ),
                        'ai_agent_tab'    => __( 'AI Agent', 'plugnmeet' ),
                        'replace_prompt'  => __( 'Replace the current agent instructions with the selected preset?', 'plugnmeet' ),
                        'max_users'       => __( 'You can select up to 32 users.', 'plugnmeet' ),
                        'searching'       => __( 'Searching…', 'plugnmeet' ),
                        'no_users'        => __( 'No users found.', 'plugnmeet' ),
                        'remove_user'     => __( 'Remove user', 'plugnmeet' ),
                ),
        );
        wp_localize_script( $this->plugin_name, 'ajax_admin', $script );
    }

    public function display_plugnmeet_notices() {
        if ( $notice = get_transient( 'plugnmeet_admin_notice' ) ) {
            ?>
            <div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
                <p><?php echo $notice['message']; ?></p>
            </div>
            <?php
            delete_transient( 'plugnmeet_admin_notice' );
        }
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
                __( 'Manage artifacts', 'plugnmeet' ),
                __( 'Artifacts', 'plugnmeet' ),
                'manage_options',
                'plugnmeet-artifacts',
                [ $menusPage, 'artifactsPage' ],
                3
        );

        add_submenu_page(
                'plugnmeet',
                __( 'Settings', 'plugnmeet' ),
                __( 'Settings', 'plugnmeet' ),
                'manage_options',
                'plugnmeet-settings',
                [ $menusPage, 'settingsPage' ],
                4
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
            $output->type   = 'success';
        } else {
            $output->msg  = __( "Unzip failed", "plugnmeet" );
            $output->type = 'error';
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

    private function sanitize_value( $value, ?callable $sanitizer = null ) {
        $value = wp_unslash( $value );

        if ( $sanitizer ) {
            return call_user_func( $sanitizer, $value );
        }

        return sanitize_text_field( $value );
    }

    private function get_post_param( string $key, $default = '', ?callable $sanitizer = null ) {
        if ( ! isset( $_POST[ $key ] ) ) {
            return $default;
        }

        return $this->sanitize_value( $_POST[ $key ], $sanitizer );
    }

    public function save_room_data() {
        global $wpdb;
        $output         = new stdClass();
        $output->status = false;
        $output->url    = '';

        if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'save_room_data' ) ) {
            set_transient( 'plugnmeet_admin_notice', array(
                    'type'    => 'error',
                    'message' => __( 'Token mismatched', 'plugnmeet' ),
            ), 5 * MINUTE_IN_SECONDS );
            wp_send_json( $output );
        }

        if ( ! class_exists( "PlugnmeetHelper" ) ) {
            require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/helper.php';
        }

        $id               = (int) $this->get_post_param( 'id', 0 );
        $room_title       = $this->get_post_param( 'room_title' );
        $description      = $this->get_post_param( 'description', '', 'wp_kses_post' );
        $moderator_pass   = $this->get_post_param( 'moderator_pass' );
        $attendee_pass    = $this->get_post_param( 'attendee_pass' );
        $welcome_message  = $this->get_post_param( 'welcome_message', '', 'sanitize_textarea_field' );
        $max_participants = (int) $this->get_post_param( 'max_participants', 0 );
        $published        = (int) $this->get_post_param( 'published', 1 );
        $raw_roles        = $_POST['roles'] ?? array();
        $roles            = is_array( $raw_roles ) ? $this->sanitize_array_fields( $raw_roles ) : array();

        $room_metadata = [];
        foreach ( PlugnmeetHelper::$roomMetadataItems as $item ) {
            if ( isset( $_POST[ $item ] ) ) {
                $raw_meta               = $_POST[ $item ];
                $room_metadata[ $item ] = is_array( $raw_meta ) ? $this->sanitize_array_fields( $raw_meta ) : $this->sanitize_value( $raw_meta );
            } else {
                $room_metadata[ $item ] = [];
            }
        }

        // Multi-line field: keep its line breaks, which sanitize_text_field would strip.
        $summarization_prompt = $_POST['insights_features']['ai_features']['meeting_summarization_features']['summarization_prompt'] ?? null;
        if ( is_string( $summarization_prompt ) ) {
            $room_metadata['insights_features']['ai_features']['meeting_summarization_features']['summarization_prompt'] = sanitize_textarea_field( wp_unslash( $summarization_prompt ) );
        }

        $room_mode                  = 'ai_agent' === $this->get_post_param( 'room_mode' ) ? 'ai_agent' : 'conference';
        $room_metadata['room_mode'] = $room_mode;
        $this->validate_ai_agent_metadata( $room_metadata, $room_mode );

        if ( empty( $moderator_pass ) ) {
            $moderator_pass = PlugnmeetHelper::secureRandomKey( 10 );
        }

        if ( empty( $attendee_pass ) ) {
            $attendee_pass = PlugnmeetHelper::secureRandomKey( 10 );
        }

        if ( $attendee_pass === $moderator_pass ) {
            set_transient( 'plugnmeet_admin_notice', array(
                    'type'    => 'error',
                    'message' => __( "attendee & moderator password can't be same", 'plugnmeet' ),
            ), 5 * MINUTE_IN_SECONDS );
            wp_send_json( $output );
        }

        // for preventing display error. Room id should be always unique
        $room_id = "";
        if ( ! $id ) {
            if ( ! class_exists( 'plugNmeetConnect' ) ) {
                require plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
            }
            $options = $this->setting_params;
            $connect = new plugNmeetConnect( $options );
            $room_id = $connect->getUUID();
        }

        if ( ! $id ) {
            $inserted = $wpdb->insert(
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

            if ( $inserted !== false ) {
                // because $wpdb->insert_id may not give correct value
                $new_id         = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM " . $wpdb->prefix . "plugnmeet_rooms WHERE room_id = %s", $room_id ) );
                $output->status = true;
                set_transient( 'plugnmeet_admin_notice', array(
                        'type'    => 'success',
                        'message' => __( 'Successfully saved room data', 'plugnmeet' ),
                ), 5 * MINUTE_IN_SECONDS );
                $output->url = admin_url( 'admin.php?page=plugnmeet&task=edit&id=' . $new_id );
            } else {
                set_transient( 'plugnmeet_admin_notice', array(
                        'type'    => 'error',
                        'message' => $wpdb->last_error,
                ), 5 * MINUTE_IN_SECONDS );
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
                set_transient( 'plugnmeet_admin_notice', array(
                        'type'    => 'error',
                        'message' => $wpdb->last_error,
                ), 5 * MINUTE_IN_SECONDS );
            } else {
                $output->status = true;
                set_transient( 'plugnmeet_admin_notice', array(
                        'type'    => 'success',
                        'message' => __( 'Successfully updated room data', 'plugnmeet' ),
                ), 5 * MINUTE_IN_SECONDS );
                $output->url = admin_url( 'admin.php?page=plugnmeet&task=edit&id=' . $id );
            }
        }

        wp_send_json( $output );
    }

    private function send_error( string $message ) {
        $output         = new stdClass();
        $output->status = false;
        $output->url    = '';
        $output->msg    = $message;

        wp_send_json( $output );
    }

    private function clamp_number( $value, $min, $max ) {
        return min( $max, max( $min, (float) $value ) );
    }

    private function int_setting( array $settings, string $key, int $fallback ) {
        return isset( $settings[ $key ] ) && (int) $settings[ $key ] > 0 ? (int) $settings[ $key ] : $fallback;
    }

    private function validate_ai_agent_metadata( array &$room_metadata, string $room_mode ) {
        $insights = isset( $room_metadata['insights_features'] ) && is_array( $room_metadata['insights_features'] ) ? $room_metadata['insights_features'] : [];
        $ai_agent = isset( $insights['ai_agent_features'] ) && is_array( $insights['ai_agent_features'] ) ? $insights['ai_agent_features'] : [];

        if ( 'ai_agent' !== $room_mode && empty( $ai_agent['is_allow'] ) ) {
            return;
        }

        $settings = (array) $this->setting_params;

        // multi-line fields keep their line breaks
        foreach ( [ 'system_prompt', 'allowed_subtopics', 'forbidden_topics', 'allowed_knowledge_urls' ] as $field ) {
            if ( isset( $_POST['insights_features']['ai_agent_features'][ $field ] ) ) {
                $ai_agent[ $field ] = sanitize_textarea_field( wp_unslash( $_POST['insights_features']['ai_agent_features'][ $field ] ) );
            }
        }

        if ( isset( $_POST['insights_features']['ai_agent_features']['activity_rules']['required_milestones'] ) ) {
            if ( ! isset( $ai_agent['activity_rules'] ) || ! is_array( $ai_agent['activity_rules'] ) ) {
                $ai_agent['activity_rules'] = [];
            }
            $ai_agent['activity_rules']['required_milestones'] = sanitize_textarea_field( wp_unslash( $_POST['insights_features']['ai_agent_features']['activity_rules']['required_milestones'] ) );
        }

        $topic  = isset( $ai_agent['topic'] ) ? trim( $ai_agent['topic'] ) : '';
        $prompt = isset( $ai_agent['system_prompt'] ) ? trim( $ai_agent['system_prompt'] ) : '';

        if ( '' === $topic ) {
            $this->send_error( __( 'The AI agent needs a topic. Please fill in the session topic.', 'plugnmeet' ) );
        }
        if ( mb_strlen( $topic ) > 200 ) {
            $this->send_error( __( 'The AI agent session topic must be 200 characters or shorter.', 'plugnmeet' ) );
        }
        if ( '' === $prompt ) {
            $this->send_error( __( 'The AI agent needs instructions. Please fill in the agent instructions.', 'plugnmeet' ) );
        }

        $ai_agent['topic']         = $topic;
        $ai_agent['system_prompt'] = $prompt;

        $auto_config     = isset( $ai_agent['auto_session_config'] ) && is_array( $ai_agent['auto_session_config'] ) ? $ai_agent['auto_session_config'] : [];
        $participant_ids = isset( $auto_config['allowed_participant_ids'] ) ? PlugnmeetHelper::sanitizeUserIds( $auto_config['allowed_participant_ids'] ) : [];

        $session_mode = isset( $ai_agent['session_mode'] ) ? (int) $ai_agent['session_mode'] : 2;
        if ( 'ai_agent' === $room_mode && 1 === $session_mode && ( count( $participant_ids ) < 1 || count( $participant_ids ) > 32 ) ) {
            $this->send_error( __( 'A controlled AI session needs between 1 and 32 selected users.', 'plugnmeet' ) );
        }

        $auto_config['max_concurrent_sessions'] = (int) $this->clamp_number(
                $auto_config['max_concurrent_sessions'] ?? 10,
                1,
                $this->int_setting( $settings, 'ai_max_concurrent_sessions_limit', 10 )
        );
        $auto_config['allowed_participant_ids'] = $participant_ids;

        $ai_agent['session_mode']                = $session_mode;
        $ai_agent['auto_session_config']         = $auto_config;
        $max_minutes_limit               = min( 120, $this->int_setting( $settings, 'ai_max_session_minutes_limit', 60 ) );
        $default_minutes                 = min( $max_minutes_limit, $this->int_setting( $settings, 'ai_default_max_session_minutes', 30 ) );
        $ai_agent['max_session_minutes'] = (int) $this->clamp_number(
                $ai_agent['max_session_minutes'] ?? $default_minutes,
                1,
                $max_minutes_limit
        );
        unset( $ai_agent['max_session_seconds'], $ai_agent['max_audio_minutes'] );

        $ai_agent['topic_anchor_interval_turns'] = (int) $this->clamp_number( $ai_agent['topic_anchor_interval_turns'] ?? 5, 3, 12 );
        $ai_agent['speaking_rate']               = round( $this->clamp_number( $ai_agent['speaking_rate'] ?? 1.0, 0.5, 2.0 ), 1 );

        $rules = isset( $ai_agent['activity_rules'] ) && is_array( $ai_agent['activity_rules'] ) ? $ai_agent['activity_rules'] : [];
        foreach ( [ 'min_whiteboard_drawings', 'min_quizzes', 'min_notepad_entries', 'min_participant_exercises' ] as $rule_field ) {
            $quota_val = isset( $rules[ $rule_field ] ) ? (int) $rules[ $rule_field ] : 0;
            if ( $quota_val < 0 || $quota_val > 20 ) {
                $this->send_error( __( 'The required activity count must be between 0 and 20.', 'plugnmeet' ) );
            }
            $rules[ $rule_field ] = $quota_val;
        }

        $raw_milestones  = isset( $rules['required_milestones'] ) ? (string) $rules['required_milestones'] : '';
        $milestone_items = preg_split( '/[\r\n,]+/', $raw_milestones );
        $milestone_items = array_values( array_filter( array_map( 'trim', $milestone_items ), static function ( $item ) {
            return '' !== $item;
        } ) );

        $milestone_error = count( $milestone_items ) > 16;
        foreach ( $milestone_items as $milestone ) {
            if ( mb_strlen( $milestone ) > 240 ) {
                $milestone_error = true;
                break;
            }
        }
        if ( $milestone_error ) {
            $this->send_error( __( 'Enter at most 16 milestones of 240 characters or fewer, one per line.', 'plugnmeet' ) );
        }
        $ai_agent['activity_rules'] = $rules;

        if ( empty( $settings['ai_allow_web_fetch_global'] ) ) {
            $ai_agent['allow_web_fetch'] = 0;
        }

        if ( empty( $settings['ai_avatar_enabled_global'] ) ) {
            unset( $ai_agent['avatar'] );
        } elseif ( isset( $ai_agent['avatar'] ) && is_array( $ai_agent['avatar'] ) ) {
            $ai_agent['avatar'] = [
                'is_enabled' => ! empty( $ai_agent['avatar']['is_enabled'] ) ? 1 : 0,
            ];
        }
        // The AI agent card lives in the Insights tab, so keep both flags consistent.
        if ( 'ai_agent' === $room_mode || ! empty( $ai_agent['is_allow'] ) ) {
            $ai_agent['is_allow'] = 1;
            $insights['is_allow'] = 1;
        }

        $insights['ai_agent_features']      = $ai_agent;
        $room_metadata['insights_features'] = $insights;
    }

    public function delete_room() {
        global $wpdb;
        $output         = new stdClass();
        $output->status = false;

        if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'ajax_admin' ) ) {
            set_transient( 'plugnmeet_admin_notice', array(
                    'type'    => 'error',
                    'message' => __( 'Token mismatched', 'plugnmeet' ),
            ), 5 * MINUTE_IN_SECONDS );
            wp_send_json( $output );
        }
        $id = (int) $this->get_post_param( 'id', 0 );

        if ( ! $id ) {
            set_transient( 'plugnmeet_admin_notice', array(
                    'type'    => 'error',
                    'message' => __( "No id was sent", 'plugnmeet' ),
            ), 5 * MINUTE_IN_SECONDS );
            wp_send_json( $output );
        }

        $result = $wpdb->delete(
                $wpdb->prefix . 'plugnmeet_rooms',
                [ 'id' => $id ],
                [ '%d' ],
        );

        if ( $result === false ) {
            set_transient( 'plugnmeet_admin_notice', array(
                    'type'    => 'error',
                    'message' => $wpdb->last_error,
            ), 5 * MINUTE_IN_SECONDS );
        } else {
            $output->status = true;
            set_transient( 'plugnmeet_admin_notice', array(
                    'type'    => 'success',
                    'message' => __( "Room deleted successfully", "plugnmeet" ),
            ), 5 * MINUTE_IN_SECONDS );
        }

        wp_send_json( $output );
    }

    /**
     * Recursively sanitize array fields.
     *
     * @param array $array The array to sanitize.
     *
     * @return array The sanitized array.
     */
    private function sanitize_array_fields( $array ) {
        $sanitized_array = [];
        foreach ( $array as $key => $value ) {
            $key = sanitize_key( $key );
            if ( is_array( $value ) ) {
                $sanitized_array[ $key ] = $this->sanitize_array_fields( $value );
            } else {
                $sanitized_array[ $key ] = $this->sanitize_value( $value );
            }
        }

        return $sanitized_array;
    }
}
