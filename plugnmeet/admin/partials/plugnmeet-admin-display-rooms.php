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

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __("Rooms", "plugnmeet") ?></h1>
    <a href="admin.php?page=plugnmeet&task=add" class="page-title-action"><?php echo __("Add New", "plugnmeet") ?></a>
    <hr/>

    <form name="search-form" id="search-form">
        <p class="search-box" style="margin-bottom: 20px">
            <label class="screen-reader-text" for="search_term"><?php echo __("Search", "plugnmeet") ?></label>
            <input type="search" id="search_term" name="search_term"
                   value="<?php echo isset($_GET['search_term']) ? esc_attr($_GET['search_term']) : "" ?>">
            <input type="hidden" id="page_num"
                   value="<?php echo isset($_GET['paged']) ? esc_attr($_GET['paged']) : 0 ?>">
            <button type="submit" id="search-submit"
                    class="button button-primary"><?php echo __("Search", "plugnmeet") ?></button>
        </p>
    </form>
    
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-id"
                style="width: 3em;"><?php echo __("Id", "plugnmeet") ?>
            </th>
            <th scope="col"
                class="manage-column column-title column-primary"><?php echo __("Room title", "plugnmeet") ?>
            </th>
            <th scope="col" class="manage-column column-categories"><?php echo __("Moderator Password", "plugnmeet") ?>

            </th>
            <th scope="col" class="manage-column column-categories"><?php echo __("Attendee Password", "plugnmeet") ?>
            </th>
            <th scope="col" class="manage-column column-categories"><?php echo __("Status", "plugnmeet") ?>
            </th>
        </tr>
        </thead>

        <tbody id="the-list">
        <?php foreach ($rooms as $room): ?>
            <tr id="post-<?php echo esc_attr($room->id) ?>"
                class="iedit type-post format-standard">
                <td class="id column-id"><?php echo esc_html($room->id) ?></td>
                <td class="title column-title"><a
                            href="admin.php?page=plugnmeet&task=edit&id=<?php echo $room->id ?>"><?php echo esc_html($room->room_title) ?></a>
                </td>
                <td class="moderator_pass column-moderator_pass"><?php echo esc_html($room->moderator_pass) ?></td>
                <td class="attendee_pass column-attendee_pass"><?php echo esc_html($room->attendee_pass) ?></td>
                <td class="published column-published"><?php echo $room->published ? __("Published", "plugnmeet") : __("Unpublished", "plugnmeet") ?>
                    <div class="alignright">
                        <button class="button button-secondary deleteRoom"
                                id="<?php echo esc_attr($room->id) ?>"><?php echo __("Delete", "plugnmeet") ?></button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    if ($totalNumRooms > $limit) {
        require plugin_dir_path(dirname(__FILE__)) . '/partials/plugnmeet-admin-display-rooms-pagination.php';
    }
    ?>

</div>
