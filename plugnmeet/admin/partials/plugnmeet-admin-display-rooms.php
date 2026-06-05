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

<div class="wrap rooms-list-table">
    <h1 class="wp-heading-inline"><?php echo __( 'Rooms', 'plugnmeet' ); ?></h1>
    <a href="<?php echo admin_url( 'admin.php?page=plugnmeet&task=add' ); ?>"
       class="page-title-action"><?php echo __( 'Add New', 'plugnmeet' ); ?></a>
    <hr class="wp-header-end">

    <form method="post">
        <?php
        $rooms_list_table->search_box( __( 'Search', 'plugnmeet' ), 'search_id' );
        $rooms_list_table->display();
        ?>
    </form>
</div>
