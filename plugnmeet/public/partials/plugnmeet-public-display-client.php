<?php
/**
 * This template provides a "blank slate" environment for the external React application.
 * It aggressively removes all WordPress theme/plugin assets to prevent conflicts.
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

// Remove unnecessary meta tags and actions from wp_head for a cleaner output.
remove_action( 'wp_head', '_wp_render_title_tag', 1 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', '_admin_bar_bump_cb' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_resource_hints', 2 );
remove_action( 'wp_head', 'start_post_rel_link' );
remove_action( 'wp_head', 'index_rel_link' );

// This global will hold the handle of the main JS module to identify it in the filter.
$pnm_main_module_handle = '';

/**
 * This is the core function for creating the "blank slate".
 * It runs late, enqueues the app's assets, and then overwrites the global script/style queues
 * to ensure nothing else is loaded.
 */
function pnm_final_asset_loader() {
    global $wp_styles, $wp_scripts, $pnm_main_module_handle;

    // 1. Define the assets your app needs.
    $setting_params = (object) get_option( "plugnmeet_settings" );
    if ( ! isset( $setting_params->client_load ) || $setting_params->client_load === "remote" ) {
        if ( ! class_exists( "plugNmeetConnect" ) ) {
            require PLUGNMEET_ROOT_PATH . '/helpers/plugNmeetConnect.php';
        }
        $connect  = new plugNmeetConnect( $setting_params );
        $files    = $connect->getClientFiles();
        $jsFiles  = $files->getJSFiles() ?? [];
        $cssFiles = $files->getCSSFiles() ?? [];
        $path     = $setting_params->plugnmeet_server_url . "/assets";
        if ( ! empty( $files->getStaticAssetsPath() ) ) {
            $path = $files->getStaticAssetsPath();
        }
    } else {
        $clientPath = PLUGNMEET_ROOT_PATH . "/public/client/dist/assets";
        $jsFiles    = array_values( preg_grep( '~\.(js)$~', scandir( $clientPath . "/js", SCANDIR_SORT_DESCENDING ) ) );
        $cssFiles   = array_values( preg_grep( '~\.(css)$~', scandir( $clientPath . "/css", SCANDIR_SORT_DESCENDING ) ) );
        $path       = plugins_url( 'public/client/dist/assets', PLUGNMEET_BASE_NAME );
    }

    $allowed_js_handles = [];
    foreach ( $jsFiles as $file ) {
        $handle = $file; // Use the filename as the handle.
        $url    = $path . '/js/' . $file;
        $args   = [ 'strategy' => 'defer' ]; // Default to defer for all scripts.

        if ( str_starts_with( $handle, 'main-module.' ) ) {
            $pnm_main_module_handle = $handle;
            $args                   = []; // The main module gets special handling, so no defer.
        }

        $allowed_js_handles[] = $handle;
        wp_enqueue_script( $handle, $url, [], null, $args );
    }

    $allowed_css_handles = [];
    foreach ( $cssFiles as $file ) {
        $handle                = $file;
        $allowed_css_handles[] = $handle;
        wp_enqueue_style( $handle, $path . '/css/' . $file, [], null );
    }

    // 2. Aggressively overwrite the queues.
    // This ensures ONLY your assets are listed for printing.
    $wp_scripts->queue = $allowed_js_handles;
    $wp_styles->queue  = $allowed_css_handles;
}

add_action( 'wp_print_styles', 'pnm_final_asset_loader', 9999 );


/**
 * Filters the script tag to add `type="module"`.
 *
 * @param string $tag The <script> tag for the enqueued script.
 * @param string $handle The script's handle.
 *
 * @return string The modified <script> tag.
 */
function pnm_script_loader_tag_filter( $tag, $handle ) {
    global $pnm_main_module_handle;

    // Only apply module logic to the main script. Defer is now handled by wp_enqueue_script.
    if ( $pnm_main_module_handle === $handle ) {
        // This regex robustly finds an existing 'type' attribute.
        if ( preg_match( '/\s*type\s*=\s*([\'"]?)[^\s>]*\1/', $tag ) ) {
            // If the attribute exists, replace it.
            $tag = preg_replace( '/\s*type\s*=\s*([\'"]?)[^\s>]*\1/', ' type="module"', $tag );
        } else {
            // If it doesn't exist, add it.
            $tag = str_replace( '<script', '<script type="module"', $tag );
        }
    }

    return $tag;
}

add_filter( 'script_loader_tag', 'pnm_script_loader_tag_filter', 10, 2 );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>plugNmeet</title>
    <?php wp_head(); ?>
    <?php wp_print_inline_script_tag( $jsOptions ); ?>
</head>
<body>
<div id="plugNmeet-app"></div>
</body>
</html>
