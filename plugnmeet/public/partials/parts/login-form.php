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

<form class="plugnmeet-login-form" data-room-id="<?php echo esc_attr( $roomInfo->room_id ); ?>">
    <div class="notice notice-info roomStatus" role="alert" style="display: none"></div>

    <div class="form-row">
        <label for="pnm-name"><?php echo __( "Name", "plugnmeet" ) ?></label>
        <input type="text" name="name" id="pnm-name" required
               value="<?php echo esc_attr( $user->display_name ) ?>"
               placeholder="<?php echo __( "Enter your name", "plugnmeet" ) ?>"
        >
    </div>

    <?php if ( isset( $role['require_password'] ) && $role['require_password'] === "on" ): ?>
        <div class="form-row">
            <label for="pnm-room-password"><?php echo __( "Password", "plugnmeet" ) ?></label>
            <input type="password" name="password" id="pnm-room-password" required
                   placeholder="<?php echo __( "Enter room password", "plugnmeet" ) ?>"
            >
        </div>
    <?php endif; ?>

    <input type="hidden" name="id" value="<?php echo esc_attr( $roomInfo->id ) ?>">
    <input type="hidden" name="action" value="plugnmeet_login_to_room">
    <input type="hidden" name="current_url" value="<?php echo esc_attr( urlencode( $currentUrl ) ) ?>">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'plugnmeet_login_to_room' ) ?>">

    <div class="form-submit">
        <button type="submit" class="button button-primary"><?php echo __( "Join", "plugnmeet" ) ?></button>
    </div>
</form>
