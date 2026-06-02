<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.mynaparrot.com
 * @since      1.0.0
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin/partials
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
    die;
}
?>

<div class="wrap plugnmeet-recordings">
    <div class="room-selector">
        <select name="roomId" id="plugnmeet-selected-roomId">
            <option value=""><?php echo __( "Select room", "plugnmeet" ) ?></option>
            <?php foreach ( $rooms as $room ): ?>
                <option value="<?php echo esc_attr( $room->room_id ); ?>"><?php echo esc_html( $room->room_title ); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="button button-primary"
                id="plugnmeet-show-recordings"><?php echo __( "Show recordings", "plugnmeet" ) ?></button>
        <button class="button button-primary"
                id="plugnmeet-merge-recordings"
                style="display: none;"><?php echo __( "Merge Recordings", "plugnmeet" ) ?></button>
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list posts" id="recordingLists"
           style="margin-top: 50px">
        <thead>
        <tr>
            <td id="cb" class="manage-column column-cb check-column">
                <label class="screen-reader-text"
                       for="cb-select-all-1"><?php echo __( "Select All", "plugnmeet" ) ?></label>
            </td>
            <th class="manage-column column-primary">
                <?php echo __( "Record Id", "plugnmeet" ); ?>
            </th>
            <th class="manage-column column-categories">
                <?php echo __( "Recording date", "plugnmeet" ); ?>
            </th>
            <th class="manage-column column-categories">
                <?php echo __( "Meeting date", "plugnmeet" ); ?>
            </th>
            <th class="manage-column" style="width: 22%">
                <?php echo __( "File size (MB)", "plugnmeet" ); ?>
            </th>
        </tr>
        </thead>
        <tbody id="recordingListsBody"></tbody>
    </table>
    <div id="recordingListsFooter" class="alignright actions" style="display: none"></div>
</div>
<div id="plugnmeet-merge-confirm-modal" style="display: none;">
    <div class="plugnmeet-modal-backdrop"></div>
    <div class="plugnmeet-modal-content">
        <div class="plugnmeet-modal-header">
            <button type="button" class="plugnmeet-modal-close" id="plugnmeet-cancel-merge-top">&times;</button>
            <h4 class="modal-title"><?php echo __( "Confirm Merge", "plugnmeet" ) ?></h4>
        </div>
        <div class="plugnmeet-modal-body">
            <p><?php echo __( "Are you sure you want to merge the following recordings in this order?", "plugnmeet" ) ?></p>
            <ul id="plugnmeet-merge-list"></ul>
            <div id="plugnmeet-modal-message" style="display: none; margin-top: 10px;"></div>
        </div>
        <div class="plugnmeet-modal-footer">
            <button class="button" id="plugnmeet-cancel-merge"><?php echo __( "Cancel", "plugnmeet" ) ?></button>
            <button class="button button-primary"
                    id="plugnmeet-confirm-merge"><?php echo __( "Confirm Merge", "plugnmeet" ) ?></button>
        </div>
    </div>
</div>
