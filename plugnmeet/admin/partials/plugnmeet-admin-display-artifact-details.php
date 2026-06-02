<?php
/**
 * Renders the artifact details page.
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin/partials
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

$metadata = $artifact_info->getMetadata();
$is_file_based = ($metadata && $metadata->hasFileInfo());
?>
<div class="wrap plugnmeet-artifact-details">
    <div class="plugnmeet-details-header">
        <h1 class="wp-heading-inline"><?php echo __('Artifact Details', 'plugnmeet'); ?></h1>
        <div class="plugnmeet-header-actions">
            <a href="<?php echo admin_url('admin.php?page=plugnmeet-artifacts'); ?>" class="page-title-action">
                <?php echo __('Back to Artifacts', 'plugnmeet'); ?>
            </a>
            <?php if ($is_file_based && !$context['is_analytics']) : ?>
                <a href="#" class="page-title-action download-artifact" data-artifact-id="<?php echo esc_attr($artifact_info->getArtifactId()); ?>"><?php echo __('Download', 'plugnmeet'); ?></a>
            <?php endif; ?>
            <?php if ($context['is_analytics']) : ?>
                <a href="#" class="page-title-action download-analytics-excel" data-artifact-id="<?php echo esc_attr($artifact_info->getArtifactId()); ?>"><?php echo __('Download Excel', 'plugnmeet'); ?></a>
            <?php endif; ?>
            <a href="#" class="page-title-action button-link-delete delete-artifact" data-artifact-id="<?php echo esc_attr($artifact_info->getArtifactId()); ?>"><?php echo __('Delete', 'plugnmeet'); ?></a>
        </div>
    </div>
    <hr class="wp-header-end">

    <table class="form-table">
        <tbody>
            <?php foreach ($context['details'] as $detail) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($detail['label']); ?></th>
                    <td><?php echo esc_html($detail['value']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($context['is_analytics']) : ?>
        <h2><?php echo __('Room Analytics', 'plugnmeet'); ?></h2>
        <table class="form-table">
            <tbody>
                <?php foreach ($context['room_details'] as $detail) : ?>
                    <tr>
                        <th scope="row"><?php echo esc_html($detail['label']); ?></th>
                        <td><?php echo esc_html($detail['value']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($context['has_users']) : ?>
            <h2><?php echo __('User Analytics', 'plugnmeet'); ?></h2>
            <div class="plugnmeet-analytics-table-wrapper">
                <table class="wp-list-table widefat striped">
                    <thead>
                        <tr>
                            <?php foreach ($context['user_headers'] as $header) : ?>
                                <th><?php echo esc_html($header); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($context['user_rows'] as $row) : ?>
                            <tr>
                                <?php foreach ($row['data'] as $cell) : ?>
                                    <td><?php echo $cell['value']; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($context['is_meeting_summary']) : ?>
        <h2><?php echo __('Meeting Summary', 'plugnmeet'); ?></h2>
        <div>
            <?php echo $context['meeting_summary_content']; ?>
        </div>
    <?php endif; ?>
</div>
