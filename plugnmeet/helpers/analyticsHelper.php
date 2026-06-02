<?php

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;

class Plugnmeet_AnalyticsHelper
{
    private $analyticsformatter;
    private $roomdata = [];
    private $usersdata = [];
    private $artifact_id;
    private $setting_params;

    public function __construct($artifact_id)
    {
        $this->artifact_id = $artifact_id;
        $this->setting_params = (object) get_option("plugnmeet_settings");

        if (!class_exists("plugNmeetConnect")) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'helpers/plugNmeetConnect.php';
        }

        $pnc = new plugNmeetConnect($this->setting_params);
        $res = $pnc->getArtifactDownloadToken($this->artifact_id);

        if ($res->getStatus()) {
            $analyticsdata = $this->fetch_data($res->getToken());
            if (!empty($analyticsdata)) {
                $data = json_decode($analyticsdata, true);
                if (!empty($data)) {
                    $analyticsdata = $data;
                }
            }
        } else {
            throw new Exception($res->getMsg());
        }

        $this->analyticsformatter = plugNmeetConnect::getAnalyticsFormatter($analyticsdata, wp_timezone()->getName());
        $formatteddata = $this->analyticsformatter->getFormattedEventData();
        $this->roomdata = $formatteddata['room'];
        $this->usersdata = $formatteddata['users'];
    }

    private function fetch_data($token)
    {
        $server_url = rtrim($this->setting_params->plugnmeet_server_url, '/');
        $host = $server_url . "/download/artifact/" . $token;

        $response = wp_remote_get($host, array('timeout' => 60));

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        return wp_remote_retrieve_body($response);
    }

    public function get_context_data()
    {
        $context = [
            'room_details' => [],
            'has_users' => false,
            'user_headers' => [],
            'user_rows' => [],
        ];

        $roomfields = $this->get_room_fields();
        $userfields = $this->get_user_fields();
        $room_labels = $this->get_room_analytics_labels();
        $user_labels = $this->get_user_analytics_labels();

        foreach ($roomfields as $field) {
            $value = $this->roomdata[$field] ?? 0;
            if (is_array($value)) {
                continue;
            }

            if ($field === "room_duration" || $field === "speech_service_total_usage") {
                $value = $this->format_seconds_to_time($value);
            } else if ($field === "enabled_e2ee") {
                $value = $value ? __('Yes', 'plugnmeet') : __('No', 'plugnmeet');
            }
            $context['room_details'][] = ['label' => $room_labels[$field] ?? $field, 'value' => $value];
        }

        if (!empty($this->usersdata)) {
            $context['has_users'] = true;
            $context['user_headers'] = array_map(function ($field) use ($user_labels) {
                return $user_labels[$field] ?? $field;
            }, $userfields);

            foreach ($this->usersdata as $userrow) {
                $rowdata = [];
                foreach ($userfields as $field) {
                    $value = $userrow[$field] ?? 0;
                    if (is_bool($value)) {
                        $value = $value ? __('Yes', 'plugnmeet') : __('No', 'plugnmeet');
                    } else if (is_array($value)) {
                        if ($field === "joined" || $field === "left") {
                            $arr = array_map(function ($d) {
                                return $this->format_timestamp($d);
                            }, empty($value) ? [] : $value);
                            $value = implode("<br><br>", $arr);
                        } else if ($field === "connection_quality") {
                            $connection_labels = $this->get_connection_quality_labels();
                            $arr = array_map(function ($k, $v) use ($connection_labels) {
                                return ($connection_labels[$k] ?? $k) . ": " . $v;
                            }, array_keys(empty($value) ? [] : $value), array_values(empty($value) ? [] : $value));
                            $value = implode("<br>", $arr);
                        } else {
                            $value = __('See Excel Report', 'plugnmeet');
                        }
                    } else if ($field === "duration" || $field === "talked_duration" || $field === "speech_service_total_usage") {
                        $value = $this->format_seconds_to_time($value);
                    }
                    $rowdata[] = ['value' => $value];
                }
                $context['user_rows'][] = ['data' => $rowdata];
            }
        }

        return $context;
    }

    public function generate_xlsx_file()
    {
        $spreadsheet = new Spreadsheet();
        $header_style = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1171A3'],
                'size' => 12,
            ],
        ];

        $this->format_room_xlsx($spreadsheet, $header_style);
        $this->format_users_xlsx($spreadsheet, $header_style);
        $this->format_polls_xlsx($spreadsheet, $header_style);
        $this->format_whiteboard_files_xlsx($spreadsheet, $header_style);

        $writer = new Xlsx($spreadsheet);
        $upload_dir = wp_upload_dir();
        $filename = 'plugnmeet_analytics_' . $this->artifact_id . '.xlsx';
        $path = $upload_dir['path'] . '/' . $filename;
        $writer->save($path);

        return ['path' => $path, 'url' => $upload_dir['url'] . '/' . $filename];
    }

    private function format_room_xlsx($spreadsheet, $header_style)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('Room Info', 'plugnmeet'));

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);

        $row_index = 1;
        $room_labels = $this->get_room_analytics_labels();

        foreach ($this->analyticsformatter->getRoomFields() as $field) {
            $data = $this->roomdata[$field] ?? 0;
            $title = $room_labels[$field] ?? $field;
            $sheet->getCell('A' . $row_index)->setValue($title);
            $sheet->getStyle('A' . $row_index)->applyFromArray($header_style);
            $formatted_data = $this->format_room_data_for_xlsx($data, $field);
            $sheet->getCell('B' . $row_index)->setValue((string)$formatted_data);
            $row_index++;
        }
    }

    private function format_users_xlsx($spreadsheet, $header_style)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle(__('Users Info', 'plugnmeet'));

        $column_map = [];
        $column_index = 'A';
        $user_labels = $this->get_user_analytics_labels();

        foreach ($this->analyticsformatter->getUserFields() as $field) {
            $column_map[$field] = $column_index++;
        }

        foreach ($column_map as $field => $col_index) {
            $sheet->getColumnDimension($col_index)->setWidth(25);
            $title = $user_labels[$field] ?? $field;
            $sheet->getCell($col_index . '1')->setValue($title);
            $sheet->getStyle($col_index . '1')->applyFromArray($header_style);
        }

        $row_index = 2;
        foreach ($this->usersdata as $user) {
            foreach ($column_map as $field => $col_index) {
                $data = $user[$field] ?? 0;
                $formatted_data = $this->format_user_data_for_xlsx($data, $field);
                $sheet->getCell($col_index . $row_index)->setValue((string)$formatted_data);
            }
            $row_index++;
        }
    }

    private function format_polls_xlsx($spreadsheet, $header_style)
    {
        if (empty($this->roomdata["polls"])) {
            return;
        }
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle(__('Polls', 'plugnmeet'));

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(30);

        $sheet->getCell('A1')->setValue(__('Question', 'plugnmeet'));
        $sheet->getCell('B1')->setValue(__('Options', 'plugnmeet'));
        $sheet->getCell('C1')->setValue(__('Created At', 'plugnmeet'));
        $sheet->getStyle('A1:C1')->applyFromArray($header_style);

        $i = 2;
        foreach ($this->roomdata["polls"] as $poll) {
            $sheet->getCell('A' . $i)->setValue($poll["question"]);
            $sheet->getCell('C' . $i)->setValue($poll["created"]);

            $arr  = array_map(function ($v) {
                return $v["text"] . ": " . $v["responses"];
            }, $poll["options"]);
            $data = implode("\n", $arr);
            $sheet->getCell('B' . $i)->setValue($data);
            $sheet->getStyle('B' . $i)->getAlignment()->setWrapText(true);

            $i++;
        }
    }

    private function format_whiteboard_files_xlsx($spreadsheet, $header_style)
    {
        if (empty($this->roomdata["whiteboard_files"])) {
            return;
        }
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle(__('Whiteboard Files', 'plugnmeet'));

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(30);

        $sheet->getCell('A1')->setValue(__('File Name', 'plugnmeet'));
        $sheet->getCell('B1')->setValue(__('Created At', 'plugnmeet'));
        $sheet->getStyle('A1:B1')->applyFromArray($header_style);

        $i = 2;
        foreach ($this->roomdata["whiteboard_files"] as $file) {
            $created = $this->analyticsformatter->formatTimestamp($file["time"]);

            $sheet->getCell('A' . $i)->setValue($file["value"]);
            $sheet->getCell('B' . $i)->setValue($created);

            $i++;
        }
    }

    private function format_room_data_for_xlsx($data, $field)
    {
        if ($field === "room_duration" || $field === "speech_service_total_usage") {
            return $this->analyticsformatter->formatSecondsToTime($data);
        }

        if (is_bool($data) || $field === "enabled_e2ee") {
            return $data ? __('Yes', 'plugnmeet') : __('No', 'plugnmeet');
        }

        return $data;
    }

    private function format_user_data_for_xlsx($data, $field)
    {
        if ($field === "joined" || $field === "left") {
            if (empty($data)) {
                return 0;
            }
            $arr = array_map(function ($d) {
                return $this->analyticsformatter->formatTimestamp($d);
            }, (array)$data);

            return implode("\n", $arr);
        }

        if ($field === "connection_quality") {
            if (empty($data)) {
                return 0;
            }
            $connection_labels = $this->get_connection_quality_labels();
            $arr = array_map(function ($k, $v) use ($connection_labels) {
                return ($connection_labels[$k] ?? $k) . ": " . $v;
            }, array_keys((array)$data), array_values((array)$data));

            return implode("\n", $arr);
        }

        if (
            $field === "duration" || $field === "talked_duration" || $field === "speech_service_total_usage"
            || $field === "webcam_duration" || $field === "mic_duration"
        ) {
            return $this->analyticsformatter->formatSecondsToTime($data);
        }

        if (is_bool($data)) {
            return $data ? __('Yes', 'plugnmeet') : __('No', 'plugnmeet');
        }

        return $data;
    }

    public function get_formatted_event_data()
    {
        return [
            "room"  => $this->roomdata,
            "users" => $this->usersdata,
        ];
    }

    public function get_room_fields()
    {
        return $this->analyticsformatter->getRoomFields();
    }

    public function get_user_fields()
    {
        return $this->analyticsformatter->getUserFields();
    }

    public function format_seconds_to_time($seconds)
    {
        return $this->analyticsformatter->formatSecondsToTime($seconds);
    }

    public function format_timestamp($timestamp, $ms = true)
    {
        return $this->analyticsformatter->formatTimestamp($timestamp, $ms);
    }

    private function get_room_analytics_labels() {
        return [
            'room_id' => __('Room ID', 'plugnmeet'),
            'room_title' => __('Room Title', 'plugnmeet'),
            'room_creation' => __('Room Creation Time', 'plugnmeet'),
            'room_ended' => __('Room Ended Time', 'plugnmeet'),
            'room_duration' => __('Room Duration', 'plugnmeet'),
            'room_total_users' => __('Total Participants', 'plugnmeet'),
            'enabled_e2ee' => __('E2EE Enabled', 'plugnmeet'),
            'recording_status' => __('Recording Status Count', 'plugnmeet'),
            'rtmp_status' => __('RTMP Status Count', 'plugnmeet'),
            'speech_service_total_usage' => __('Speech Service Total Usage', 'plugnmeet'),
            'external_media_player_status' => __('External Media Player Status Count', 'plugnmeet'),
            'etherpad_status' => __('Etherpad Status Count', 'plugnmeet'),
            'external_display_link_status' => __('External Display Link Status Count', 'plugnmeet'),
            'ingress_created' => __('Ingress Created Count', 'plugnmeet'),
            'breakout_room' => __('Breakout Room Count', 'plugnmeet'),
        ];
    }

    private function get_user_analytics_labels() {
        return [
            'name' => __('Name', 'plugnmeet'),
            'id' => __('User ID', 'plugnmeet'),
            'ex_id' => __('User ID', 'plugnmeet'),
            'is_admin' => __('Is Admin', 'plugnmeet'),
            'duration' => __('Duration', 'plugnmeet'),
            'joined' => __('Joined At', 'plugnmeet'),
            'left' => __('Left At', 'plugnmeet'),
            'mic_status' => __('Mic Status Changes', 'plugnmeet'),
            'mic_muted' => __('Mic Muted Count', 'plugnmeet'),
            'mic_duration' => __('Mic Enabled Duration', 'plugnmeet'),
            'talked' => __('Talked Count', 'plugnmeet'),
            'talked_duration' => __('Talked Duration', 'plugnmeet'),
            'webcam_status' => __('Webcam Status Changes', 'plugnmeet'),
            'webcam_duration' => __('Webcam Enabled Duration', 'plugnmeet'),
            'raise_hand' => __('Raise Hand Count', 'plugnmeet'),
            'voted_poll' => __('Voted Poll Count', 'plugnmeet'),
            'whiteboard_annotated' => __('Whiteboard Annotated Count', 'plugnmeet'),
            'whiteboard_files' => __('Whiteboard Files Count', 'plugnmeet'),
            'screen_share_status' => __('Screen Share Status Changes', 'plugnmeet'),
            'speech_services_usage' => __('Speech Services Usage', 'plugnmeet'),
            'public_chat' => __('Public Chat Messages', 'plugnmeet'),
            'private_chat' => __('Private Chat Messages', 'plugnmeet'),
            'chat_files' => __('Chat Files Shared', 'plugnmeet'),
            'interface_invisible' => __('Interface Invisible Count', 'plugnmeet'),
            'connection_quality' => __('Connection Quality', 'plugnmeet'),
        ];
    }

    private function get_connection_quality_labels() {
        return [
            'excellent' => __('Excellent', 'plugnmeet'),
            'good' => __('Good', 'plugnmeet'),
            'poor' => __('Poor', 'plugnmeet'),
        ];
    }
}
