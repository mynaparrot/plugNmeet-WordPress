<?php
if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

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
?>

<div class="wrap">
    <h1 class="mb-6"><?php echo $_GET['task'] === "add" ? "Add room" : "Edit room" ?></h1>
    <hr/>
    <form name="plugnmeet-form" id="plugnmeet-form">
        <?php
        require plugin_dir_path(dirname(__FILE__)) . '/partials/form-parts/basic.php';
        ?>

        <h2><?php echo __("Room features", "plugnmeet") ?></h2>

        <table class="form-table" role="presentation">
            <tbody>
            <?php echo PlugnmeetHelper::getRoomFeatures($fields_values['room_features']); ?>

            </tbody>
        </table>

        <h2><?php echo __("Chat features", "plugnmeet") ?></h2>

        <table class="form-table" role="presentation">
            <tbody>
            <?php echo PlugnmeetHelper::getChatFeatures($fields_values['chat_features']); ?>

            </tbody>
        </table>

        <h2><?php echo __("Default lock settings", "plugnmeet") ?></h2>

        <table class="form-table" role="presentation">
            <tbody>
            <?php echo PlugnmeetHelper::getDefaultLockSettings($fields_values['default_lock_settings']); ?>

            </tbody>
        </table>
        <table class="form-table" role="presentation">
            <tbody>
            <?php echo PlugnmeetHelper::getStatusSettings($fields_values['published']); ?>

            </tbody>
        </table>
        <input type="hidden" name="id" value="<?php echo $fields_values['id']; ?>">
        <input type="hidden" name="action" value="plugnmeet_save_room_data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('save_room_data') ?>">
        <button class="button button-primary" type="submit"><?php echo __("Submit", "plugnmeet") ?></button>
        <button class="button button-secondary" onclick="goBack()"><?php echo __("Cancel", "plugnmeet") ?></button>
    </form>
</div>

<script type="text/javascript">
    function goBack() {
        window.location.href = "admin.php?page=plugnmeet";
    }
</script>
