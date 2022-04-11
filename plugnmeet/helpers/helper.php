<?php
/**
 *
 * @since      1.0.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/helpers
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

class PlugnmeetHelper
{
    private static $allowedHtml = array(
        'select' => array(
            'id' => array(),
            'name' => array(),
            'value' => array(),
            'class' => array(),
        ),
        'option' => array(
            'value' => array(),
            'selected' => array(),
        ),
        'tr' => array(),
        'th' => array(
            'scope' => array()
        ),
        'td' => array(
            'scope' => array()
        )
    );

    public static function secureRandomKey(int $length = 36): string
    {
        $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    private static function formatHtml($items, $fieldName, $data)
    {
        $html = "";
        foreach ($items as $key => $item) {
            $html .= '<tr>';
            $html .= '<th scope="row">' . $item['label'] . '</th>';
            $html .= '<td>';
            $html .= "<select name=\"{$fieldName}[{$key}]\" class=\"list_class\">";
            foreach ($item["options"] as $option) {
                $selected = "";
                if (!empty($data)) {
                    if ($option['value'] == $data[$key]) {
                        $selected = "selected";
                    }
                } else {
                    if ($option['value'] == $item["selected"]) {
                        $selected = "selected";
                    }
                }
                $html .= '<option value="' . esc_attr($option['value']) . '" ' . $selected . '>' . esc_attr($option['label']) . '</option>';
            }

            $html .= '</select></td></tr>';
        }

        return wp_kses($html, self::$allowedHtml);
    }

    public static function getRoomFeatures($room_features)
    {
        $roomFeatures = array(
            "allow_webcams" => array(
                "label" => __("allow_webcams", "plugnmeet"),
                "des" => __("allow_webcams_des", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "mute_on_start" => array(
                "label" => __("mute_on_start", "plugnmeet"),
                "des" => __("MUTE_ON_START_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 0
            ),
            "allow_screen_share" => array(
                "label" => __("allow_screen_share", "plugnmeet"),
                "des" => __("ALLOW_SCREEN_SHARING_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "allow_recording" => array(
                "label" => __("allow_recording", "plugnmeet"),
                "des" => __("ALLOW_RECORDING_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "allow_rtmp" => array(
                "label" => __("allow_rtmp", "plugnmeet"),
                "des" => __("ALLOW_RTMP_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "allow_view_other_webcams" => array(
                "label" => __("allow_view_other_webcams", "plugnmeet"),
                "des" => __("ALLOW_VIEW_OTHER_WEBCAMS_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "allow_view_other_users_list" => array(
                "label" => __("allow_view_other_users_list", "plugnmeet"),
                "des" => __("ALLOW_VIEW_OTHER_USERS_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "admin_only_webcams" => array(
                "label" => __("admin_only_webcams", "plugnmeet"),
                "des" => __("ADMIN_ONLY_WEBCAMS_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 0
            ),
        );

        $data = [];
        if (!empty($room_features)) {
            $data = $room_features;
        }

        return self::formatHtml($roomFeatures, "room_features", $data);
    }

    public static function getChatFeatures($chat_features)
    {
        $chatFeatures = array(
            "allow_chat" => array(
                "label" => __("allow_chat", "plugnmeet"),
                "des" => __("ALLOW_CHAT_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "allow_file_upload" => array(
                "label" => __("allow_file_upload", "plugnmeet"),
                "des" => __("ALLOW_FILE_UPLOAD_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
        );

        $data = [];
        if (!empty($chat_features)) {
            $data = $chat_features;
        }

        return self::formatHtml($chatFeatures, "chat_features", $data);
    }

    public static function getSharedNotePadFeatures($sharedNotePad_features)
    {
        $sharedNotePadFeatures = array(
            "allowed_shared_note_pad" => array(
                "label" => __("allow_shared_notepad", "plugnmeet"),
                "des" => __("allow_shared_notepad_des", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            )
        );

        $data = [];
        if (!empty($sharedNotePad_features)) {
            $data = $sharedNotePad_features;
        }

        return self::formatHtml($sharedNotePadFeatures, "shared_note_pad_features", $data);
    }

    public static function getWhiteboardFeatures($whiteboard_features)
    {
        $whiteboardFeatures = array(
            "allowed_whiteboard" => array(
                "label" => __("allow_whiteboard", "plugnmeet"),
                "des" => __("allow_whiteboard_des", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            )
        );

        $data = [];
        if (!empty($whiteboard_features)) {
            $data = $whiteboard_features;
        }

        return self::formatHtml($whiteboardFeatures, "whiteboard_features", $data);
    }

    public static function getDefaultLockSettings($default_lock_settings)
    {
        $defaultLockSettings = array(
            "lock_microphone" => array(
                "label" => __("lock_microphone", "plugnmeet"),
                "des" => __("LOCK_MICROPHONE_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 0
            ),
            "lock_webcam" => array(
                "label" => __("lock_webcam", "plugnmeet"),
                "des" => __("LOCK_WEBCAM_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 0
            ),
            "lock_screen_sharing" => array(
                "label" => __("lock_screen_sharing", "plugnmeet"),
                "des" => __("LOCK_SCREEN_SHARING_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "lock_whiteboard" => array(
                "label" => __("lock_whiteboard", "plugnmeet"),
                "des" => __("lock_whiteboard_des", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "lock_shared_notepad" => array(
                "label" => __("lock_shared_notepad", "plugnmeet"),
                "des" => __("lock_shared_notepad_des", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 1
            ),
            "lock_chat" => array(
                "label" => __("lock_chat", "plugnmeet"),
                "des" => __("LOCK_CHAT_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 0
            ),
            "lock_chat_send_message" => array(
                "label" => __("lock_chat_send_message", "plugnmeet"),
                "des" => __("LOCK_CHAT_SEND_MESSAGE_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 0
            ),
            "lock_chat_file_share" => array(
                "label" => __("lock_chat_file_share", "plugnmeet"),
                "des" => __("LOCK_CHAT_SEND_FILE_DES", "plugnmeet"),
                "options" => array(
                    array(
                        "label" => __("Yes", "plugnmeet"),
                        "value" => 1
                    ), array(
                        "label" => __("No", "plugnmeet"),
                        "value" => 0
                    )),
                "selected" => 0
            ),
        );

        $data = [];
        if (!empty($default_lock_settings)) {
            $data = (array)$default_lock_settings;
        }

        return self::formatHtml($defaultLockSettings, "default_lock_settings", $data);
    }

    public static function getStatusSettings($published = 1)
    {
        $options = array(
            array(
                "value" => 1,
                "text" => "Published"
            ),
            array(
                "value" => 0,
                "text" => "Unpublished"
            )
        );

        $html = '<tr>';
        $html .= '<th scope="row">' . __("Room Status", "plugnmeet") . '</th>';
        $html .= '<td>';
        $html .= '<select id="published" name="published" >';

        foreach ($options as $option) {
            if ($published == $option['value']) {
                $html .= '<option value="' . $option['value'] . '" selected = "selected">' . $option['text'] . '</option>';
            } else {
                $html .= '<option value="' . $option['value'] . '" >' . $option['text'] . '</option>';
            }
        }

        $html .= '</select></td></tr>';
        return $html;
    }
}
