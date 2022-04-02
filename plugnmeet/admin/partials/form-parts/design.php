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
$custom_design = $fields_values['custom_design'];
?>

<table class="form-table" role="presentation">
    <tbody>
    <tr>
        <th scope="row"><?php echo __("Custom CSS URL", "plugnmeet") ?></th>
        <td><input name="custom_design[custom_css_url]"
                   type="text" size="40" value="<?php echo esc_attr($custom_design['custom_css_url']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Primary Color", "plugnmeet") ?></th>
        <td><input name="custom_design[primary_color]" class="colorPickerItem"
                   type="text" size="20" value="<?php echo esc_attr($custom_design['primary_color']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Secondary Color", "plugnmeet") ?></th>
        <td><input name="custom_design[secondary_color]" class="colorPickerItem"
                   type="text" size="20" value="<?php echo esc_attr($custom_design['secondary_color']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Background Color", "plugnmeet") ?></th>
        <td><input name="custom_design[background_color]" class="colorPickerItem"
                   type="text" size="20" value="<?php echo esc_attr($custom_design['background_color']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Background Image", "plugnmeet") ?></th>
        <td>
            <input style="margin-right: 20px;" size="36"
                   id="background_image"
                   name="custom_design[background_image]"
                   value="<?php echo esc_attr($custom_design['background_image']); ?>"/>
            <input class="button upload_logo_button" data-attached-to="background_image" type="button"
                   value="<?php echo __('Upload/Select image', 'plugnmeet'); ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Header Color", "plugnmeet") ?></th>
        <td><input name="custom_design[header_color]" class="colorPickerItem"
                   type="text" size="20" value="<?php echo esc_attr($custom_design['header_color']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Footer Color", "plugnmeet") ?></th>
        <td><input name="custom_design[footer_color]" class="colorPickerItem"
                   type="text" size="20" value="<?php echo esc_attr($custom_design['footer_color']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Left Bar Color", "plugnmeet") ?></th>
        <td><input name="custom_design[left_color]" class="colorPickerItem"
                   type="text" size="20" value="<?php echo esc_attr($custom_design['left_color']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __("Right Bar Color", "plugnmeet") ?></th>
        <td><input name="custom_design[right_color]" class="colorPickerItem"
                   type="text" size="20" value="<?php echo esc_attr($custom_design['right_color']); ?>"></td>
    </tr>
    </tbody>
</table>