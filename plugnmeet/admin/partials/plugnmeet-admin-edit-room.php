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

$enable_ai_agent = PlugnmeetHelper::isAiAgentEnabled();
?>

<div class="wrap plugnmeet-room-edit">
    <div class="plugnmeet-details-header">
        <h1 class="wp-heading-inline"><?php echo $_GET['task'] === "add" ? "Add room" : "Edit room" ?></h1>
        <?php if ( $enable_ai_agent ) : ?>
            <div id="pnm-mode-switcher" class="pnm-mode-switcher" role="radiogroup"
                 aria-label="<?php echo esc_attr__( "Room mode", "plugnmeet" ) ?>"
                 aria-describedby="pnm-mode-switcher-help"
                 title="<?php echo esc_attr( plugnmeet_get_help_text( 'basic.room_mode' ) ) ?>">
                <label class="pnm-mode-option<?php echo 'conference' === $fields_values['room_mode'] ? ' is-active' : '' ?>">
                    <input type="radio" name="room_mode" value="conference" form="plugnmeet-form"
                        <?php checked( $fields_values['room_mode'], 'conference' ); ?>>
                    <span class="pnm-mode-label"><?php echo __( "Live Web Conference", "plugnmeet" ) ?></span>
                </label>
                <label class="pnm-mode-option<?php echo 'ai_agent' === $fields_values['room_mode'] ? ' is-active' : '' ?>">
                    <input type="radio" name="room_mode" value="ai_agent" form="plugnmeet-form"
                        <?php checked( $fields_values['room_mode'], 'ai_agent' ); ?>>
                    <span class="pnm-mode-label"><?php echo __( "Autonomous AI Agent (1-on-1)", "plugnmeet" ) ?></span>
                </label>
            </div>
            <span id="pnm-mode-switcher-help" class="screen-reader-text"><?php echo esc_html( plugnmeet_get_help_text( 'basic.room_mode' ) ) ?></span>
        <?php endif; ?>
        <div class="plugnmeet-header-actions">
            <button class="button button-primary" type="submit"
                    form="plugnmeet-form"><?php echo __( "Submit", "plugnmeet" ) ?></button>
            <a class="button button-secondary"
               href="admin.php?page=plugnmeet"><?php echo __( "Cancel", "plugnmeet" ) ?></a>
        </div>
    </div>
    <hr class="wp-header-end"/>

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
                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Basic information", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Room identity, passwords and welcome message.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/basic.php'; ?>
                    </div>
                </div>
            </div>

            <div id="room-features" class="plugnmeet-tab-content" style="display: none;">
                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Room features", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Webcams, screen sharing and more.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getRoomFeatures( $fields_values['room_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="other-features" class="plugnmeet-tab-content" style="display: none;">
                <div class="pnm-cards-toolbar">
                    <button type="button"
                            class="button button-secondary pnm-expand-all"><?php echo __( "Expand all", "plugnmeet" ) ?></button>
                    <button type="button"
                            class="button button-secondary pnm-collapse-all"><?php echo __( "Collapse all", "plugnmeet" ) ?></button>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Recording", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Record on the server or locally.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getRecordingFeatures( $fields_values['recording_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Chat", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Public chat and file sharing.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getChatFeatures( $fields_values['chat_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Waiting room", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Hold participants until admitted.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getWaitingRoomFeatures( $fields_values['waiting_room_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Polls", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Interactive polls and quizzes.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getPollsFeatures( $fields_values['polls_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Shared notepad", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Collaborative text editor.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getSharedNotePadFeatures( $fields_values['shared_note_pad_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Whiteboard", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Interactive drawing and annotation space.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getWhiteboardFeatures( $fields_values['whiteboard_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Breakout rooms", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Split participants into sub-rooms.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getBreakoutRoomFeatures( $fields_values['breakout_room_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "External media player", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Play external videos in sync for everyone.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getExternalMediaPlayerFeatures( $fields_values['external_media_player_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Display external link", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Show external content inside the session.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getDisplayExternalLinkFeatures( $fields_values['display_external_link_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "External broadcasting", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Stream the session to external platforms.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getExternalBroadcastingFeatures( $fields_values['external_broadcasting_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Ingress", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Push high-quality streams via OBS/RTMP/WHIP.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getIngressFeatures( $fields_values['ingress_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "SIP dial-in", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Join the audio by phone call.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getSipDialInFeatures( $fields_values['sip_dial_in_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "End-to-end encryption", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Encrypt media streams between participants.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getEndToEndEncryptionFeatures( $fields_values['end_to_end_encryption_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="insights" class="plugnmeet-tab-content" style="display: none;">
                <div id="pnm-insights-standard-card" class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Insights & AI meeting tools", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Transcription, chat translation and AI tools.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getInsightsFeatures( $fields_values['insights_features'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ( $enable_ai_agent ) : ?>
                    <div id="pnm-ai-agent-card" class="pnm-card pnm-collapsible" data-collapsed="false">
                        <div class="pnm-card-header">
                            <button type="button" class="pnm-card-toggle" aria-expanded="true">
                                <span class="pnm-card-title"><?php echo __( "AI Agent & 1-on-1 autonomous session", "plugnmeet" ) ?></span>
                                <span class="pnm-card-subtitle"><?php echo __( "Instructions, guardrails and limits for the AI agent.", "plugnmeet" ) ?></span>
                                <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="pnm-card-body">
                            <table class="form-table" role="presentation">
                                <tbody>
                                <?php echo PlugnmeetHelper::getAiAgentFeatures( $fields_values['insights_features'] ); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div id="lock" class="plugnmeet-tab-content" style="display: none;">
                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Default lock settings", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "What participants can do when they join.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php echo PlugnmeetHelper::getDefaultLockSettings( $fields_values['default_lock_settings'] ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="design" class="plugnmeet-tab-content" style="display: none;">
                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Design customization", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Colors, logo and background.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <table class="form-table" role="presentation">
                            <tbody>
                            <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/design.php'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="permission" class="plugnmeet-tab-content" style="display: none;">
                <div class="pnm-card pnm-collapsible" data-collapsed="false">
                    <div class="pnm-card-header">
                        <button type="button" class="pnm-card-toggle" aria-expanded="true">
                            <span class="pnm-card-title"><?php echo __( "Permission", "plugnmeet" ) ?></span>
                            <span class="pnm-card-subtitle"><?php echo __( "Room access per WordPress role.", "plugnmeet" ) ?></span>
                            <span class="pnm-toggle-indicator" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="pnm-card-body">
                        <?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/form-parts/permission.php'; ?>
                    </div>
                </div>
            </div>

        </div>

        <input type="hidden" name="id" value="<?php echo esc_attr( $fields_values['id'] ); ?>">
        <input type="hidden" name="action" value="plugnmeet_save_room_data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'save_room_data' ) ?>">
    </form>

    <?php if ( $enable_ai_agent ) : ?>
        <div id="pnm-user-search-modal" class="pnm-modal" style="display: none;">
            <div class="pnm-modal-backdrop"></div>
            <div class="pnm-modal-content" role="dialog" aria-modal="true" aria-labelledby="pnm-user-search-title">
                <div class="pnm-modal-header">
                    <h2 id="pnm-user-search-title"><?php echo __( "Select users", "plugnmeet" ) ?></h2>
                    <button type="button" class="pnm-modal-close" id="pnm-user-search-close"
                            aria-label="<?php echo esc_attr__( "Close", "plugnmeet" ) ?>">&times;</button>
                </div>
                <div class="pnm-modal-toolbar">
                    <input type="search" id="pnm-user-search-input"
                           placeholder="<?php echo esc_attr__( "Search by name or email", "plugnmeet" ) ?>">
                    <select id="pnm-user-role-filter">
                        <option value=""><?php echo __( "All roles", "plugnmeet" ) ?></option>
                        <?php foreach ( wp_roles()->get_names() as $role_key => $role_name ) : ?>
                            <option value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_html( $role_name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <p class="pnm-modal-counter">
                    <?php echo __( "Selected", "plugnmeet" ) ?>:
                    <span class="pnm-selected-count">0</span> / 32
                </p>
                <ul id="pnm-user-search-results" class="pnm-user-search-results"></ul>
                <div class="pnm-modal-footer">
                    <button type="button" class="button button-primary"
                            id="pnm-user-search-done"><?php echo __( "Done", "plugnmeet" ) ?></button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
