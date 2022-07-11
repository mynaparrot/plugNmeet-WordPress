<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * AFAIK nothing can be done with textdomains else than loading it.
 * This, if true, makes this class a total waste of code.
 *
 * @since      1.0.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/includes
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

class Plugnmeet_I18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain('plugnmeet', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');

    }

}
