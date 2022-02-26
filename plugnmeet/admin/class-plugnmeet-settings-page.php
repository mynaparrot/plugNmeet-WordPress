<?php
/**
 *
 * @since      1.0.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}

class Plugnmeet_SettingsPage
{
    private $hasError = false;
    private $show = false;

    public function __construct()
    {
    }

    public function textCallBack($args)
    {
        $options = get_option('plugnmeet_settings');

        $id = isset($args['id']) ? $args['id'] : '';
        $required = isset($args['required']) ? $args['required'] : '';
        $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : $args['default'];

        echo '<input id="' . $args['id'] . '" required="' . $required . '" name="plugnmeet_settings[' . $id . ']" type="text" size="40" value="' . $value . '">';
    }

    public function selectCallBack($args)
    {
        $options = get_option('plugnmeet_settings');

        $id = isset($args['id']) ? $args['id'] : '';
        $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : "true";
        $selectOptions = array("true", "false");

        $html = '<select id="' . $args['id'] . '" name="plugnmeet_settings[' . $id . ']" value="' . $value . '">';

        foreach ($selectOptions as $option) {
            if ($value === $option) {
                $html .= '<option value="' . $option . '" selected>' . $option . '</option>';
            } else {
                $html .= '<option value="' . $option . '" >' . $option . '</option>';
            }
        }

        $html .= '</select>';
        echo $html;
    }

    public function numberCallBack($args)
    {
        $options = get_option('plugnmeet_settings');

        $id = isset($args['id']) ? $args['id'] : '';
        $required = isset($args['required']) ? $args['required'] : '';
        $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : $args['default'];

        echo '<input id="' . $args['id'] . '" required="' . $required . '" name="plugnmeet_settings[' . $id . ']" type="number" size="10" value="' . $value . '">';
    }

    public function mediaCallBack()
    {
        $options = get_option('plugnmeet_settings');
        $id = 'logo';
        $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : "";

        $html = '<input style="margin-right: 20px;" id="upload_logo" type="text" size="36" name="plugnmeet_settings[' . $id . ']" value="' . $value . '" />';
        $html .= '<input id="upload_logo_button" class="button" type="button" value="' . __('Upload/Select image', 'plugnmeet') . '" />';

        echo $html;
    }

    public function clientUpdateCallBack($args)
    {
        $options = get_option('plugnmeet_settings');

        $id = isset($args['id']) ? $args['id'] : '';
        $required = isset($args['required']) ? $args['required'] : '';
        $value = isset($options[$id]) ? sanitize_text_field($options[$id]) : $args['default'];

        $html = '<input style="margin-right: 20px;" id="' . $args['id'] . '" required="' . $required . '" name="plugnmeet_settings[' . $id . ']" type="text" size="40" value="' . $value . '">';
        $html .= '<input id="update_client_button" class="button" type="button" value="' . __('Update', 'plugnmeet') . '" />';

        echo $html;
    }

    public function validation($input)
    {
        $options = get_option('plugnmeet_settings');
        foreach ($input as $key => $val) {
            if (!$val && $key !== "logo") {
                $this->hasError = true;
                add_settings_error(
                    'plugnmeet_settings',
                    'Missing value error',
                    __($key . ' can\'t be empty.', 'plugnmeet'),
                    'error'
                );
                return $options;
            }
        }
        if (!$this->hasError && !$this->show) {
            $this->show = true;
            add_settings_error(
                'plugnmeet_settings',
                'Success',
                __('Setting saved', 'plugnmeet'),
                'success'
            );
        }
        return $input;
    }

    public function checkError()
    {
        settings_errors('plugnmeet_settings');
    }

    public function plugnmeet_register_settings()
    {

        register_setting(
            'plugnmeet_settings',
            'plugnmeet_settings',
            [$this, 'validation']
        );

        $this->configSection();
        $this->optionsSection();
    }

    private function configSection()
    {
        add_settings_section(
            'plugnmeet_settings_config_section',
            __('Server Settings', 'plugnmeet'),
            [$this, 'checkError'],
            'plugnmeet-settings'
        );

        add_settings_field(
            'plugnmeet_server_url',
            __('plugNmeet Server URL', 'plugnmeet'),
            [$this, 'textCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_config_section',
            ['id' => 'plugnmeet_server_url', 'required' => "required"]
        );

        add_settings_field(
            'plugnmeet_api_key',
            __('plugNmeet API Key', 'plugnmeet'),
            [$this, 'textCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_config_section',
            ['id' => 'plugnmeet_api_key', 'required' => "required"]
        );

        add_settings_field(
            'plugnmeet_secret',
            __('plugNmeet Secret', 'plugnmeet'),
            [$this, 'textCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_config_section',
            ['id' => 'plugnmeet_secret', 'required' => "required"]
        );

        add_settings_field(
            'livekit_server_url',
            __('Livekit URL', 'plugnmeet'),
            [$this, 'textCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_config_section',
            ['id' => 'livekit_server_url', 'required' => "required"]
        );

        add_settings_field(
            'client_download_url',
            __('Client download url', 'plugnmeet'),
            [$this, 'clientUpdateCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_config_section',
            ['id' => 'client_download_url', 'required' => "required", 'default' => "https://github.com/mynaparrot/plugNmeet-client/releases/latest/download/client.zip"]
        );
    }

    private function optionsSection()
    {
        add_settings_section(
            'plugnmeet_settings_options_section',
            __('Options', 'plugnmeet'),
            [$this, 'checkError'],
            'plugnmeet-settings'
        );

        add_settings_field(
            'enable_dynacast',
            __('Enable Dynacast', 'plugnmeet'),
            [$this, 'selectCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_options_section',
            ['id' => 'enable_dynacast']
        );

        add_settings_field(
            'enable_simulcast',
            __('Enable Simulcast', 'plugnmeet'),
            [$this, 'selectCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_options_section',
            ['id' => 'enable_simulcast']
        );

        add_settings_field(
            'stop_mic_track_on_mute',
            __('Stop mic track on mute', 'plugnmeet'),
            [$this, 'selectCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_options_section',
            ['id' => 'stop_mic_track_on_mute']
        );

        add_settings_field(
            'number_of_webcams_per_page_pc',
            __('Webcams per page (PC)', 'plugnmeet'),
            [$this, 'numberCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_options_section',
            ['id' => 'number_of_webcams_per_page_pc', 'required' => "required", "default" => 25]
        );

        add_settings_field(
            'number_of_webcams_per_page_mobile',
            __('Webcams per page (mobile)', 'plugnmeet'),
            [$this, 'numberCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_options_section',
            ['id' => 'number_of_webcams_per_page_mobile', 'required' => "required", "default" => 6]
        );

        add_settings_field(
            'logo',
            __('Custom logo', 'plugnmeet'),
            [$this, 'mediaCallBack'],
            'plugnmeet-settings',
            'plugnmeet_settings_options_section'
        );
    }
}
