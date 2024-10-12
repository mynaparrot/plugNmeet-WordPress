<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress or ClassicPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.plugnmeet.org
 * @since             1.0.0
 * @package           Plugnmeet
 *
 * @wordpress-plugin
 * Plugin Name:       Plug-N-Meet web conference integration
 * Plugin URI:        https://github.com/mynaparrot/plugNmeet-WordPress
 * Description:       Plug-N-Meet web conference integration with WordPress
 * Version:           1.2.11
 * Author:            Jibon L. Costa <jibon@mynaparrot.com>
 * Requires at least: 5.9
 * Requires PHP:      7.4.0
 * Tested up to:      6.6.2
 * Author URI:        https://www.mynaparrot.com/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugnmeet
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGNMEET_VERSION', '1.2.11' );

/**
 * Define the Plugin basename
 */
define( 'PLUGNMEET_BASE_NAME', plugin_basename( __FILE__ ) );

define( 'PLUGNMEET_ROOT_PATH', dirname( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * This action is documented in includes/class-plugnmeet-activator.php
 * Full security checks are performed inside the class.
 */
function plugnmeet_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugnmeet-activator.php';
	Plugnmeet_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * This action is documented in includes/class-plugnmeet-deactivator.php
 * Full security checks are performed inside the class.
 */
function plugnmeet_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugnmeet-deactivator.php';
	Plugnmeet_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'plugnmeet_activate' );
register_deactivation_hook( __FILE__, 'plugnmeet_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugnmeet.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Generally you will want to hook this function, instead of calling it globally.
 * However since the purpose of your plugin is not known until you write it, we include the function globally.
 *
 * @since    1.0.0
 */
function plugnmeet_run() {

	$plugin = new Plugnmeet();
	$plugin->run();

}

plugnmeet_run();
