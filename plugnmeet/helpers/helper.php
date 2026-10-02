<?php
/**
 *
 * @since      1.0.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/helpers
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'plugnmeet-help-texts.php';

class PlugnmeetHelper {

	public static $roomMetadataItems = [
		'room_features',
		'recording_features',
		'external_broadcasting_features',
		'chat_features',
		'shared_note_pad_features',
		'whiteboard_features',
		'external_media_player_features',
		'waiting_room_features',
		'breakout_room_features',
		'display_external_link_features',
		'ingress_features',
		'end_to_end_encryption_features',
		'insights_features',
		'polls_features',
		'sip_dial_in_features',
		'default_lock_settings',
		'custom_design'
	];

	private static $allowedHtml = array(
		'select'   => array(
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'class' => array(),
		),
		'option'   => array(
			'value'    => array(),
			'selected' => array(),
		),
		'input'    => array(
			'type'         => array(),
			'id'           => array(),
			'name'         => array(),
			'value'        => array(),
			'class'        => array(),
			'style'        => array(),
			'autocomplete' => array()
		),
		'textarea' => array(
			'name' => array(),
			'cols' => array(),
			'rows' => array(),
			'id'   => array()
		),
		'tr'       => array(
			'class' => array()
		),
		'th'       => array(
			'scope'   => array(),
			'colspan' => array()
		),
		'td'       => array(
			'scope'   => array(),
			'colspan' => array()
		),
		'p'        => array(
			'class' => array()
		),
		'strong'   => array(),
		'b'        => array(),
		'em'       => array(),
		'i'        => array(),
		'code'     => array(),
		'br'       => array(),
		'label'    => array(
			'for' => array()
		),
		'hr'       => array(),
	);

	public static function get_room_url( $room_id ) {
		if ( get_option( 'permalink_structure' ) ) {
			$setting_params = (object) get_option( "plugnmeet_settings" );
			$slug           = ! empty( $setting_params->room_slug_path ) ? rtrim( $setting_params->room_slug_path, "/" ) : "plugnmeet/room";

			return home_url( $slug . "/" . $room_id . '/' );
		} else {
			return add_query_arg( 'plugnmeet_room', $room_id, home_url( '/' ) );
		}
	}

	public static function secureRandomKey( int $length = 36 ): string {
		$keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$pieces   = [];
		$max      = mb_strlen( $keyspace, '8bit' ) - 1;
		for ( $i = 0; $i < $length; ++ $i ) {
			$pieces [] = $keyspace[ random_int( 0, $max ) ];
		}

		return implode( '', $pieces );
	}

	private static $sectionTitles = array(
		"insights_features.transcription_features" => "Transcription",
		"insights_features.chat_translation_features" => "Chat translation",
		"insights_features.ai_features" => "AI features",
	);

	private static function buildFieldId( $dottedPath ) {
		return 'pnm-' . str_replace( array( '.', '[', ']' ), '-', $dottedPath );
	}

	private static function formatHtml( $items, $fieldName, $data, $isRecursiveCall = false, $dottedPath = '' ) {
		$html = "";
		foreach ( $items as $key => $item ) {
			if ( is_array( $item ) && ! isset( $item['type'] ) ) {
				$newFieldName  = $fieldName . '[' . $key . ']';
				$newDottedPath = ( '' === $dottedPath ? $fieldName : $dottedPath ) . '.' . $key;
				$newData       = isset( $data[ $key ] ) ? $data[ $key ] : [];
				if ( isset( self::$sectionTitles[ $newDottedPath ] ) ) {
					$html .= '<tr class="pnm-subsection-title"><th scope="row" colspan="2">' . __( self::$sectionTitles[ $newDottedPath ], "plugnmeet" ) . '</th></tr>';
				} else {
					$html .= '<tr class="pnm-subsection-divider"><th scope="row" colspan="2"></th></tr>';
				}
				$html .= self::formatHtml( $item, $newFieldName, $newData, true, $newDottedPath );
			} elseif ( isset( $item['type'] ) ) {
				$composedPath = ( '' === $dottedPath ? $fieldName : $dottedPath ) . '.' . $key;
				$fieldId      = self::buildFieldId( $composedPath );
				$helpText     = plugnmeet_help_text_html( $composedPath );

				if ( $item["type"] === "select" ) {
					$html .= '<tr>';
					$html .= '<th scope="row"><label for="' . esc_attr( $fieldId ) . '">' . $item['label'] . '</label></th>';
					$html .= '<td>';
					$html .= "<select id=\"{$fieldId}\" name=\"{$fieldName}[{$key}]\" class=\"list_class\">";

					$value = $item["selected"];
					if ( isset( $data[ $key ] ) ) {
						$value = $data[ $key ];
					}

					foreach ( $item["options"] as $option ) {
						$selected = "";
						if ( $option['value'] == $value ) {
							$selected = "selected";
						}
						$html .= '<option value="' . esc_attr( $option['value'] ) . '" ' . $selected . '>' . esc_attr( $option['label'] ) . '</option>';
					}

					$html .= '</select>' . $helpText . '</td></tr>';
				} elseif ( $item["type"] === "text" || $item["type"] === "number" ) {
					$value = $item["default"];
					if ( isset( $data[ $key ] ) ) {
						$value = $data[ $key ];
					}
					$html .= '<tr>';
					$html .= '<th scope="row"><label for="' . esc_attr( $fieldId ) . '">' . esc_attr( $item['label'] ) . '</label></th>';
					$html .= '<td>';
					$html .= '<input id="' . esc_attr( $fieldId ) . '" type="' . esc_attr( $item["type"] ) . '" name="' . $fieldName . '[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '"   autocomplete="off">';
					$html .= $helpText;
					$html .= '</td></tr>';
				} elseif ( $item["type"] === "textarea" ) {
					$value = $item["default"];
					if ( isset( $data[ $key ] ) ) {
						$value = $data[ $key ];
					}
					$html .= '<tr>';
					$html .= '<th scope="row"><label for="' . esc_attr( $fieldId ) . '">' . esc_attr( $item['label'] ) . '</label></th>';
					$html .= '<td>';
					$html .= '<textarea id="' . esc_attr( $fieldId ) . '" name="' . esc_attr( $fieldName ) . '[' . esc_attr( $key ) . ']">' . esc_attr( $value ) . '</textarea>';
					$html .= $helpText;
					$html .= '</td></tr>';
				}
			}
		}

		if ( $isRecursiveCall ) {
			return $html;
		}

		return wp_kses( $html, self::$allowedHtml );
	}

	public static function getRoomFeatures( $room_features ) {
		$roomFeatures = array(
			"room_duration"               => array(
				"label"   => __( "Room duration", "plugnmeet" ),
				"default" => 0,
				"type"    => "number"
			),
			"allow_webcams"               => array(
				"label"    => __( "Allow webcams", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"mute_on_start"               => array(
				"label"    => __( "Mute on start", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"allow_screen_share"          => array(
				"label"    => __( "Allow screen share", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"allow_view_other_webcams"    => array(
				"label"    => __( "Allow view other webcams", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"allow_view_other_users_list" => array(
				"label"    => __( "Allow view other users list", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"admin_only_webcams"          => array(
				"label"    => __( "Admin only webcams", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"moderator_join_first"        => array(
				"label"    => __( "Moderator join first", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"enable_analytics"            => array(
				"label"    => __( "Enable analytics", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"allow_virtual_bg"            => array(
				"label"    => __( "Allow virtual background", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"allow_raise_hand"            => array(
				"label"    => __( "Allow raise hand", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"allow_reactions"             => array(
				"label"    => __( "Allow reactions", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"auto_gen_user_id"            => array(
				"label"    => __( "Auto generate user ID", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
		);

		$data = [];
		if ( ! empty( $room_features ) ) {
			$data = $room_features;
		}

		return self::formatHtml( $roomFeatures, "room_features", $data );
	}

	public static function getRecordingFeatures( $recording_features ) {
		$recordingFeatures = array(
			"is_allow"                    => array(
				"label"    => __( "Allow recording", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"is_allow_cloud"              => array(
				"label"    => __( "Allow cloud recording", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"enable_auto_cloud_recording" => array(
				"label"    => __( "Enable auto cloud recording", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"is_allow_local"              => array(
				"label"    => __( "Allow local recording", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
		);

		$data = [];
		if ( ! empty( $recording_features ) ) {
			$data = $recording_features;
		}

		return self::formatHtml( $recordingFeatures, "recording_features", $data );
	}

	public static function getExternalBroadcastingFeatures( $external_broadcasting_features ) {
		$externalBroadcastingFeatures = array(
			"is_allow"      => array(
				"label"    => __( "Allow External Broadcasting Features", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"is_allow_rtmp" => array(
				"label"    => __( "Allow RTMP broadcasting", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
		);

		$data = [];
		if ( ! empty( $external_broadcasting_features ) ) {
			$data = $external_broadcasting_features;
		}

		return self::formatHtml( $externalBroadcastingFeatures, "external_broadcasting_features", $data );
	}

	public static function getChatFeatures( $chat_features ) {
		$chatFeatures = array(
			"is_allow"             => array(
				"label"    => __( "Allow chat", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"is_allow_file_upload" => array(
				"label"    => __( "Allow file upload", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
		);

		$data = [];
		if ( ! empty( $chat_features ) ) {
			$data = $chat_features;
		}

		return self::formatHtml( $chatFeatures, "chat_features", $data );
	}

	public static function getSharedNotePadFeatures( $sharedNotePad_features ) {
		$sharedNotePadFeatures = array(
			"is_allow" => array(
				"label"    => __( "Allow shared notepad", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			)
		);

		$data = [];
		if ( ! empty( $sharedNotePad_features ) ) {
			$data = $sharedNotePad_features;
		}

		return self::formatHtml( $sharedNotePadFeatures, "shared_note_pad_features", $data );
	}

	public static function getWhiteboardFeatures( $whiteboard_features ) {
		$whiteboardFeatures = array(
			"is_allow" => array(
				"label"    => __( "Allow whiteboard", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			)
		);

		$data = [];
		if ( ! empty( $whiteboard_features ) ) {
			$data = $whiteboard_features;
		}

		return self::formatHtml( $whiteboardFeatures, "whiteboard_features", $data );
	}

	public static function getExternalMediaPlayerFeatures( $external_media_player_features ) {
		$externalMediaPlayerFeatures = array(
			"is_allow" => array(
				"label"    => __( "Allow external media player", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			)
		);

		$data = [];
		if ( ! empty( $external_media_player_features ) ) {
			$data = $external_media_player_features;
		}

		return self::formatHtml( $externalMediaPlayerFeatures, "external_media_player_features", $data );
	}

	public static function getWaitingRoomFeatures( $waiting_room_features ) {
		$waitingRoomFeatures = array(
			"is_active"        => array(
				"label"    => __( "Activate waiting room", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"waiting_room_msg" => array(
				"label"   => __( "Waiting room message", "plugnmeet" ),
				"default" => "",
				"type"    => "textarea"
			)
		);

		$data = [];
		if ( ! empty( $waiting_room_features ) ) {
			$data = $waiting_room_features;
		}

		return self::formatHtml( $waitingRoomFeatures, "waiting_room_features", $data );
	}

	public static function getBreakoutRoomFeatures( $breakout_room_features ) {
		$breakoutRoomFeatures = array(
			"is_allow"             => array(
				"label"    => __( "Allow breakout rooms", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"allowed_number_rooms" => array(
				"label"   => __( "Number of rooms", "plugnmeet" ),
				"default" => 6,
				"type"    => "number"
			)
		);

		$data = [];
		if ( ! empty( $breakout_room_features ) ) {
			$data = $breakout_room_features;
		}

		return self::formatHtml( $breakoutRoomFeatures, "breakout_room_features", $data );
	}

	public static function getDisplayExternalLinkFeatures( $display_external_link_features ) {
		$displayExternalLinkFeatures = array(
			"is_allow" => array(
				"label"    => __( "Allow Display External Link", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			)
		);

		$data = [];
		if ( ! empty( $display_external_link_features ) ) {
			$data = $display_external_link_features;
		}

		return self::formatHtml( $displayExternalLinkFeatures, "display_external_link_features", $data );
	}

	public static function getIngressFeatures( $ingress_features ) {
		$ingressFeatures = array(
			"is_allow" => array(
				"label"    => __( "Allow create ingress", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			)
		);

		$data = [];
		if ( ! empty( $ingress_features ) ) {
			$data = $ingress_features;
		}

		return self::formatHtml( $ingressFeatures, "ingress_features", $data );
	}

	public static function getPollsFeatures( $polls_features ) {
		$pollsFeatures = array(
			"is_allow" => array(
				"label"    => __( "Allow polls", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			)
		);

		$data = [];
		if ( ! empty( $polls_features ) ) {
			$data = $polls_features;
		}

		return self::formatHtml( $pollsFeatures, "polls_features", $data );
	}

	public static function getEndToEndEncryptionFeatures( $e2ee_features ) {
		$e2eeFeatures = array(
			"is_enabled"                         => array(
				"label"    => __( "Enable End-To-End Encryption (E2EE)", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"enabled_self_insert_encryption_key" => array(
				"label"    => __( "Enable self insert key", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"included_chat_messages"             => array(
				"label"    => __( "Enable encryption for chat", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"included_whiteboard"                => array(
				"label"    => __( "Enable encryption for whiteboard", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			)
		);

		$data = [];
		if ( ! empty( $e2ee_features ) ) {
			$data = $e2ee_features;
		}

		return self::formatHtml( $e2eeFeatures, "end_to_end_encryption_features", $data );
	}

	public static function getSipDialInFeatures( $sip_dial_in_features ) {
		$sipDialInFeatures = array(
			"is_allow"                 => array(
				"label"    => __( "Allow SIP/VoIP dial in", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"enable_dial_in_on_create" => array(
				"label"    => __( "Enable dial in on create", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"hide_phone_number"        => array(
				"label"    => __( "Hide phone number", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
		);

		$data = [];
		if ( ! empty( $sip_dial_in_features ) ) {
			$data = $sip_dial_in_features;
		}

		return self::formatHtml( $sipDialInFeatures, "sip_dial_in_features", $data );
	}

	public static function getInsightsFeatures( $insights_features ) {
		$insightsFeatures = array(
			"is_allow"                  => array(
				"label"    => __( "Enable insights features", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"transcription_features"    => array(
				"is_allow"                  => array(
					"label"    => __( "Allow transcription", "plugnmeet" ),
					"options"  => array(
						array(
							"label" => __( "Yes", "plugnmeet" ),
							"value" => 1
						),
						array(
							"label" => __( "No", "plugnmeet" ),
							"value" => 0
						)
					),
					"selected" => 0,
					"type"     => "select"
				),
				"is_allow_translation"      => array(
					"label"    => __( "Allow translation feature", "plugnmeet" ),
					"options"  => array(
						array(
							"label" => __( "Yes", "plugnmeet" ),
							"value" => 1
						),
						array(
							"label" => __( "No", "plugnmeet" ),
							"value" => 0
						)
					),
					"selected" => 0,
					"type"     => "select"
				),
				"is_allow_speech_synthesis" => array(
					"label"    => __( "Allow speech synthesis", "plugnmeet" ),
					"options"  => array(
						array(
							"label" => __( "Yes", "plugnmeet" ),
							"value" => 1
						),
						array(
							"label" => __( "No", "plugnmeet" ),
							"value" => 0
						)
					),
					"selected" => 0,
					"type"     => "select"
				),
			),
			"chat_translation_features" => array(
				"is_allow" => array(
					"label"    => __( "Allow chat translation", "plugnmeet" ),
					"options"  => array(
						array(
							"label" => __( "Yes", "plugnmeet" ),
							"value" => 1
						),
						array(
							"label" => __( "No", "plugnmeet" ),
							"value" => 0
						)
					),
					"selected" => 0,
					"type"     => "select"
				),
			),
			"ai_features"               => array(
				"is_allow"                       => array(
					"label"    => __( "Enable AI features", "plugnmeet" ),
					"options"  => array(
						array(
							"label" => __( "Yes", "plugnmeet" ),
							"value" => 1
						),
						array(
							"label" => __( "No", "plugnmeet" ),
							"value" => 0
						)
					),
					"selected" => 0,
					"type"     => "select"
				),
				"ai_text_chat_features"          => array(
					"is_allow" => array(
						"label"    => __( "Allow AI text chat", "plugnmeet" ),
						"options"  => array(
							array(
								"label" => __( "Yes", "plugnmeet" ),
								"value" => 1
							),
							array(
								"label" => __( "No", "plugnmeet" ),
								"value" => 0
							)
						),
						"selected" => 0,
						"type"     => "select"
					),
				),
				"meeting_summarization_features" => array(
					"is_allow" => array(
						"label"    => __( "Allow meeting summarization", "plugnmeet" ),
						"options"  => array(
							array(
								"label" => __( "Yes", "plugnmeet" ),
								"value" => 1
							),
							array(
								"label" => __( "No", "plugnmeet" ),
								"value" => 0
							)
						),
						"selected" => 0,
						"type"     => "select"
					),
				)
			)
		);

		$data = [];
		if ( ! empty( $insights_features ) ) {
			$data = $insights_features;
		}

		return self::formatHtml( $insightsFeatures, "insights_features", $data );
	}

	public static function getDefaultLockSettings( $default_lock_settings ) {
		$defaultLockSettings = array(
			"lock_microphone"        => array(
				"label"    => __( "Lock microphone", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"lock_webcam"            => array(
				"label"    => __( "Lock webcam", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"lock_screen_sharing"    => array(
				"label"    => __( "Lock screen sharing", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"lock_whiteboard"        => array(
				"label"    => __( "Lock whiteboard", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"lock_shared_notepad"    => array(
				"label"    => __( "Lock shared notepad", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 1,
				"type"     => "select"
			),
			"lock_chat"              => array(
				"label"    => __( "Lock chat", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"lock_chat_send_message" => array(
				"label"    => __( "Lock chat send message", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"lock_chat_file_share"   => array(
				"label"    => __( "Lock chat file share", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"lock_private_chat"      => array(
				"label"    => __( "Lock private chat", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
			"lock_reactions"         => array(
				"label"    => __( "Lock reactions", "plugnmeet" ),
				"options"  => array(
					array(
						"label" => __( "Yes", "plugnmeet" ),
						"value" => 1
					),
					array(
						"label" => __( "No", "plugnmeet" ),
						"value" => 0
					)
				),
				"selected" => 0,
				"type"     => "select"
			),
		);

		$data = [];
		if ( ! empty( $default_lock_settings ) ) {
			$data = (array) $default_lock_settings;
		}

		return self::formatHtml( $defaultLockSettings, "default_lock_settings", $data );
	}

	public static function getStatusSettings( $published = 1 ) {
		$options = array(
			array(
				"value" => 1,
				"text"  => __( "Published", "plugnmeet" )
			),
			array(
				"value" => 0,
				"text"  => __( "Unpublished", "plugnmeet" )
			)
		);

		$html  = '<tr>';
		$html .= '<th scope="row"><label for="published">' . __( "Room Status", "plugnmeet" ) . '</label></th>';
		$html .= '<td>';
		$html .= '<select id="published" name="published" >';

		foreach ( $options as $option ) {
			if ( $published == $option['value'] ) {
				$html .= '<option value="' . $option['value'] . '" selected = "selected">' . $option['text'] . '</option>';
			} else {
				$html .= '<option value="' . $option['value'] . '" >' . $option['text'] . '</option>';
			}
		}

		$html .= '</select>';
		$html .= plugnmeet_help_text_html( 'basic.published' );
		$html .= '</td></tr>';

		return wp_kses( $html, self::$allowedHtml );
	}

	/**
	 * @param $title
	 * @param $name
	 * @param $options array(array("text" => "test", "value" => "value"))
	 * @param $default
	 *
	 * @return string
	 */
	public static function formatSelectOptions( string $title, string $name, array $options, $default = null ) {
		$html = '<tr>';
		$html .= '<th scope="row">' . $title . '</th>';
		$html .= '<td>';
		$html .= '<select name="' . $name . '" >';

		foreach ( $options as $option ) {
			$selected = null;
			if ( $default == $option['value'] ) {
				$selected = 'selected = "selected"';
			}
			$html .= '<option ' . $selected . ' value="' . $option['value'] . '" >' . $option['text'] . '</option>';
		}

		$html .= '</select></td></tr>';

		return wp_kses( $html, self::$allowedHtml );
	}
}
