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
                    if ($option == $data[$key]) {
                        $selected = "selected";
                    } else {
                        /*if($option == $item["selected"]){
                            $selected = "selected";
                        }*/
                    }
                } else {
                    if ($option == $item["selected"]) {
                        $selected = "selected";
                    }
                }
                $html .= "<option value=\"{$option}\" {$selected}>{$option}</option>";
            }

            $html .= '</select></td></tr>';
        }

        return $html;
    }

    public static function getRoomFeatures($room_metadata)
    {
        $roomFeatures = array(
            "allow_webcams" => array(
                "label" => __("allow_webcams", "plugnmeet"),
                "des" => __("allow_webcams_des", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "mute_on_start" => array(
                "label" => __("mute_on_start", "plugnmeet"),
                "des" => __("MUTE_ON_START_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "false"
            ),
            "allow_screen_share" => array(
                "label" => __("allow_screen_share", "plugnmeet"),
                "des" => __("ALLOW_SCREEN_SHARING_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "allow_recording" => array(
                "label" => __("allow_recording", "plugnmeet"),
                "des" => __("ALLOW_RECORDING_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "allow_rtmp" => array(
                "label" => __("allow_rtmp", "plugnmeet"),
                "des" => __("ALLOW_RTMP_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "allow_view_other_webcams" => array(
                "label" => __("allow_view_other_webcams", "plugnmeet"),
                "des" => __("ALLOW_VIEW_OTHER_WEBCAMS_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "allow_view_other_users_list" => array(
                "label" => __("allow_view_other_users_list", "plugnmeet"),
                "des" => __("ALLOW_VIEW_OTHER_USERS_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "admin_only_webcams" => array(
                "label" => __("admin_only_webcams", "plugnmeet"),
                "des" => __("ADMIN_ONLY_WEBCAMS_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "false"
            ),
        );

        $data = [];
        if (isset($room_metadata->room_features)) {
            $data = (array)$room_metadata->room_metadata->room_features;
        }

        return self::formatHtml($roomFeatures, "room_features", $data);
    }

    public static function getChatFeatures($room_metadata)
    {
        $chatFeatures = array(
            "allow_chat" => array(
                "label" => __("allow_chat", "plugnmeet"),
                "des" => __("ALLOW_CHAT_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "allow_file_upload" => array(
                "label" => __("allow_file_upload", "plugnmeet"),
                "des" => __("ALLOW_FILE_UPLOAD_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
        );

        $data = [];
        if (isset($room_metadata->chat_features)) {
            $data = (array)$room_metadata->chat_features;
        }

        return self::formatHtml($chatFeatures, "chat_features", $data);
    }

    public static function getDefaultLockSettings($room_metadata)
    {
        $defaultLockSettings = array(
            "lock_microphone" => array(
                "label" => __("lock_microphone", "plugnmeet"),
                "des" => __("LOCK_MICROPHONE_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "false"
            ),
            "lock_webcam" => array(
                "label" => __("lock_webcam", "plugnmeet"),
                "des" => __("LOCK_WEBCAM_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "false"
            ),
            "lock_screen_sharing" => array(
                "label" => __("lock_screen_sharing", "plugnmeet"),
                "des" => __("LOCK_SCREEN_SHARING_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "true"
            ),
            "lock_chat" => array(
                "label" => __("lock_chat", "plugnmeet"),
                "des" => __("LOCK_CHAT_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "false"
            ),
            "lock_chat_send_message" => array(
                "label" => __("lock_chat_send_message", "plugnmeet"),
                "des" => __("LOCK_CHAT_SEND_MESSAGE_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "false"
            ),
            "lock_chat_file_share" => array(
                "label" => __("lock_chat_file_share", "plugnmeet"),
                "des" => __("LOCK_CHAT_SEND_FILE_DES", "plugnmeet"),
                "options" => array("true", "false"),
                "selected" => "false"
            ),
        );

        $data = [];
        if (isset($room_metadata->default_lock_settings)) {
            $data = (array)$room_metadata->default_lock_settings;
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
