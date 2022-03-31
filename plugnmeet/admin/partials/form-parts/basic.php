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

<table class="form-table" role="presentation">
    <tbody>
    <?php if (isset($_GET['id'])): ?>
        <tr>
            <th scope="row"><?php echo __("Room Id", "plugnmeet") ?></th>
            <td>
                <input name="room_id" type="text" size="40" value="<?php echo esc_attr($fields_values['room_id']); ?>"
                       disabled>
                <input name="room_id" type="text" size="40"
                       value="[plugnmeet_room_view id='<?php echo esc_attr($fields_values['id']); ?>']"
                       disabled>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <th scope="row"><?php echo __("Room title", "plugnmeet") ?></th>
        <td><input required="required" name="room_title"
                   type="text" size="40" value="<?php echo esc_attr($fields_values['room_title']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Room description", "plugnmeet") ?></th>
        <td><?php wp_editor($fields_values['description'], "description"); ?> </td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Moderator Password", "plugnmeet") ?></th>
        <td><input required="required" name="moderator_pass" type="text" size="40"
                   value="<?php echo esc_attr($fields_values['moderator_pass']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Attendee Password", "plugnmeet") ?></th>
        <td><input required="required" name="attendee_pass" type="text" size="40"
                   value="<?php echo esc_attr($fields_values['attendee_pass']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Welcome Message", "plugnmeet") ?></th>
        <td><textarea name="welcome_message"><?php echo esc_textarea($fields_values['welcome_message']); ?></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Maximum participants (0 = unlimited)", "plugnmeet") ?></th>
        <td><input name="max_participants" type="number" size="10"
                   value="<?php echo esc_attr($fields_values['max_participants']); ?>">
        </td>
    </tr>

    <?php echo PlugnmeetHelper::getStatusSettings($fields_values['published']); ?>
    </tbody>
</table>
