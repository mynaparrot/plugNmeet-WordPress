<?php
/**
 * Central help texts for the Plugnmeet admin room edit screen.
 *
 * @since      1.1.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/helpers
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
    die;
}

if ( ! function_exists( 'plugnmeet_get_help_texts' ) ) {
    function plugnmeet_get_help_texts() {
        return array(
            // room_features
            'room_features.allow_webcams'                                               => __( 'Allow participants to use their webcams.', 'plugnmeet' ),
            'room_features.mute_on_start'                                               => __( 'Automatically mute all participants when they join the session.', 'plugnmeet' ),
            'room_features.allow_screen_share'                                          => __( 'Allow participants to share their screen.', 'plugnmeet' ),
            'room_features.allow_view_other_webcams'                                    => __( 'Allow participants to view other participants\' webcams. If disabled, they will only see moderators\' webcams.', 'plugnmeet' ),
            'room_features.allow_view_other_users_list'                                 => __( 'Allow participants to see the list of other participants.', 'plugnmeet' ),
            'room_features.admin_only_webcams'                                          => __( 'Only allow moderators to use their webcams.', 'plugnmeet' ),
            'room_features.room_duration'                                               => __( 'Defines how long the room can stay open, in minutes, before it closes automatically. Enter 0 for no time limit. This value cannot be changed after the session has started — the new duration will apply from the next session, or you can end the current session to apply it immediately.', 'plugnmeet' ),
            'room_features.moderator_join_first'                                        => __( 'When enabled, the session will not start and no one can join until a moderator joins first. Participants will remain on the page and will not enter the room or a waiting area. This is different from the Waiting Room option, where users can still join and wait.', 'plugnmeet' ),
            'room_features.enable_analytics'                                            => __( 'Tracks participation and engagement data for the session.', 'plugnmeet' ),
            'room_features.allow_virtual_bg'                                            => __( 'Enables participants to blur their background or use a virtual image. Great for maintaining privacy and ensuring a professional appearance.', 'plugnmeet' ),
            'room_features.allow_raise_hand'                                            => __( 'Allow participants to raise their hand.', 'plugnmeet' ),
            'room_features.allow_reactions'                                             => __( 'Allow participants to send floating reactions (e.g., thumbs up, clap) during the session.', 'plugnmeet' ),
            'room_features.auto_gen_user_id'                                            => __( 'When enabled, the system creates a new ID for each join so the same user can join from multiple devices, for example from a tablet to draw on the whiteboard while using a PC for the camera and microphone. When disabled, the system uses the WordPress user ID for logged-in users and auto-generates an ID based on the session ID for users who are not logged in. It is recommended to keep this off so duplicate joins are prevented.', 'plugnmeet' ),

            // recording_features
            'recording_features.is_allow'                                               => __( 'The primary master switch for all recording features. If disabled, both cloud and local recording options will be hidden from moderators.', 'plugnmeet' ),
            'recording_features.is_allow_cloud'                                         => __( 'Enables recording on the PlugNmeet server. The final video is processed and stored in the cloud, making it easy to share via a link once the session ends.', 'plugnmeet' ),
            'recording_features.enable_auto_cloud_recording'                            => __( 'Automatically triggers the cloud recording the moment the first moderator joins the session. Ideal for ensuring no session is forgotten.', 'plugnmeet' ),
            'recording_features.is_allow_local'                                         => __( 'Allows recording directly to the computer. <strong>Note:</strong> Requires Google Chrome. To capture all session audio, the user must share their "Chrome Tab" and check the "Also share tab audio" box. The file is saved locally to the device once finished.', 'plugnmeet' ),

            // external_broadcasting_features
            'external_broadcasting_features.is_allow'                                   => __( 'The primary master switch for all broadcasting features. If disabled, all other broadcasting options will be disabled.', 'plugnmeet' ),
            'external_broadcasting_features.is_allow_rtmp'                              => __( 'Enables moderators to broadcast the live session to external platforms like YouTube, Facebook, or Twitch. Perfect for public webinars or guest lectures.', 'plugnmeet' ),

            // chat_features
            'chat_features.is_allow'                                                    => __( 'The absolute master switch for all chat functions. If disabled, the entire chat area is removed for everyone, including moderators. No public or private chat will be possible.', 'plugnmeet' ),
            'chat_features.is_allow_file_upload'                                        => __( 'Enables the ability to share files within the chat. This requires the chat feature to be active.', 'plugnmeet' ),

            // shared_note_pad_features
            'shared_note_pad_features.is_allow'                                         => __( 'Enables a collaborative text editor where participants and moderators can take real-time notes together. This is perfect for brainstorming, co-writing, or building a shared summary.', 'plugnmeet' ),

            // whiteboard_features
            'whiteboard_features.is_allow'                                              => __( 'Unlocks an interactive drawing space for the session. Use it for sketching diagrams, solving math problems visually, or letting participants annotate shared content.', 'plugnmeet' ),

            // external_media_player_features
            'external_media_player_features.is_allow'                                   => __( 'Allows moderators to sync and play videos from sources like YouTube or Vimeo for everyone. Ideal for analyzing clips or watching videos together without bandwidth lag.', 'plugnmeet' ),

            // waiting_room_features
            'waiting_room_features.is_active'                                           => __( 'Enable the waiting room for this session.', 'plugnmeet' ),
            'waiting_room_features.waiting_room_msg'                                    => __( 'This message is shown to users while they are waiting to be admitted to the room. Use it to share instructions, expectations, or helpful information before the session starts.', 'plugnmeet' ),

            // breakout_room_features
            'breakout_room_features.is_allow'                                           => __( 'Enables moderators to split participants into smaller groups for focused discussions, collaborative projects, or private group activities.', 'plugnmeet' ),
            'breakout_room_features.allowed_number_rooms'                               => __( 'The maximum number of sub-rooms a moderator can create within a single session.', 'plugnmeet' ),

            // display_external_link_features
            'display_external_link_features.is_allow'                                   => __( 'Allows moderators to display external content, such as H5P activities, SCORM packages, or quiz systems (e.g., Kahoot), directly to all participants during the session.', 'plugnmeet' ),

            // ingress_features
            'ingress_features.is_allow'                                                 => __( 'Allows moderators to broadcast high-quality video into the session using external software like OBS via RTMP or WHIP protocols. This is ideal for sharing high-resolution video content, pre-recorded sessions, or bypassing browser upload limitations.', 'plugnmeet' ),

            // polls_features
            'polls_features.is_allow'                                                   => __( 'Enables moderators to create interactive polls and quick quizzes during the session. This is a great way to check participant understanding and increase engagement.', 'plugnmeet' ),

            // sip_dial_in_features
            'sip_dial_in_features.is_allow'                                             => __( 'Allows participants to join the audio session via a traditional phone call. This is essential for participants with poor internet connections or those who need to join while on the go.', 'plugnmeet' ),
            'sip_dial_in_features.enable_dial_in_on_create'                             => __( 'Automatically activate SIP dial-in when the room is created, so participants can join by phone immediately without a moderator enabling it during the session.', 'plugnmeet' ),
            'sip_dial_in_features.hide_phone_number'                                    => __( 'Hide the phone numbers of participants who joined via SIP dial-in from the participants list and chat.', 'plugnmeet' ),

            // end_to_end_encryption_features
            'end_to_end_encryption_features.is_enabled'                                 => __( 'Provides strong security by encrypting video and audio streams between participants. In this mode, the server generates and securely distributes a unique encryption key for the session.', 'plugnmeet' ),
            'end_to_end_encryption_features.enabled_self_insert_encryption_key'         => __( 'Allows participants to manually enter a shared secret key, offering the highest level of privacy as the server never has access to the encryption keys. It is the host\'s responsibility to share the same secret with all participants; if different keys are used, users will not be able to communicate with each other. <strong>Note:</strong> When this option is enabled, features that require server-side media processing (like cloud recording, transcription, and AI summarization) will be automatically disabled.', 'plugnmeet' ),
            'end_to_end_encryption_features.included_chat_messages'                     => __( 'Extends end-to-end encryption to text chat, ensuring messages are only readable by participants in the room.', 'plugnmeet' ),
            'end_to_end_encryption_features.included_whiteboard'                        => __( 'Includes all whiteboard drawings and annotations in the end-to-end encryption layer for total data privacy.', 'plugnmeet' ),

            // insights_features
            'insights_features.is_allow'                                                => __( 'The primary master switch for all data-driven features. If disabled, all sub-options, including Transcription, AI Assistant, and Meeting Reports, will be completely unavailable.', 'plugnmeet' ),
            'insights_features.transcription_features.is_allow'                         => __( 'Enables real-time speech-to-text during the session. This must be enabled for transcription translation or speech synthesis (TTS) to work.', 'plugnmeet' ),
            'insights_features.transcription_features.is_allow_translation'             => __( 'Allows the live transcript to be translated into different languages, supporting multilingual sessions.', 'plugnmeet' ),
            'insights_features.transcription_features.is_allow_speech_synthesis'        => __( 'Enables Text-to-Speech, allowing the live transcript to be read aloud for better accessibility and for visually impaired participants.', 'plugnmeet' ),
            'insights_features.chat_translation_features.is_allow'                      => __( 'Enables real-time translation of chat messages, allowing participants to communicate instantly across different languages.', 'plugnmeet' ),
            'insights_features.ai_features.is_allow'                                    => __( 'The master switch for all AI-powered capabilities. If disabled, specific features like AI Chat and Meeting Summarization will be hidden, even if Insights is active.', 'plugnmeet' ),
            'insights_features.ai_features.ai_text_chat_features.is_allow'              => __( 'Integrates an AI assistant directly into the chat to help answer questions, explain complex concepts, and facilitate discussion.', 'plugnmeet' ),
            'insights_features.ai_features.meeting_summarization_features.is_allow'     => __( 'Automatically generates a concise AI summary after the session ends, highlighting key points, decisions, and follow-up tasks.', 'plugnmeet' ),

            // default_lock_settings
            'default_lock_settings.lock_microphone'                                     => __( 'Lock the microphone for participants. They cannot turn on or use their microphone until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_webcam'                                         => __( 'Lock the webcam for participants. They cannot share their camera until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_screen_sharing'                                 => __( 'Lock screen sharing for participants. They cannot share their screen until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_whiteboard'                                     => __( 'Lock the whiteboard for participants. They cannot draw or annotate on the whiteboard until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_shared_notepad'                                 => __( 'Lock the shared notepad for participants. They cannot write in the shared notepad until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_chat'                                           => __( 'Lock the chat for participants. They cannot use the chat until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_chat_send_message'                              => __( 'Lock message sending for participants. They can still read the chat but cannot send messages until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_chat_file_share'                                => __( 'Lock file sharing in chat for participants. They cannot share files in the chat until a moderator unlocks it.', 'plugnmeet' ),
            'default_lock_settings.lock_private_chat'                                   => __( 'Lock private chat for participants. They cannot send private messages to other participants until a moderator unlocks it. They can still send private messages to moderators.', 'plugnmeet' ),
            'default_lock_settings.lock_reactions'                                      => __( 'Lock reactions for participants. They cannot send reactions until a moderator unlocks it.', 'plugnmeet' ),

            // basic tab
            'basic.room_id'                                                             => __( 'The unique ID of this room and everything tied to it. The SDK uses this ID to create the room, and recordings, artifacts and other room data will always be linked to it. It is generated automatically and cannot be changed. The second field shows the WordPress shortcode you can paste into any post or page to embed this room.', 'plugnmeet' ),
            'basic.room_title'                                                          => __( 'The name used for the meeting header. It helps participants identify the session.', 'plugnmeet' ),
            'basic.description'                                                         => __( 'A short description of the room. Useful for internal notes and for identifying the session.', 'plugnmeet' ),
            'basic.moderator_pass'                                                      => __( 'The password required to join this room as a moderator. It is auto-generated when the room is created and can be replaced.', 'plugnmeet' ),
            'basic.attendee_pass'                                                       => __( 'The password required to join this room as an attendee. It is auto-generated when the room is created and can be replaced.', 'plugnmeet' ),
            'basic.welcome_message'                                                     => __( 'Any text entered here will appear as the first message in the public chat when users join the room. This is useful for sharing instructions, links, or important information with participants as they enter.', 'plugnmeet' ),
            'basic.max_participants'                                                    => __( 'Set this value to limit how many users can join the session at the same time. Set it to 0 to allow unlimited participants. This value cannot be changed after the session has started — the new limit will apply from the next session, or you can end the current session to apply it immediately.', 'plugnmeet' ),
            'basic.published'                                                           => __( 'Published rooms are available to users on the room page. Unpublished rooms stay hidden until you publish them again.', 'plugnmeet' ),

            // design tab
            'design.custom_css_url'                                                     => __( 'Link an external .css file to overwrite or customize the interface styles.', 'plugnmeet' ),
            'design.primary_color'                                                      => __( 'Main accent color used for buttons, active states, and highlights.', 'plugnmeet' ),
            'design.secondary_color'                                                    => __( 'The accent color used for secondary UI elements.', 'plugnmeet' ),
            'design.background_color'                                                   => __( 'The primary background color of the interface.', 'plugnmeet' ),
            'design.background_image'                                                   => __( 'Background wallpaper. For best results, use a 1920x1080 image.', 'plugnmeet' ),
            'design.logo'                                                               => __( 'Upload your logo to the meeting room. If left blank, the default PlugNmeet logo will be used.', 'plugnmeet' ),
            'design.header_color'                                                       => __( 'Background color for the top navigation bar.', 'plugnmeet' ),
            'design.footer_color'                                                       => __( 'Background color for the bottom toolbar.', 'plugnmeet' ),
            'design.side_panel_bg_color'                                                => __( 'Background color for the right-side panels (Chat and Participants list).', 'plugnmeet' ),

            // permission tab
            'permission.join_as_moderator'                                              => __( 'Users with this role join as moderators and can manage the session: mute or lock participants, control recordings, polls and breakout rooms.', 'plugnmeet' ),
            'permission.join_as_attendee'                                               => __( 'Users with this role join as attendees: regular participants whose permissions are limited by the room features and default lock settings.', 'plugnmeet' ),
            'permission.require_password'                                               => __( 'Require users with this role to enter a password before joining, depending on their join mode.', 'plugnmeet' ),
            'permission.can_view_recording'                                             => __( 'Allow users with this role to view this room\'s recordings.', 'plugnmeet' ),
            'permission.can_play'                                                       => __( 'Allow users with this role to play this room\'s recordings.', 'plugnmeet' ),
            'permission.can_download'                                                   => __( 'Allow users with this role to download this room\'s recordings.', 'plugnmeet' ),
            'permission.can_delete'                                                     => __( 'Allow users with this role to delete this room\'s recordings. Not applicable for guests.', 'plugnmeet' ),
            // settings page
            'settings.plugnmeet_server_url'                                             => __( 'The base URL of your PlugNmeet server (e.g., https://pnm.example.com).', 'plugnmeet' ),
            'settings.plugnmeet_api_key'                                                => __( 'The unique API key provided by your PlugNmeet server.', 'plugnmeet' ),
            'settings.plugnmeet_secret'                                                 => __( 'The API secret provided by your PlugNmeet server used for secure authentication.', 'plugnmeet' ),
            'settings.client_load'                                                      => __( 'Choose how the meeting client is delivered. Remote (default): the meeting opens on a standalone page served by this plugin, with the client files loaded from your plugNmeet server. Local: the client files are served from this plugin — use the Update button to download them first; useful for serving your own customized client. Redirect: users are taken directly to your plugNmeet server\'s interface and leave your site.', 'plugnmeet' ),
            'settings.client_download_url'                                              => __( 'Advanced option for using your own customized plugNmeet client. The Update button downloads the client zip from this URL to your server. Only used when client load is set to Local.', 'plugnmeet' ),
            'settings.enable_dynacast'                                                  => __( 'Optimizes performance by dynamically pausing video layers that are not being viewed by participants. This significantly reduces server-side CPU and bandwidth usage. <strong>Note:</strong> This will be automatically enabled if using SVC codecs (VP9/AV1) and is required for multi-codec simulcast.', 'plugnmeet' ),
            'settings.enable_simulcast'                                                 => __( 'Delivers multiple video quality layers to ensure a smooth experience for users on weak connections.', 'plugnmeet' ),
            'settings.video_codec'                                                      => __( 'The encoding standard for video transmission. VP8 is the default and offers the widest browser compatibility. VP9 offers a superior balance of video quality, CPU efficiency and low latency, and automatically falls back to VP8 on older browsers. <strong>Warning:</strong> Advanced codecs like AV1 require modern browser support; ensure your users are on compatible setups if you deviate from the default.', 'plugnmeet' ),
            'settings.default_webcam_resolution'                                        => __( 'The initial resolution setting for participant cameras.', 'plugnmeet' ),
            'settings.default_screen_share_resolution'                                  => __( 'The initial resolution setting for screen sharing.', 'plugnmeet' ),
            'settings.default_audio_preset'                                             => __( 'The audio quality profile optimized for the session.', 'plugnmeet' ),
            'settings.stop_mic_track_on_mute'                                           => __( 'When enabled, the microphone track is fully stopped while muted instead of just being silenced, so no audio data is sent while muted.', 'plugnmeet' ),
            'settings.logo'                                                             => __( 'Upload your logo to the meeting room. If left blank, the default PlugNmeet logo will be used.', 'plugnmeet' ),
            'settings.copyright_display'                                                => __( 'Display the copyright text in the client footer.', 'plugnmeet' ),
            'settings.copyright_text'                                                   => __( 'Branding text displayed in the client footer. HTML is supported for links or custom styling.', 'plugnmeet' ),
            'settings.room_host_page'                                                   => __( 'The page a room link opens on. By default the room opens inside your theme\'s normal layout; for a clean, distraction-free meeting view, create an empty page (Pages > Add New) and select it here. After changing, go to Settings > Permalinks and simply click "Save Changes".', 'plugnmeet' ),
            'settings.room_slug_path'                                                   => __( 'The first part of every room link. The default plugnmeet/room gives links like yoursite.com/plugnmeet/room/room-id; you can change it to any word you prefer, e.g. meetings. After changing it, go to Settings > Permalinks and simply click "Save Changes".', 'plugnmeet' ),
            'settings.custom_css_url'                                                   => __( 'Link an external .css file to overwrite or customize the interface styles.', 'plugnmeet' ),
            'settings.primary_color'                                                    => __( 'Main accent color used for buttons, active states, and highlights.', 'plugnmeet' ),
            'settings.secondary_color'                                                  => __( 'The accent color used for secondary UI elements.', 'plugnmeet' ),
            'settings.background_color'                                                 => __( 'The primary background color of the interface.', 'plugnmeet' ),
            'settings.background_image'                                                 => __( 'Background wallpaper. For best results, use a 1920x1080 image.', 'plugnmeet' ),
            'settings.header_color'                                                     => __( 'Background color for the top navigation bar.', 'plugnmeet' ),
            'settings.footer_color'                                                     => __( 'Background color for the bottom toolbar.', 'plugnmeet' ),
            'settings.side_panel_bg_color'                                              => __( 'Background color for the right-side panels (Chat and Participants list).', 'plugnmeet' ),
        );
    }
}

if ( ! function_exists( 'plugnmeet_get_help_text' ) ) {
    /**
     * Returns the help text for a dotted field path, or '' when missing.
     *
     * @param string $path Dotted field path, e.g. 'chat_features.is_allow'.
     *
     * @return string
     */
    function plugnmeet_get_help_text( $path ) {
        $texts = plugnmeet_get_help_texts();

        return isset( $texts[ $path ] ) ? $texts[ $path ] : '';
    }
}

if ( ! function_exists( 'plugnmeet_help_text_html' ) ) {
    /**
     * Returns a <p class="description"> block for a dotted field path,
     * or '' when the path has no help text.
     *
     * @param string $path Dotted field path.
     *
     * @return string
     */
    function plugnmeet_help_text_html( $path ) {
        $text = plugnmeet_get_help_text( $path );

        if ( '' === $text ) {
            return '';
        }

        $allowed_tags = array(
            'strong' => array(),
            'b'      => array(),
            'em'     => array(),
            'i'      => array(),
            'code'   => array(),
            'br'     => array(),
        );

        return '<p class="description">' . wp_kses( $text, $allowed_tags ) . '</p>';
    }
}
