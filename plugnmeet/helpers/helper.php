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
			'autocomplete' => array(),
			'placeholder'  => array(),
			'min'          => array(),
			'max'          => array(),
			'step'         => array(),
			'maxlength'    => array(),
			'checked'      => array()
		),
		'textarea' => array(
			'name'        => array(),
			'cols'        => array(),
			'rows'        => array(),
			'id'          => array(),
			'class'       => array(),
			'placeholder' => array()
		),
		'div'      => array(
			'id'    => array(),
			'class' => array()
		),
		'span'     => array(
			'id'    => array(),
			'class' => array()
		),
		'button'   => array(
			'type'       => array(),
			'id'         => array(),
			'class'      => array(),
			'aria-label' => array()
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
		"insights_features.transcription_features"    => "Transcription",
		"insights_features.chat_translation_features" => "Chat translation",
		"insights_features.ai_features"               => "AI features",
		// empty title means: render the nested fields without a header row
		"insights_features.ai_agent_features.auto_session_config" => "",
		"insights_features.ai_agent_features.avatar"          => "",
		"insights_features.ai_agent_features.activity_rules"  => "",
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
					if ( '' !== self::$sectionTitles[ $newDottedPath ] ) {
						$html .= '<tr class="pnm-subsection-title"><th scope="row" colspan="2">' . __( self::$sectionTitles[ $newDottedPath ], "plugnmeet" ) . '</th></tr>';
					}
				} else {
					$html .= '<tr class="pnm-subsection-divider"><th scope="row" colspan="2"></th></tr>';
				}
				$html .= self::formatHtml( $item, $newFieldName, $newData, true, $newDottedPath );
			} elseif ( isset( $item['type'] ) ) {
				$composedPath = ( '' === $dottedPath ? $fieldName : $dottedPath ) . '.' . $key;
				$fieldId      = self::buildFieldId( $composedPath );
				$helpText     = plugnmeet_help_text_html( $composedPath );
				$rowClass     = isset( $item['row_class'] ) ? ' class="' . esc_attr( $item['row_class'] ) . '"' : '';

				if ( $item["type"] === "section" ) {
					$html .= '<tr' . ( '' === $rowClass ? ' class="pnm-subsection-title"' : ' class="pnm-subsection-title ' . esc_attr( $item['row_class'] ) . '"' ) . '><th scope="row" colspan="2">' . esc_html( $item['label'] ) . '</th></tr>';
				} elseif ( $item["type"] === "raw" ) {
					$html .= $item['html'];
				} elseif ( $item["type"] === "select" ) {
					$html .= '<tr' . $rowClass . '>';
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
					$inputClass = isset( $item['class'] ) ? $item['class'] : ( 'text' === $item["type"] ? 'regular-text' : '' );
					$attributes = '' === $inputClass ? '' : ' class="' . esc_attr( $inputClass ) . '"';
					foreach ( array( 'min', 'max', 'step', 'maxlength', 'placeholder' ) as $attribute ) {
						if ( isset( $item[ $attribute ] ) ) {
							$attributes .= ' ' . $attribute . '="' . esc_attr( $item[ $attribute ] ) . '"';
						}
					}

					$html .= '<tr' . $rowClass . '>';
					$html .= '<th scope="row"><label for="' . esc_attr( $fieldId ) . '">' . esc_attr( $item['label'] ) . '</label></th>';
					$html .= '<td>';
					$html .= '<input id="' . esc_attr( $fieldId ) . '" type="' . esc_attr( $item["type"] ) . '" name="' . $fieldName . '[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '"' . $attributes . '   autocomplete="off">';
					$html .= $helpText;
					$html .= '</td></tr>';
				} elseif ( $item["type"] === "textarea" ) {
					$value = $item["default"];
					if ( isset( $data[ $key ] ) ) {
						$value = $data[ $key ];
					}
					$rows        = isset( $item['rows'] ) ? ' rows="' . (int) $item['rows'] . '"' : '';
					$placeholder = isset( $item['placeholder'] ) ? ' placeholder="' . esc_attr( $item['placeholder'] ) . '"' : '';
					$class       = isset( $item['class'] ) ? $item['class'] : 'large-text';
					$html        .= '<tr' . $rowClass . '>';
					$html .= '<th scope="row"><label for="' . esc_attr( $fieldId ) . '">' . esc_attr( $item['label'] ) . '</label></th>';
					$html .= '<td>';
					$html .= '<textarea id="' . esc_attr( $fieldId ) . '" name="' . esc_attr( $fieldName ) . '[' . esc_attr( $key ) . ']" class="' . esc_attr( $class ) . '"' . $rows . $placeholder . '>' . esc_attr( $value ) . '</textarea>';
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
					"is_allow"             => array(
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
					"summarization_prompt" => array(
						"label"   => __( "Summary instructions", "plugnmeet" ),
						"default" => "",
						"rows"    => 4,
						"type"    => "textarea"
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

	public static function getRoomMode( $room_metadata ) {
		$mode = is_array( $room_metadata ) && isset( $room_metadata['room_mode'] ) ? $room_metadata['room_mode'] : 'conference';

		if ( 'ai_tutor' === $mode ) {
			return 'ai_agent';
		}

		return 'ai_agent' === $mode ? 'ai_agent' : 'conference';
	}

	public static function isAiAgentEnabled() {
		$settings = (array) get_option( 'plugnmeet_settings' );

		return ! isset( $settings['enable_ai_agent'] ) || '0' !== (string) $settings['enable_ai_agent'];
	}

	public static function getAiAgentPresets() {
		return array(
			'custom'            => array(
				'label'  => __( 'Custom instructions', 'plugnmeet' ),
				'prompt' => '',
			),
			'customer_support'  => array(
				'label'  => __( 'Customer Support & Product Specialist', 'plugnmeet' ),
				'prompt' => 'You are a friendly customer support specialist for our product. Greet the customer and ask what they need help with. Answer in short, clear steps and use the shared notepad to list them. If you do not know an answer, say so and offer the next best step. Ask one question at a time and confirm at the end that the issue is resolved.',
			),
			'sales_demo'        => array(
				'label'  => __( 'Interactive Product Demo & Onboarding Guide', 'plugnmeet' ),
				'prompt' => 'You are a product specialist giving an interactive demo. Start by asking about the visitor\'s goals, then walk through only the features that match those goals. Use the whiteboard to sketch the workflow (for example a poll with two options) and keep every step short. Invite questions after each step and finish with a clear recommendation and the next step to take.',
			),
			'tutor_whiteboard'  => array(
				'label'  => __( '1-on-1 Tutor & Concept Explainer', 'plugnmeet' ),
				'prompt' => 'You are a patient 1-on-1 tutor. Ask the learner what they want to understand, then explain the concept in small steps and use the whiteboard for diagrams and examples. Check understanding after each step with a short question, correct mistakes kindly, and stop when the learner can explain the concept back to you.',
			),
			'language_coach'    => array(
				'label'  => __( 'Language & Communication Coach', 'plugnmeet' ),
				'prompt' => 'You are a friendly language coach running a conversation practice session. Speak in the target language at a level the learner can follow, keep the conversation going with open questions, and gently repeat the correct form after a mistake. Introduce one new phrase per topic and give short, encouraging feedback at the end.',
			),
			'interview_screener' => array(
				'label'  => __( 'Candidate Interview & Skill Assessment', 'plugnmeet' ),
				'prompt' => 'You are conducting a structured screening interview. Ask one question at a time about the candidate\'s experience and the skills required for the role, and ask a follow-up when an answer is vague. Take notes in the shared notepad. Close the session by summarizing the candidate\'s strengths and the open points the hiring team should verify.',
			),
		);
	}

	public static function getAiAgentFeatures( $insights_features ) {
		$settings = (array) get_option( 'plugnmeet_settings' );
		$data     = array();
		if ( isset( $insights_features['ai_agent_features'] ) && is_array( $insights_features['ai_agent_features'] ) ) {
			$data = $insights_features['ai_agent_features'];
		}

		// Rooms saved before the switch to minutes only stored seconds.
		if ( ! isset( $data['max_session_minutes'] ) && ! empty( $data['max_session_seconds'] ) ) {
			$data['max_session_minutes'] = max( 1, (int) round( ( (int) $data['max_session_seconds'] ) / 60 ) );
		}

		$max_minutes_limit = isset( $settings['ai_max_session_minutes_limit'] ) ? min( 120, max( 1, (int) $settings['ai_max_session_minutes_limit'] ) ) : 60;
		$default_minutes   = isset( $settings['ai_default_max_session_minutes'] ) ? max( 1, min( $max_minutes_limit, (int) $settings['ai_default_max_session_minutes'] ) ) : min( 30, $max_minutes_limit );

		$presets = array();
		foreach ( self::getAiAgentPresets() as $value => $preset ) {
			$presets[] = array( 'label' => $preset['label'], 'value' => $value );
		}

		$fields = array(
			'is_allow'            => array(
				'label'     => __( 'Run AI agent in this room', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 0,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-main',
			),
			'session_mode'        => array(
				'label'     => __( 'Session mode', 'plugnmeet' ),
				'options'   => array(
					array( 'label' => __( 'Open session for each user', 'plugnmeet' ), 'value' => 2 ),
					array( 'label' => __( 'Controlled session for selected users', 'plugnmeet' ), 'value' => 1 ),
				),
				'selected'  => 2,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-session',
			),
			'auto_session_config' => array(
				'max_concurrent_sessions' => array(
					'label'     => __( 'Maximum concurrent sessions', 'plugnmeet' ),
					'default'   => 10,
					'min'       => 1,
					'max'       => isset( $settings['ai_max_concurrent_sessions_limit'] ) ? (int) $settings['ai_max_concurrent_sessions_limit'] : 10,
					'type'      => 'number',
					'row_class' => 'pnm-ai-agent-multi',
				),
				'allowed_participant_ids' => array(
					'type' => 'raw',
					'html' => self::allowedParticipantsRow( $data ),
				),
			),
		);

		if ( ! empty( $settings['ai_avatar_enabled_global'] ) ) {
			$fields['avatar'] = array(
				'is_enabled' => array(
					'label'     => __( 'Enable a talking avatar', 'plugnmeet' ),
					'options'   => self::yesNoOptions(),
					'selected'  => 0,
					'type'      => 'select',
					'row_class' => 'pnm-ai-agent-detail',
				),
			);
		}

		$fields = array_merge( $fields, array(
			'preset_template'     => array(
				'label'     => __( 'Preset instructions', 'plugnmeet' ),
				'options'   => $presets,
				'selected'  => 'custom',
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'agent_display_name'  => array(
				'label'     => __( 'Agent display name', 'plugnmeet' ),
				'default'   => __( 'AI Assistant', 'plugnmeet' ),
				'type'      => 'text',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'topic'               => array(
				'label'     => __( 'Session topic', 'plugnmeet' ),
				'default'   => '',
				'maxlength' => 200,
				'type'      => 'text',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'system_prompt'       => array(
				'label'     => __( 'Agent instructions', 'plugnmeet' ),
				'default'   => '',
				'rows'      => 6,
				'type'      => 'textarea',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'max_session_minutes' => array(
				'label'     => __( 'Maximum session duration (minutes)', 'plugnmeet' ),
				'default'   => $default_minutes,
				'min'       => 1,
				'max'       => $max_minutes_limit,
				'type'      => 'number',
				'row_class' => 'pnm-ai-agent-detail',
			),
		) );

		$fields = array_merge( $fields, array(
			'language_section'           => array(
				'type'      => 'section',
				'row_class' => 'pnm-ai-agent-detail',
				'label'     => __( 'Language & voice', 'plugnmeet' ),
			),
			'spoken_lang'                => array(
				'label'       => __( 'Spoken language', 'plugnmeet' ),
				'default'     => '',
				'placeholder' => 'en-US',
				'type'        => 'text',
				'row_class'   => 'pnm-ai-agent-detail',
			),
			'target_lang'                => array(
				'label'       => __( 'Reply language', 'plugnmeet' ),
				'default'     => '',
				'placeholder' => 'es-ES',
				'type'        => 'text',
				'row_class'   => 'pnm-ai-agent-detail',
			),
			'instruction_lang'           => array(
				'label'       => __( 'Instructions language', 'plugnmeet' ),
				'default'     => '',
				'placeholder' => 'en-US',
				'type'        => 'text',
				'row_class'   => 'pnm-ai-agent-detail',
			),
			'voice'                      => array(
				'label'     => __( 'Voice', 'plugnmeet' ),
				'default'   => '',
				'type'      => 'text',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'speaking_rate'              => array(
				'label'     => __( 'Speaking rate', 'plugnmeet' ),
				'default'   => '1.0',
				'min'       => 0.5,
				'max'       => 2.0,
				'step'      => 0.1,
				'type'      => 'number',
				'row_class' => 'pnm-ai-agent-detail',
			),
		) );

		$workspace = array(
			'workspace_section'      => array(
				'type'      => 'section',
				'row_class' => 'pnm-ai-agent-detail',
				'label'     => __( 'Workspace tools', 'plugnmeet' ),
			),
			'allow_whiteboard_write' => array(
				'label'     => __( 'Allow whiteboard writing', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 1,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'allow_vision_capture'   => array(
				'label'     => __( 'Allow whiteboard vision', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 1,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'allow_polls'            => array(
				'label'     => __( 'Allow polls', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 1,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'allow_notepad_write'    => array(
				'label'     => __( 'Allow notepad writing', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 1,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'allow_chat_message'     => array(
				'label'     => __( 'Allow chat messages', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 1,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			),
		);

		if ( ! empty( $settings['ai_allow_web_fetch_global'] ) ) {
			$workspace['allow_web_fetch']        = array(
				'label'     => __( 'Allow web fetch', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 0,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			);
			$workspace['allowed_knowledge_urls'] = array(
				'label'     => __( 'Knowledge URLs', 'plugnmeet' ),
				'default'   => '',
				'rows'      => 3,
				'type'      => 'textarea',
				'row_class' => 'pnm-ai-agent-detail',
			);
		}

		if ( self::hasEnabledMcpServer( $settings ) ) {
			$workspace['enable_mcp_servers'] = array(
				'label'     => __( 'Enable MCP servers', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 1,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			);
		}

		$fields = array_merge( $fields, $workspace, array(
			'guardrails_section'          => array(
				'type'      => 'section',
				'row_class' => 'pnm-ai-agent-detail',
				'label'     => __( 'Topic boundaries & guardrails', 'plugnmeet' ),
			),
			'allowed_subtopics'           => array(
				'label'     => __( 'Allowed subtopics', 'plugnmeet' ),
				'default'   => '',
				'rows'      => 3,
				'type'      => 'textarea',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'forbidden_topics'            => array(
				'label'     => __( 'Forbidden topics', 'plugnmeet' ),
				'default'   => '',
				'rows'      => 3,
				'type'      => 'textarea',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'strict_topic_enforcement'    => array(
				'label'     => __( 'Strict topic enforcement', 'plugnmeet' ),
				'options'   => self::yesNoOptions(),
				'selected'  => 1,
				'type'      => 'select',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'topic_anchor_interval_turns' => array(
				'label'     => __( 'Topic reminder interval (turns)', 'plugnmeet' ),
				'default'   => 5,
				'min'       => 3,
				'max'       => 12,
				'type'      => 'number',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'activity_rules_section'      => array(
				'label'     => __( 'Mandatory agent actions & milestones', 'plugnmeet' ),
				'type'      => 'section',
				'row_class' => 'pnm-ai-agent-detail',
			),
			'activity_rules'              => array(
				'min_whiteboard_drawings'   => array(
					'label'     => __( 'Minimum whiteboard drawings', 'plugnmeet' ),
					'default'   => 0,
					'min'       => 0,
					'max'       => 20,
					'type'      => 'number',
					'row_class' => 'pnm-ai-agent-detail',
				),
				'min_quizzes'               => array(
					'label'     => __( 'Minimum quizzes', 'plugnmeet' ),
					'default'   => 0,
					'min'       => 0,
					'max'       => 20,
					'type'      => 'number',
					'row_class' => 'pnm-ai-agent-detail',
				),
				'min_notepad_entries'       => array(
					'label'     => __( 'Minimum notepad entries', 'plugnmeet' ),
					'default'   => 0,
					'min'       => 0,
					'max'       => 20,
					'type'      => 'number',
					'row_class' => 'pnm-ai-agent-detail',
				),
				'min_participant_exercises' => array(
					'label'     => __( 'Minimum participant exercises', 'plugnmeet' ),
					'default'   => 0,
					'min'       => 0,
					'max'       => 20,
					'type'      => 'number',
					'row_class' => 'pnm-ai-agent-detail',
				),
				'required_milestones'       => array(
					'label'       => __( 'Required milestones (one per line)', 'plugnmeet' ),
					'default'     => '',
					'rows'        => 3,
					'placeholder' => __( "Understand the problem statement\nComplete a practice example", 'plugnmeet' ),
					'type'        => 'textarea',
					'row_class'   => 'pnm-ai-agent-detail',
				),
			),
		) );

		return self::formatHtml( $fields, 'insights_features[ai_agent_features]', $data, false, 'insights_features.ai_agent_features' );
	}

	private static function yesNoOptions() {
		return array(
			array( 'label' => __( 'Yes', 'plugnmeet' ), 'value' => 1 ),
			array( 'label' => __( 'No', 'plugnmeet' ), 'value' => 0 ),
		);
	}

	private static function hasEnabledMcpServer( $settings ) {
		for ( $i = 1; $i <= 3; $i++ ) {
			if ( ! empty( $settings[ "ai_mcp_{$i}_enabled" ] ) ) {
				return true;
			}
		}

		return false;
	}

	private static function allowedParticipantsRow( $data ) {
		$field_name = 'insights_features[ai_agent_features][auto_session_config][allowed_participant_ids]';
		$ids        = array();
		if ( isset( $data['auto_session_config']['allowed_participant_ids'] ) && is_array( $data['auto_session_config']['allowed_participant_ids'] ) ) {
			$ids = self::sanitizeUserIds( $data['auto_session_config']['allowed_participant_ids'] );
		}

		$chips = '';
		$count = 0;
		if ( $ids ) {
			foreach ( get_users( array( 'include' => $ids, 'number' => 32, 'orderby' => 'display_name' ) ) as $user ) {
				$chips .= '<span class="pnm-user-chip">';
				$chips .= '<span class="pnm-user-chip-label">' . esc_html( $user->display_name . ' (' . $user->user_email . ')' ) . '</span>';
				$chips .= '<button type="button" class="pnm-user-chip-remove" aria-label="' . esc_attr__( 'Remove user', 'plugnmeet' ) . '">&times;</button>';
				$chips .= '<input type="hidden" name="' . esc_attr( $field_name ) . '[]" value="' . esc_attr( $user->ID ) . '">';
				$chips .= '</span>';
				$count++;
			}
		}

		$html = '<tr class="pnm-ai-agent-participants">';
		$html .= '<th scope="row"><label for="pnm-selected-users">' . __( 'Allowed participants', 'plugnmeet' ) . '</label></th>';
		$html .= '<td>';
		$html .= '<div id="pnm-selected-users" class="pnm-selected-users">' . $chips . '</div>';
		$html .= '<p class="pnm-selected-users-meta">' . __( 'Selected users', 'plugnmeet' ) . ': <span id="pnm-selected-users-count">' . $count . '</span> / 32</p>';
		$html .= '<button type="button" class="button" id="pnm-select-users">' . __( '+ Select users', 'plugnmeet' ) . '</button> ';
		$html .= '<button type="button" class="button" id="pnm-clear-selected-users">' . __( 'Clear all', 'plugnmeet' ) . '</button>';
		$html .= plugnmeet_help_text_html( 'insights_features.ai_agent_features.auto_session_config.allowed_participant_ids' );
		$html .= '</td></tr>';

		return $html;
	}

	public static function sanitizeUserIds( $ids ) {
		if ( ! is_array( $ids ) ) {
			return array();
		}

		return array_slice( array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) ), 0, 32 );
	}

	/**
	 * Strip plugin-only keys and normalize the AI agent values for the PlugNmeet server.
	 *
	 * @param array $room_metadata Decoded room metadata, updated in place.
	 * @param array $settings Plugin settings.
	 */
	public static function prepareAiAgentFeatures( array &$room_metadata, array $settings ) {
		$room_mode       = self::getRoomMode( $room_metadata );
		$enable_ai_agent = ! isset( $settings['enable_ai_agent'] ) || '0' !== (string) $settings['enable_ai_agent'];

		unset( $room_metadata['room_mode'] );

		$insights = isset( $room_metadata['insights_features'] ) && is_array( $room_metadata['insights_features'] ) ? $room_metadata['insights_features'] : array();
		$ai       = isset( $insights['ai_agent_features'] ) && is_array( $insights['ai_agent_features'] ) ? $insights['ai_agent_features'] : array();

		$enable_mcp = ! isset( $ai['enable_mcp_servers'] ) || ! empty( $ai['enable_mcp_servers'] );
		unset( $ai['preset_template'], $ai['enable_mcp_servers'], $ai['is_enabled'], $ai['active_session_id'] );

		$is_ai_agent_mode = $enable_ai_agent && 'ai_agent' === $room_mode;
		$is_conference_ai = $enable_ai_agent && ! $is_ai_agent_mode && ! empty( $ai['is_allow'] );

		if ( ! $is_ai_agent_mode && ! $is_conference_ai ) {
			$insights['ai_agent_features'] = array( 'is_allow' => false );
			if ( $enable_mcp ) {
				self::injectTextChatMcpServers( $insights, $settings );
			}
			$room_metadata['insights_features'] = $insights;

			return;
		}

		// AI driven sessions always run with insights and breakout rooms enabled.
		$room_metadata['room_features'] = isset( $room_metadata['room_features'] ) && is_array( $room_metadata['room_features'] ) ? $room_metadata['room_features'] : array();
		$room_metadata['room_features']['auto_gen_user_id'] = false;
		$room_metadata['room_features']['breakout_room_features']['is_allow'] = true;
		$room_metadata['room_features']['end_to_end_encryption_features']['is_enabled'] = false;
		$room_metadata['room_features']['end_to_end_encryption_features']['enabled_self_insert_encryption_key'] = false;
		$room_metadata['breakout_room_features']['is_allow'] = true;
		$room_metadata['end_to_end_encryption_features']['enabled_self_insert_encryption_key'] = false;
		$insights['is_allow'] = true;
		$ai['is_allow']       = true;

		if ( $is_ai_agent_mode ) {
			$room_metadata['room_features']['moderator_join_first'] = false;

			// Workspace tools the agent needs to run a session on its own.
			$room_metadata['whiteboard_features']['is_allow']      = true;
			$room_metadata['shared_note_pad_features']['is_allow'] = true;
			$room_metadata['polls_features']['is_allow']           = true;
			$room_metadata['chat_features']['is_allow']            = true;
			$room_metadata['room_features']['enable_analytics']    = true;

			// 1 = controlled session for selected users, 2 = open session per user.
			$session_mode       = 1 === (int) ( $ai['session_mode'] ?? 2 ) ? 1 : 2;
			$ai['session_mode'] = $session_mode;

			if ( 1 === $session_mode ) {
				$allowed_ids = self::sanitizeUserIds( $ai['auto_session_config']['allowed_participant_ids'] ?? array() );

				$ai['auto_session_config'] = array(
					'allowed_participant_ids' => array_map( 'strval', $allowed_ids ),
					'auto_start_agent'        => true,
				);
			} else {
				$limit          = max( 1, (int) ( $settings['ai_max_concurrent_sessions_limit'] ?? 10 ) );
				$max_concurrent = (int) ( $ai['auto_session_config']['max_concurrent_sessions'] ?? 1 );

				$ai['auto_session_config'] = array(
					'max_concurrent_sessions' => max( 1, min( $limit, $max_concurrent ) ),
					'auto_start_agent'        => true,
				);
			}
		} else {
			// Normal room with conference AI: no autonomous session.
			$ai['session_mode'] = 0;
			unset( $ai['auto_session_config'] );
		}

		$ai['agent_display_name'] = trim( (string) ( $ai['agent_display_name'] ?? '' ) ) ?: __( 'AI Assistant', 'plugnmeet' );
		$ai['topic']              = mb_substr( trim( (string) ( $ai['topic'] ?? '' ) ), 0, 200 );
		$ai['system_prompt']      = trim( (string) ( $ai['system_prompt'] ?? '' ) );
		$ai['allowed_subtopics']  = self::parseStringList( $ai['allowed_subtopics'] ?? '', 64, 160 );
		$ai['forbidden_topics']   = self::parseStringList( $ai['forbidden_topics'] ?? '', 32, 160 );

		// Proto3 drops false booleans, so always send them explicitly.
		foreach ( array( 'strict_topic_enforcement', 'allow_whiteboard_write', 'allow_vision_capture', 'allow_polls', 'allow_notepad_write', 'allow_chat_message' ) as $key ) {
			$ai[ $key ] = ! isset( $ai[ $key ] ) || ! empty( $ai[ $key ] );
		}

		if ( ! empty( $settings['ai_allow_web_fetch_global'] ) && ! empty( $ai['allow_web_fetch'] ) ) {
			$ai['allow_web_fetch']        = true;
			$ai['allowed_knowledge_urls'] = self::parseUrlList( $ai['allowed_knowledge_urls'] ?? '' );
		} else {
			$ai['allow_web_fetch']        = false;
			$ai['allowed_knowledge_urls'] = array();
		}

		$ai['topic_anchor_interval_turns'] = (int) max( 3, min( 12, (int) ( $ai['topic_anchor_interval_turns'] ?? 6 ) ) );
		$speaking_rate                     = (float) ( $ai['speaking_rate'] ?? 1.0 );
		$ai['speaking_rate']               = max( 0.5, min( 2.0, $speaking_rate > 0 ? $speaking_rate : 1.0 ) );

		// One duration in minutes drives the session, the AI audio budget and the avatar budget.
		$minutes_limit   = min( 120, max( 1, (int) ( $settings['ai_max_session_minutes_limit'] ?? 60 ) ) );
		$default_minutes = max( 1, min( $minutes_limit, (int) ( $settings['ai_default_max_session_minutes'] ?? 30 ) ) );
		if ( isset( $ai['max_session_minutes'] ) ) {
			$session_minutes = (int) $ai['max_session_minutes'];
		} elseif ( isset( $ai['max_session_seconds'] ) ) {
			$session_minutes = (int) round( ( (int) $ai['max_session_seconds'] ) / 60 );
		} else {
			$session_minutes = $default_minutes;
		}
		$session_minutes = max( 1, min( $minutes_limit, $session_minutes ) );
		unset( $ai['max_session_minutes'] );
		$ai['max_session_seconds'] = $session_minutes * 60;
		$ai['max_audio_minutes']   = $session_minutes;

		if ( ! empty( $settings['ai_avatar_enabled_global'] ) && is_array( $ai['avatar'] ?? null ) && ! empty( $ai['avatar']['is_enabled'] ) ) {
			$avatar    = array(
				'is_enabled'         => true,
				'max_avatar_minutes' => $session_minutes,
			);
			$providers = array( 'beyond_presence', 'anam', 'simli' );

			$avatar_provider = ! empty( $settings['ai_avatar_custom_enabled'] )
				? (string) ( $settings['ai_avatar_provider'] ?? '' ) : '';
			if ( in_array( $avatar_provider, $providers, true ) ) {
				$avatar['provider'] = $avatar_provider;
			}

			if ( ! empty( $settings['ai_avatar_custom_enabled'] ) ) {
				$avatar_id = mb_substr( trim( (string) ( $settings['ai_avatar_id'] ?? '' ) ), 0, 256 );
				if ( '' !== $avatar_id ) {
					$avatar['avatar_id'] = $avatar_id;
				}
			}

			if ( ! empty( $settings['ai_avatar_custom_enabled'] ) && '' !== trim( (string) ( $settings['ai_avatar_api_key'] ?? '' ) ) ) {
				$avatar['api_key'] = trim( (string) $settings['ai_avatar_api_key'] );
				foreach ( array(
					'endpoint'    => 'ai_avatar_endpoint',
					'auth_header' => 'ai_avatar_auth_header',
				) as $key => $setting_key ) {
					$val = trim( (string) ( $settings[ $setting_key ] ?? '' ) );
					if ( '' !== $val ) {
						$avatar[ $key ] = $val;
					}
				}
			}

			$ai['avatar'] = $avatar;
		} else {
			unset( $ai['avatar'] );
		}

		// Activity quotas are meaningless without the matching tool.
		$rules         = isset( $ai['activity_rules'] ) && is_array( $ai['activity_rules'] ) ? $ai['activity_rules'] : array();
		$min_wb        = $ai['allow_whiteboard_write'] ? max( 0, min( 20, (int) ( $rules['min_whiteboard_drawings'] ?? 0 ) ) ) : 0;
		$min_quizzes   = $ai['allow_polls'] ? max( 0, min( 20, (int) ( $rules['min_quizzes'] ?? 0 ) ) ) : 0;
		$min_notepad   = $ai['allow_notepad_write'] ? max( 0, min( 20, (int) ( $rules['min_notepad_entries'] ?? 0 ) ) ) : 0;
		$min_exercises = max( 0, min( 20, (int) ( $rules['min_participant_exercises'] ?? 0 ) ) );
		$milestones    = self::parseStringList( $rules['required_milestones'] ?? '', 16, 240 );

		if ( $min_wb > 0 || $min_quizzes > 0 || $min_notepad > 0 || $min_exercises > 0 || ! empty( $milestones ) ) {
			$ai['activity_rules'] = array(
				'min_whiteboard_drawings'   => $min_wb,
				'min_quizzes'               => $min_quizzes,
				'min_notepad_entries'       => $min_notepad,
				'min_participant_exercises' => $min_exercises,
				'required_milestones'       => $milestones,
			);
		} else {
			unset( $ai['activity_rules'] );
		}

		$custom_provider = self::buildCustomProviderConfig( $settings );
		if ( $custom_provider ) {
			$ai['custom_provider'] = $custom_provider;
		} else {
			unset( $ai['custom_provider'] );
		}

		if ( $enable_mcp ) {
			$text_chat_enabled = ! empty( $insights['ai_features']['is_allow'] ) && ! empty( $insights['ai_features']['ai_text_chat_features']['is_allow'] );

			foreach ( self::buildMcpServersConfig( $settings ) as $name => $server ) {
				if ( in_array( $server['scope'], array( 'ai_agent', 'shared' ), true ) ) {
					$ai['mcp_servers'][ $name ] = $server['config'];
				}
				if ( $text_chat_enabled && in_array( $server['scope'], array( 'text_chat', 'shared' ), true ) ) {
					$insights['ai_features']['ai_text_chat_features']['mcp_servers'][ $name ] = $server['config'];
				}
			}
		}

		$insights['ai_agent_features']      = $ai;
		$room_metadata['insights_features'] = $insights;
	}

	/**
	 * Add the MCP servers of the text chat scope to an AI enabled conference room.
	 *
	 * @param array $insights Insights features, updated in place.
	 * @param array $settings Plugin settings.
	 */
	private static function injectTextChatMcpServers( array &$insights, array $settings ) {
		if ( empty( $insights['is_allow'] )
		     || empty( $insights['ai_features']['is_allow'] )
		     || empty( $insights['ai_features']['ai_text_chat_features']['is_allow'] ) ) {
			return;
		}

		foreach ( self::buildMcpServersConfig( $settings ) as $name => $server ) {
			if ( in_array( $server['scope'], array( 'text_chat', 'shared' ), true ) ) {
				$insights['ai_features']['ai_text_chat_features']['mcp_servers'][ $name ] = $server['config'];
			}
		}
	}

	/**
	 * Build the custom AI provider config from the site settings, if enabled.
	 *
	 * @param array $settings Plugin settings.
	 *
	 * @return array
	 */
	private static function buildCustomProviderConfig( array $settings ) {
		$api_key = trim( (string) ( $settings['ai_custom_api_key'] ?? '' ) );
		if ( empty( $settings['ai_custom_provider_enabled'] ) || '' === $api_key ) {
			return array();
		}

		$provider = (string) ( $settings['ai_custom_provider'] ?? '' );
		$config   = array(
			'provider' => in_array( $provider, array( 'google', 'openai' ), true ) ? $provider : 'google',
			'api_key'  => $api_key,
		);

		$optionals = array(
			'endpoint'         => 'ai_custom_endpoint',
			'auth_header'      => 'ai_custom_auth_header',
			'realtime_model'   => 'ai_custom_realtime_model',
			'delegation_model' => 'ai_custom_delegation_model',
			'summarize_model'  => 'ai_custom_summarize_model',
		);

		foreach ( $optionals as $key => $setting ) {
			$value = trim( (string) ( $settings[ $setting ] ?? '' ) );
			if ( '' !== $value ) {
				$config[ $key ] = $value;
			}
		}

		return $config;
	}

	/**
	 * Build the MCP server configs from the site settings, keyed by server name.
	 *
	 * @param array $settings Plugin settings.
	 *
	 * @return array Name => array( 'scope' => string, 'config' => array ).
	 */
	private static function buildMcpServersConfig( array $settings ) {
		$servers = array();

		for ( $i = 1; $i <= 3; $i++ ) {
			if ( empty( $settings[ "ai_mcp_{$i}_enabled" ] ) ) {
				continue;
			}

			$name = trim( (string) ( $settings[ "ai_mcp_{$i}_name" ] ?? '' ) );
			$url  = trim( (string) ( $settings[ "ai_mcp_{$i}_url" ] ?? '' ) );
			if ( '' === $name || mb_strlen( $name ) > 64 || ! str_starts_with( $url, 'https://' ) ) {
				continue;
			}

			$headers = array();
			$decoded = json_decode( (string) ( $settings[ "ai_mcp_{$i}_headers" ] ?? '' ), true );
			if ( is_array( $decoded ) ) {
				foreach ( $decoded as $header => $value ) {
					if ( count( $headers ) < 16 && is_scalar( $value ) && '' !== trim( (string) $header ) ) {
						$headers[ (string) $header ] = (string) $value;
					}
				}
			}

			$scope = (string) ( $settings[ "ai_mcp_{$i}_scope" ] ?? '' );

			$servers[ $name ] = array(
				'scope'  => in_array( $scope, array( 'ai_agent', 'text_chat', 'shared' ), true ) ? $scope : 'ai_agent',
				'config' => array(
					'url'           => $url,
					'headers'       => $headers,
					'allowed_tools' => self::parseStringList( $settings[ "ai_mcp_{$i}_allowed_tools" ] ?? '', 8 ),
				),
			);
		}

		return $servers;
	}

	/**
	 * Turn a comma/newline separated list into unique trimmed strings.
	 *
	 * @param mixed $value Raw value.
	 * @param int $max_items Maximum number of items to keep.
	 * @param int $max_length Truncate items to this length, 0 to keep them as is.
	 *
	 * @return array
	 */
	private static function parseStringList( $value, int $max_items, int $max_length = 0 ) {
		$items = is_array( $value ) ? $value : preg_split( '/[\r\n,]+/', (string) $value );
		$list  = array();

		foreach ( $items as $item ) {
			$item = trim( (string) $item );
			if ( '' === $item ) {
				continue;
			}
			$list[] = $max_length > 0 ? mb_substr( $item, 0, $max_length ) : $item;
		}

		return array_slice( array_values( array_unique( $list ) ), 0, $max_items );
	}

	/**
	 * Keep only unique and valid https knowledge URLs.
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return array
	 */
	private static function parseUrlList( $value ) {
		$items = is_array( $value ) ? $value : preg_split( '/[\r\n]+/', (string) $value );
		$urls  = array();

		foreach ( $items as $item ) {
			$item = trim( (string) $item );
			if ( str_starts_with( $item, 'https://' ) && mb_strlen( $item ) <= 1024 && ! in_array( $item, $urls, true ) ) {
				$urls[] = $item;
			}
		}

		return array_slice( $urls, 0, 32 );
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

		$html = '<tr>';
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
