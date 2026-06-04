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
?>

<div class="pnm-container" id="pnm-room-view-<?php echo esc_attr( $roomInfo->room_id ); ?>">
    <div class="column column-full">
        <?php if ( ! empty( $roomInfo->description ) ): ?>
            <div class="description"><?php echo wp_kses_post( $roomInfo->description ) ?></div>
            <hr/>
        <?php endif; ?>
        <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/parts/login-form.php'; ?>

        <?php if ( isset( $role['can_view_recording'] ) && $role['can_view_recording'] === "on" ): ?>
            <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/parts/recordings.php'; ?>
        <?php endif; ?>
    </div>
</div>
