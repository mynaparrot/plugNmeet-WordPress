<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.mynaparrot.com
 * @since      1.0.0
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/public/partials
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}
$user = wp_get_current_user();

?>

<div class="pnm-container">
    <div class="column column-full">
        <div class="description"><?php echo wp_kses_post($roomInfo->description) ?></div>
        <hr/>
        <div class="column-full ">
            <div class="flex">
                <div class="w-6-4 description">
                    <?php echo __("Instruction", "plugnmeet") ?>
                </div>
                <div class="w-6-2">
                    <h1 class="headline"><?php echo __("Login", "plugnmeet") ?></h1>
                    <div class="br">
                        <div class="br-inner"></div>
                    </div>
                    <form class="login-form" id="plugnmeet-login-form">
                        <div id="roomStatus" class="alert" role="alert" style="display: none"></div>
                        <label for="name" class="input">
                            <p><?php echo __("Name", "plugnmeet") ?></p>
                            <input type="text" name="name" class="form-control form-field" id="name" required
                                   value="<?php echo esc_attr($user->display_name) ?>">
                        </label>

                        <label for="password" class="input">
                            <p><?php echo __("Room's Password", "plugnmeet") ?></p>
                            <input type="password" name="password" class="form-control form-field" id="room-password"
                                   required>
                        </label>

                        <input type="hidden" name="id" value="<?php echo esc_attr($roomInfo->id) ?>">
                        <input type="hidden" name="action" value="plugnmeet_login_to_room">
                        <input type="hidden" name="nonce"
                               value="<?php echo wp_create_nonce('plugnmeet_login_to_room') ?>">
                        <div class="btns">
                            <button type="submit" class="submit"><?php echo __("Login", "plugnmeet") ?></button>
                            <button type="reset" class="reset"><?php echo __("Reset", "plugnmeet") ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
