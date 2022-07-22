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

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

$totalPages = ceil($totalNumRooms / $limit);
$currentPage = isset($_GET['paged']) ? sanitize_text_field($_GET['paged']) : 1;

$showNext = false;
if ($totalPages > 1 && $currentPage < $totalPages) {
    $showNext = true;
}

$showPre = false;
if ($currentPage > 1) {
    $showPre = true;
}
$url = "admin.php?page=plugnmeet";
if (isset($_GET['search_term'])) {
    $url .= "&search_term=" . sanitize_text_field($_GET['search_term']);
}
?>

<div class="tablenav bottom">
    <div class="alignright actions">
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $totalNumRooms ?> items</span>

            <?php if ($showPre): ?>
                <a class="next-page button"
                   href="<?php echo $url . "&paged=" . ($currentPage - 1) ?>">
                    <span class="screen-reader-text"><?php echo __("Previous page", "plugnmeet") ?></span>
                    <span aria-hidden="true">‹</span></a>
            <?php else: ?>
                <span class="pagination-links">
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
            </span>
            <?php endif; ?>

            <span class="screen-reader-text"><?php echo __("Current Page", "plugnmeet") ?></span>
            <span id="table-paging" class="paging-input">
                <span
                        class="tablenav-paging-text"><?php echo $currentPage ?> of <span
                            class="total-pages"><?php echo $totalPages ?>
                    </span>
                </span>
            </span>

            <?php if ($showNext): ?>
                <a class="next-page button"
                   href="<?php echo $url . "&paged=" . ($currentPage + 1) ?>">
                    <span class="screen-reader-text"><?php echo __("Next page", "plugnmeet") ?></span>
                    <span aria-hidden="true">›</span></a>
            <?php else: ?>
                <span class="pagination-links">
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
            </span>
            <?php endif; ?>
            <br class="clear">
        </div>
    </div>
</div>
