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

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
    die;
}
?>

<div class="wrap plugnmeet-room-edit">
    <div class="plugnmeet-details-header">
        <h1 class="wp-heading-inline"><?php echo $_GET['task'] === "add" ? "Add room" : "Edit room" ?></h1>
        <div class="plugnmeet-header-actions">
            <button class="button button-primary" type="submit"
                    form="plugnmeet-form"><?php echo __( "Submit", "plugnmeet" ) ?></button>
            <a class="button button-secondary"
               href="admin.php?page=plugnmeet"><?php echo __( "Cancel", "plugnmeet" ) ?></a>
        </div>
    </div>
    <hr/>

    <form name="plugnmeet-form" id="plugnmeet-form" class="plugnmeet-form">
        <div class="nav-tab-wrapper">
            <a href="#basic" class="nav-tab nav-tab-active">
                <?php echo __( "Basic", "plugnmeet" ) ?>
            </a>
            <a href="#room-features" class="nav-tab">
                <?php echo __( "Room features", "plugnmeet" ) ?>
            </a>
            <a href="#other-features" class="nav-tab">
                <?php echo __( "Other features", "plugnmeet" ) ?>
            </a>
            <a href="#insights" class="nav-tab">
                <?php echo __( "Insights AI", "plugnmeet" ) ?>
            </a>
            <a href="#lock" class="nav-tab">
                <?php echo __( "Default lock settings", "plugnmeet" ) ?>
            </a>
            <a href="#design" class="nav-tab">
                <?php echo __( "Design Customization", "plugnmeet" ) ?>
            </a>
            <a href="#permission" class="nav-tab">
                <?php echo __( "Permission", "plugnmeet" ) ?>
            </a>
        </div>

        <div id="plugnmeet-room-tab-contents">
            <div id="basic" class="plugnmeet-tab-content">
                <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/basic.php'; ?>
            </div>

            <div id="room-features" class="plugnmeet-tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getRoomFeatures( $fields_values['room_features'] ); ?>
                    </tbody>
                </table>
            </div>

            <div id="other-features" class="plugnmeet-tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getRecordingFeatures( $fields_values['recording_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getExternalBroadcastingFeatures( $fields_values['external_broadcasting_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getChatFeatures( $fields_values['chat_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getSharedNotePadFeatures( $fields_values['shared_note_pad_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getWhiteboardFeatures( $fields_values['whiteboard_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getExternalMediaPlayerFeatures( $fields_values['external_media_player_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getWaitingRoomFeatures( $fields_values['waiting_room_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getBreakoutRoomFeatures( $fields_values['breakout_room_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getDisplayExternalLinkFeatures( $fields_values['display_external_link_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getIngressFeatures( $fields_values['ingress_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getPollsFeatures( $fields_values['polls_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getSipDialInFeatures( $fields_values['sip_dial_in_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getEndToEndEncryptionFeatures( $fields_values['end_to_end_encryption_features'] ); ?>
                    </tbody>
                </table>
            </div>

            <div id="insights" class="plugnmeet-tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getInsightsFeatures( $fields_values['insights_features'] ); ?>
                    </tbody>
                </table>
            </div>

            <div id="lock" class="plugnmeet-tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php echo PlugnmeetHelper::getDefaultLockSettings( $fields_values['default_lock_settings'] ); ?>
                    </tbody>
                </table>
            </div>

            <div id="design" class="plugnmeet-tab-content" style="display: none;">
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/design.php'; ?>
                    </tbody>
                </table>
            </div>

            <div id="permission" class="plugnmeet-tab-content" style="display: none;">
                <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/permission.php'; ?>
            </div>

        </div>

        <input type="hidden" name="id" value="<?php echo esc_attr( $fields_values['id'] ); ?>">
        <input type="hidden" name="action" value="plugnmeet_save_room_data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'save_room_data' ) ?>">
    </form>
</div>
