<?php

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

class PlugNmeetOnAfterUpdate {

    public function __construct() {
        $this->databaseTableUpdate();
    }

    private function databaseTableUpdate() {
        global $wpdb;

        if (version_compare(PLUGNMEET_VERSION, '1.0.11', '=')) {
            $column = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
                DB_NAME, $wpdb->prefix . 'plugnmeet_rooms', 'roles'
            ));
            if (empty($column)) {
                $sql = 'ALTER TABLE `' . $wpdb->prefix . 'plugnmeet_rooms` ADD `roles` TEXT NOT NULL AFTER `room_metadata`;';
                $wpdb->query($sql);
            }
        }
    }
}
