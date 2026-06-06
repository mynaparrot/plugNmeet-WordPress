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
        <?php
        $login_form_template = $this->pnm_locate_template( 'parts/login-form.php' );
        require $login_form_template;
        ?>

        <?php if ( isset( $role['can_view_recording'] ) && $role['can_view_recording'] === "on" ): ?>
            <?php
            $recordings_template = $this->pnm_locate_template( 'parts/recordings.php' );
            require $recordings_template;
            ?>
        <?php endif; ?>
    </div>
</div>
