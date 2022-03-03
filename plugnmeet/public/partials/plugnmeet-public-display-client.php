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

$params = (object)get_option("plugnmeet_settings");
$clientPath = PLUGNMEET_ROOT_PATH . "/public/client/dist/assets";

$jsFiles = preg_grep('~\.(js)$~', scandir($clientPath . "/js", SCANDIR_SORT_DESCENDING));
$cssFiles = preg_grep('~\.(css)$~', scandir($clientPath . "/css", SCANDIR_SORT_DESCENDING));

$path = plugins_url('public/client/dist/assets', PLUGNMEET_BASE_NAME);
$jsTag = "";
foreach ($jsFiles as $file) {
    $jsTag .= '<script src="' . $path . '/js/' . $file . '" defer="defer"></script>' . "\n\t";
}

$cssTag = "";
foreach ($cssFiles as $file) {
    $cssTag .= '<link href="' . $path . '/css/' . $file . '" rel="stylesheet" />' . "\n\t";
}
$customLogo = "";
if ($params->logo) {
    $customLogo = 'window.CUSTOM_LOGO = "' . $params->logo . '";';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo esc_html($_GET['room_title']); ?></title>
    <?php echo $cssTag . $jsTag; ?>

    <script type="text/javascript">
        window.PLUG_N_MEET_SERVER_URL = "<?php echo $params->plugnmeet_server_url; ?>";
        window.LIVEKIT_SERVER_URL = "<?php echo $params->livekit_server_url; ?>";
        window.STATIC_ASSETS_PATH = "<?php echo $path; ?>";
        <?php echo $customLogo; ?>

        Window.ENABLE_DYNACAST = <?php echo filter_var($params->enable_dynacast, FILTER_VALIDATE_BOOLEAN); ?>;
        window.ENABLE_SIMULCAST = <?php echo filter_var($params->enable_simulcast, FILTER_VALIDATE_BOOLEAN); ?>;
        window.STOP_MIC_TRACK_ON_MUTE = <?php echo filter_var($params->stop_mic_track_on_mute, FILTER_VALIDATE_BOOLEAN); ?>;
        window.NUMBER_OF_WEBCAMS_PER_PAGE_PC = <?php echo (int)$params->number_of_webcams_per_page_pc; ?>;
        window.NUMBER_OF_WEBCAMS_PER_PAGE_MOBILE = <?php echo (int)$params->number_of_webcams_per_page_mobile; ?>;
    </script>
</head>
<body>
<div id="plugNmeet-app"></div>
</body>
</html>

