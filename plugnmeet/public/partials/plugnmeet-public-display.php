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

if ( ! class_exists( 'PlugnmeetHelper' ) ) {
    require_once PLUGNMEET_ROOT_PATH . '/helpers/helper.php';
}

$room_metadata    = ! empty( $roomInfo->room_metadata ) ? json_decode( $roomInfo->room_metadata, true ) : array();
$room_metadata    = is_array( $room_metadata ) ? $room_metadata : array();
$room_mode        = PlugnmeetHelper::getRoomMode( $room_metadata );
$enable_ai_agent  = PlugnmeetHelper::isAiAgentEnabled();
$ai_agent         = isset( $room_metadata['insights_features']['ai_agent_features'] ) && is_array( $room_metadata['insights_features']['ai_agent_features'] ) ? $room_metadata['insights_features']['ai_agent_features'] : array();
$is_ai_agent_mode = $enable_ai_agent && 'ai_agent' === $room_mode;
$is_ai_enabled    = $is_ai_agent_mode || ( $enable_ai_agent && ! empty( $ai_agent['is_allow'] ) );

$show_recordings = isset( $role['can_view_recording'] ) && 'on' === $role['can_view_recording'];
if ( $is_ai_agent_mode ) {
    // Personal 1-on-1 sessions have no shared recording list, moderators keep it.
    $show_recordings = $show_recordings && ( ( isset( $role['join_as'] ) && 'moderator' === $role['join_as'] ) || current_user_can( 'manage_options' ) );
}
?>

<div class="pnm-container" id="pnm-room-view-<?php echo esc_attr( $roomInfo->room_id ); ?>">
    <div class="column column-full">
        <?php if ( ! empty( $roomInfo->description ) ): ?>
            <div class="description"><?php echo wp_kses_post( $roomInfo->description ) ?></div>
            <hr/>
        <?php endif; ?>

        <?php if ( $is_ai_enabled ): ?>
            <?php
            $ai_name  = trim( (string) ( $ai_agent['agent_display_name'] ?? '' ) );
            $ai_topic = trim( (string) ( $ai_agent['topic'] ?? '' ) );
            ?>
            <div class="pnm-ai-banner <?php echo $is_ai_agent_mode ? 'pnm-ai-banner-autonomous' : 'pnm-ai-banner-conference'; ?>">
                <div class="pnm-ai-banner-text">
                    <span class="pnm-ai-banner-name"><?php echo esc_html( '' !== $ai_name ? $ai_name : __( 'AI Assistant', 'plugnmeet' ) ); ?></span>
                    <?php if ( '' !== $ai_topic ): ?>
                        <span class="pnm-ai-banner-topic"><?php echo esc_html( $ai_topic ); ?></span>
                    <?php endif; ?>
                </div>
                <?php if ( $is_ai_agent_mode ): ?>
                    <span class="pnm-ai-banner-badge"><?php echo esc_html__( '1-on-1 Autonomous AI Session', 'plugnmeet' ); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php
        $login_form_template = $this->pnm_locate_template( 'parts/login-form.php' );
        require $login_form_template;
        ?>

        <?php if ( $show_recordings ): ?>
            <?php
            $recordings_template = $this->pnm_locate_template( 'parts/recordings.php' );
            require $recordings_template;
            ?>
        <?php endif; ?>
    </div>
</div>
