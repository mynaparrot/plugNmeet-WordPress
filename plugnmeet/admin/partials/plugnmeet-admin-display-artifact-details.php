<?php
/**
 * Renders the artifact details page.
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin/partials
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
    die;
}

$metadata      = $artifact_info->getMetadata();
$is_file_based = ( $metadata && $metadata->hasFileInfo() );
$roomId        = isset( $_GET['room_id'] ) ? sanitize_text_field( $_GET['room_id'] ) : '';
?>
<div class="wrap plugnmeet-artifact-details">
    <div class="plugnmeet-details-header">
        <h1 class="wp-heading-inline"><?php echo __( 'Artifact Details', 'plugnmeet' ); ?></h1>
        <div class="plugnmeet-header-actions">
            <a href="<?php echo admin_url( 'admin.php?page=plugnmeet-artifacts&room_id=' . $roomId ); ?>"
               class="btn btn-secondary">
                <?php echo __( 'Back to Artifacts', 'plugnmeet' ); ?>
            </a>
            <?php if ( $is_file_based && ! $context['is_analytics'] ) : ?>
                <a href="#" class="btn btn-success download-artifact"
                   data-artifact-id="<?php echo esc_attr( $artifact_info->getArtifactId() ); ?>"><?php echo __( 'Download', 'plugnmeet' ); ?></a>
            <?php endif; ?>
            <?php if ( $context['is_analytics'] ) : ?>
                <a href="#" class="btn btn-success download-analytics-excel"
                   data-artifact-id="<?php echo esc_attr( $artifact_info->getArtifactId() ); ?>"><?php echo __( 'Download Excel', 'plugnmeet' ); ?></a>
            <?php endif; ?>
            <?php if ( $is_file_based ) : ?>
                <a href="#" class="btn btn-danger delete-artifact"
                   data-artifact-id="<?php echo esc_attr( $artifact_info->getArtifactId() ); ?>"><?php echo __( 'Delete', 'plugnmeet' ); ?></a>
            <?php endif; ?>
        </div>
    </div>
    <hr class="wp-header-end">

    <table class="form-table mb-4">
        <tbody>
        <?php foreach ( $context['details'] as $detail ) : ?>
            <tr>
                <th scope="row"><?php echo esc_html( $detail['label'] ); ?></th>
                <td><?php echo esc_html( $detail['value'] ); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ( $context['is_analytics'] ) : ?>
        <h4><?php echo __( 'Room Analytics', 'plugnmeet' ); ?></h4>
        <table class="form-table">
            <tbody>
            <?php foreach ( $context['room_details'] as $detail ) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html( $detail['label'] ); ?></th>
                    <td><?php echo esc_html( $detail['value'] ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $context['has_users'] ) : ?>
            <h4 class="mt-4"><?php echo __( 'User Analytics', 'plugnmeet' ); ?></h4>
            <div class="plugnmeet-analytics-table-wrapper">
                <table class="wp-list-table widefat striped">
                    <thead>
                    <tr>
                        <?php foreach ( $context['user_headers'] as $header ) : ?>
                            <th><?php echo esc_html( $header ); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $context['user_rows'] as $row ) : ?>
                        <tr>
                            <?php foreach ( $row['data'] as $cell ) : ?>
                                <td><?php echo wp_kses( $cell['value'], array( 'br' => array() ) ); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ( $context['is_meeting_summary'] ) : ?>
        <h4 class="mb-4"><?php echo __( 'Meeting Summary', 'plugnmeet' ); ?></h4>
        <div>
            <?php echo wp_kses_post( $context['meeting_summary_content'] ); ?>
        </div>
    <?php endif; ?>
</div>
