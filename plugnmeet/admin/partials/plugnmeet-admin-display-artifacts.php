<?php
/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.mynaparrot.com
 * @since      1.0.0
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin/partials
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}
?>

<div class="wrap plugnmeet-artifacts">
    <div class="room-selector">
        <select name="roomId" id="plugnmeet-selected-roomId">
            <option value=""><?php echo __("Select room", "plugnmeet") ?></option>
            <?php foreach ($rooms as $room): ?>
                <option value="<?php echo esc_attr($room->room_id); ?>"><?php echo esc_html($room->room_title); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="button button-primary"
                id="plugnmeet-show-artifacts"><?php echo __("Show artifacts", "plugnmeet") ?></button>
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list posts" id="artifactLists"
           style="margin-top: 50px">
        <thead>
        <tr>
            <th class="manage-column column-primary">
                <?php echo __("Artifact ID", "plugnmeet"); ?>
            </th>
            <th class="manage-column column-categories">
                <?php echo __("File Name", "plugnmeet"); ?>
            </th>
            <th class="manage-column column-categories">
                <?php echo __("File Size (MB)", "plugnmeet"); ?>
            </th>
            <th class="manage-column" style="width: 22%">
                <?php echo __("Creation Date", "plugnmeet"); ?>
            </th>
        </tr>
        </thead>
        <tbody id="artifactListsBody"></tbody>
    </table>
    <div id="artifactListsFooter" class="alignright actions" style="display: none"></div>
</div>
