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

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}
$currentUrl = home_url( add_query_arg( null, null ) );
?>

<div class="column-full ">
    <div class="flex">
        <div class="w-full">
            <form class="login-form plugnmeet-login-form">
                <div class="alert roomStatus" role="alert" style="display: none"></div>
                <label for="name" class="input">
                    <span><?php echo __( "Name", "plugnmeet" ) ?></span>
                    <input type="text" name="name" class="form-control form-field" id="name" required
                           value="<?php echo esc_attr( $user->display_name ) ?>"
                           placeholder="<?php echo __( "Your full name", "plugnmeet" ) ?>"
                    >
                </label>

				<?php if ( isset( $role['require_password'] ) && $role['require_password'] === "on" ): ?>
                    <label for="password" class="input">
                        <span><?php echo __( "Password", "plugnmeet" ) ?></span>
                        <input type="password" name="password" class="form-control form-field" id="room-password"
                               required
                               placeholder="<?php echo __( "Room's Password", "plugnmeet" ) ?>"
                        >
                    </label>
				<?php endif; ?>

                <input type="hidden" name="id" value="<?php echo esc_attr( $roomInfo->id ) ?>">
                <input type="hidden" name="action" value="plugnmeet_login_to_room">
                <input type="hidden" name="current_url" value="<?php echo esc_attr( urlencode( $currentUrl ) ) ?>">
                <input type="hidden" name="nonce"
                       value="<?php echo wp_create_nonce( 'plugnmeet_login_to_room' ) ?>">
                <div class="btns">
                    <button type="submit" class="submit"><?php echo __( "Join", "plugnmeet" ) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
