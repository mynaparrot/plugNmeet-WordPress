<?php
/**
 *
 * @link       https://www.mynaparrot.com
 * @since      1.0.0
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/public/partials
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'adjacent_posts_rel_link');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', '_admin_bar_bump_cb');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_resource_hints', 2);
remove_action('wp_head', 'start_post_rel_link');
remove_action('wp_head', 'index_rel_link');

function insert_plnm_js_css()
{
    global $wp_styles, $wp_scripts;

    $clientPath = PLUGNMEET_ROOT_PATH . "/public/client/dist/assets";
    $jsFiles = preg_grep('~\.(js)$~', scandir($clientPath . "/js", SCANDIR_SORT_DESCENDING));
    $cssFiles = preg_grep('~\.(css)$~', scandir($clientPath . "/css", SCANDIR_SORT_DESCENDING));
    $path = plugins_url('public/client/dist/assets', PLUGNMEET_BASE_NAME);

    foreach ($jsFiles as $file) {
        wp_enqueue_script($file, $path . '/js/' . $file, array(), null);
    }
    foreach ($cssFiles as $file) {
        wp_enqueue_style($file, $path . '/css/' . $file, array(), null);
    }

    foreach ($wp_styles->queue as $style) {
        if (in_array($style, $cssFiles))
            continue;
        $wp_styles->remove($style);
    }
    foreach ($wp_scripts->queue as $script) {
        if (in_array($script, $jsFiles))
            continue;
        $wp_scripts->remove($script);
    }
}

add_action('wp_print_styles', 'insert_plnm_js_css', 100);

function add_custom_attr($tag)
{
    $tag = str_replace('src=', 'defer="defer" src=', $tag);
    return $tag;
}

add_filter('script_loader_tag', 'add_custom_attr', 10, 3);


$params = (object)get_option("plugnmeet_settings");
$assets_path = plugins_url('public/client/dist/assets', PLUGNMEET_BASE_NAME);

$customLogo = "";
if ($params->logo) {
    $customLogo = 'window.CUSTOM_LOGO = "' . esc_url_raw($params->logo) . '";';
}

$js = 'window.PLUG_N_MEET_SERVER_URL = "' . esc_url_raw($params->plugnmeet_server_url) . '";';
$js .= 'window.LIVEKIT_SERVER_URL = "' . esc_url_raw($params->livekit_server_url) . '";';
$js .= 'window.STATIC_ASSETS_PATH = "' . esc_url_raw($assets_path) . '";';
$js .= $customLogo;
$js .= 'window.ENABLE_DYNACAST = "' . filter_var($params->enable_dynacast, FILTER_VALIDATE_BOOLEAN) . '";';
$js .= 'window.ENABLE_SIMULCAST = "' . filter_var($params->enable_simulcast, FILTER_VALIDATE_BOOLEAN) . '";';

$js .= 'window.STOP_MIC_TRACK_ON_MUTE = "' . filter_var($params->stop_mic_track_on_mute, FILTER_VALIDATE_BOOLEAN) . '";';
$js .= 'window.NUMBER_OF_WEBCAMS_PER_PAGE_PC = "' . filter_var($params->number_of_webcams_per_page_pc, FILTER_VALIDATE_INT) . '";';
$js .= 'window.NUMBER_OF_WEBCAMS_PER_PAGE_MOBILE = "' . filter_var($params->number_of_webcams_per_page_mobile, FILTER_VALIDATE_INT) . '";';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php wp_head(); ?>
    <?php wp_print_inline_script_tag($js); ?>
</head>
<body>
<div id="plugNmeet-app"></div>
</body>
</html>
