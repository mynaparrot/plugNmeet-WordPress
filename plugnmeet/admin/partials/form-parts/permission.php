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
global $wp_roles;

$roles = array();
foreach ($wp_roles->roles as $key => $role) {
    $r = array(
        'title' => $role['name'],
        'require_password' => "on",
        'join_as' => 'attendee',
        'can_view_recording' => "off",
        'can_play' => "off",
        'can_download' => "off",
        'can_delete' => "off"
    );

    if (isset($role['capabilities']['edit_users']) && $role['capabilities']['edit_users']) {
        $r['require_password'] = "off";
	    $r['can_play'] = "on";
	    $r['can_view_recording'] = "on";
        $r['can_download'] = "on";
        $r['can_delete'] = "on";
        $r['join_as'] = 'moderator';
    } elseif (isset($role['capabilities']['edit_posts']) && $role['capabilities']['edit_posts']) {
        $r['require_password'] = "off";
	    $r['can_view_recording'] = "on";
	    $r['can_play'] = "on";
        $r['can_download'] = "on";
        $r['join_as'] = 'moderator';
    }

    $roles[$key] = $r;
}
$roles['guest'] = array(
    'title' => "Guest/Public",
    'require_password' => "on",
    'join_as' => 'attendee',
    'can_view_recording' => 'off',
    'can_play' => 'off',
    'can_download' => "off",
    'can_delete' => "off"
);


$dbRoles = $fields_values['roles'];
if (!empty($dbRoles)) {
    foreach ($roles as $key => $role) {
        if (!isset($dbRoles[$key])) {
            continue;
        }
        $keys = array_keys($role);
        foreach ($keys as $k) {
            if (isset($dbRoles[$key][$k])) {
                $roles[$key][$k] = $dbRoles[$key][$k];
            } else if ($k !== "title") {
                $roles[$key][$k] = "off";
            }
        }
    }
}

?>
<style>
    .permission-table {
        margin-top: 10px;
    }

    table, th, td {
        border-collapse: collapse;
    }

    td {
        border: 1px solid white;
    }

    th {
        color: #fff;
    }

    tr th {
        padding: 5px 10px;
        font-size: 14px;
        text-align: center;
    }

    td {
        padding: 5px;
    }
</style>
<div class="permission-table">
    <table bgcolor="silver" width="100%">
        <tr bgcolor="#333">
            <th><?php echo __("Role", "plugnmeet"); ?></th>
            <th><?php echo __("Join as <br/>Moderator", "plugnmeet"); ?></th>
            <th><?php echo __("Join as <br/>Attendee", "plugnmeet"); ?></th>
            <th><?php echo __("Require <br/> Password", "plugnmeet"); ?></th>
            <th><?php echo __("Allow View <br/>Recordings", "plugnmeet"); ?></th>
            <th><?php echo __("Allow Play <br/>Recordings", "plugnmeet"); ?></th>
            <th><?php echo __("Allow Download <br/>Recordings", "plugnmeet"); ?></th>
            <th><?php echo __("Allow Delete <br/>Recordings", "plugnmeet"); ?></th>
        </tr>
        <?php foreach ($roles as $key => $role): ?>
            <tr id="<?php echo $key; ?>">
                <td><?php echo $role['title']; ?></td>
                <td align="center">
                    <input type="radio" name="roles[<?php echo $key; ?>][join_as]"
                           value="moderator"
                        <?php echo $role['join_as'] === "moderator" ? "checked='checked'" : ""; ?>>
                </td>
                <td align="center">
                    <input type="radio" name="roles[<?php echo $key; ?>][join_as]"
                           value="attendee"
                        <?php echo $role['join_as'] === "attendee" ? "checked='checked'" : ""; ?>>
                </td>
                <td align="center">
                    <input type="checkbox" name="roles[<?php echo $key; ?>][require_password]"
                        <?php echo $role['require_password'] === "on" ? "checked='checked'" : ""; ?>>
                </td>
                <td align="center">
                    <input type="checkbox" name="roles[<?php echo $key; ?>][can_view_recording]"
			            <?php echo $role['can_view_recording'] === "on" ? "checked='checked'" : ""; ?>>
                </td>
                <td align="center">
                    <input type="checkbox" name="roles[<?php echo $key; ?>][can_play]"
			            <?php echo $role['can_play'] === "on" ? "checked='checked'" : ""; ?>>
                </td>
                <td align="center">
                    <input type="checkbox" name="roles[<?php echo $key; ?>][can_download]"
                        <?php echo $role['can_download'] === "on" ? "checked='checked'" : ""; ?>>
                </td>
                <td align="center">
                    <?php if ($key === "guest"): ?>
                        <div>n/a</div>
                    <?php else: ?>
                        <input type="checkbox" name="roles[<?php echo $key; ?>][can_delete]"
                            <?php echo $role['can_delete'] === "on" ? "checked='checked'" : ""; ?>>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
