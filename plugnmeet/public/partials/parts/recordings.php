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

<div class="pnm-recordings-wrapper">
    <h4><?php echo __( "Recordings", "plugnmeet" ); ?></h4>

    <table class="widefat">
        <thead>
        <tr>
            <th><?php echo __( "Recording date", "plugnmeet" ); ?></th>
            <th><?php echo __( "Meeting date", "plugnmeet" ); ?></th>
            <th><?php echo __( "File size (MB)", "plugnmeet" ); ?></th>
            <th style="text-align: right"><?php echo __( "Action", "plugnmeet" ); ?></th>
        </tr>
        </thead>
        <tbody id="recordingListsBody">
        <tr>
            <td colspan="4"><?php echo __( "Loading...", "plugnmeet" ); ?></td>
        </tr>
        </tbody>
    </table>

    <div class="pagination-links" style="display: none; margin-top: 1rem;">
        <button id="backward" class="button"><?php echo __( "Pre", "plugnmeet" ); ?></button>
        <button id="forward" class="button"><?php echo __( "Next", "plugnmeet" ); ?></button>
    </div>

    <div id="playbackModal" style="display:none">
        <video id="modalPlayer" width="100%" height="400" controls controlsList="nodownload" src=""
               oncontextmenu="return false"></video>
    </div>
</div>
