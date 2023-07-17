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

<div class="wrap">
    <h1 class="mb-6"><?php echo $_GET['task'] === "add" ? "Add room" : "Edit room" ?></h1>
    <hr/>
    <form name="plugnmeet-form" id="plugnmeet-form">
        <div class="d-flex justify-content-end mb-3">
            <button class="button button-primary me-3" type="submit"><?php echo __( "Submit", "plugnmeet" ) ?></button>
            <a class="button button-secondary"
               href="admin.php?page=plugnmeet"><?php echo __( "Cancel", "plugnmeet" ) ?></a>
        </div>

        <ul class="nav nav-tabs" id="plugnmeet-room-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                        type="button"
                        role="tab" aria-controls="basic" aria-selected="true">
					<?php echo __( "Basic", "plugnmeet" ) ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="room-features-tab" data-bs-toggle="tab" data-bs-target="#room-features"
                        type="button"
                        role="tab" aria-controls="room-features" aria-selected="false">
					<?php echo __( "Room features", "plugnmeet" ) ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="other-features-tab" data-bs-toggle="tab" data-bs-target="#other-features"
                        type="button"
                        role="tab" aria-controls="other-features" aria-selected="false">
					<?php echo __( "Other features", "plugnmeet" ) ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="lock-tab" data-bs-toggle="tab" data-bs-target="#lock" type="button"
                        role="tab" aria-controls="lock" aria-selected="false">
					<?php echo __( "Default lock settings", "plugnmeet" ) ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="design-tab" data-bs-toggle="tab" data-bs-target="#design" type="button"
                        role="tab" aria-controls="design" aria-selected="false">
					<?php echo __( "Design Customization", "plugnmeet" ) ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="permission-tab" data-bs-toggle="tab" data-bs-target="#permission"
                        type="button"
                        role="tab" aria-controls="permission" aria-selected="false">
					<?php echo __( "Permission", "plugnmeet" ) ?>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="plugnmeet-room-tab-contents">
            <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
				<?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/basic.php'; ?>
            </div>

            <div class="tab-pane fade" id="room-features" role="tabpanel" aria-labelledby="room-features">
                <table class="form-table" role="presentation">
                    <tbody>
					<?php echo PlugnmeetHelper::getRoomFeatures( $fields_values['room_features'] ); ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="other-features" role="tabpanel" aria-labelledby="other-features">
                <table class="form-table" role="presentation">
                    <tbody>
					<?php echo PlugnmeetHelper::getRecordingFeatures( $fields_values['recording_features'] ); ?>
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
					<?php echo PlugnmeetHelper::getSpeechToTextTranslationFeatures( $fields_values['speech_to_text_translation_features'] ); ?>
                    </tbody>
                </table>
                <hr/>
                <table class="form-table" role="presentation">
                    <tbody>
		            <?php echo PlugnmeetHelper::getEndToEndEncryptionFeatures( $fields_values['end_to_end_encryption_features'] ); ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="lock" role="tabpanel" aria-labelledby="lock-tab">
                <table class="form-table" role="presentation">
                    <tbody>
					<?php echo PlugnmeetHelper::getDefaultLockSettings( $fields_values['default_lock_settings'] ); ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="design" role="tabpanel" aria-labelledby="design-tab">
                <table class="form-table" role="presentation">
                    <tbody>
					<?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/design.php'; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="permission" role="tabpanel" aria-labelledby="permission-tab">
				<?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/permission.php'; ?>
            </div>

        </div>

        <input type="hidden" name="id" value="<?php echo esc_attr( $fields_values['id'] ); ?>">
        <input type="hidden" name="action" value="plugnmeet_save_room_data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'save_room_data' ) ?>">
    </form>
</div>
