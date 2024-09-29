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

$user = wp_get_current_user();
$role = array(
	'require_password' => "on",
	'join_as'          => 'attendee',
	'can_download'     => "off",
	'can_delete'       => "off"
);

if ( ! empty( $roomInfo->roles ) ) {
	$roles    = json_decode( $roomInfo->roles, true );
	$userRole = 'guest';

	if ( $user->ID ) {
		$userRole = $user->roles[0]; // at present let's consider the first one only
	}

	if ( isset( $roles[ $userRole ] ) ) {
		$role = $roles[ $userRole ];
	}
}
?>

<div class="pnm-container">
    <div class="column column-full">
        <div class="description"><?php echo wp_kses_post( $roomInfo->description ) ?></div>
        <hr/>
		<?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/parts/login-form.php'; ?>

		<?php if ( isset( $role['can_view_recording'] ) && $role['can_view_recording'] === "on" ): ?>
			<?php require plugin_dir_path( dirname( __FILE__ ) ) . '/partials/parts/recordings.php'; ?>
		<?php endif; ?>
    </div>
    <script>
        // check if returned from conference
        const params = new URLSearchParams(document.location.search);
        if (params.has("pnm-returned", "true")) {
            // this will only work if link opened with window.open()
            window.close();
        }
    </script>
</div>
