<?php
/**
 *
 * @since      1.0.0
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

class Plugnmeet_SettingsPage {
	private $hasError = false;
	private $show = false;
	private $isRegister = false;

	private $allowedHtml = array(
		'input'  => array(
			'type'             => array(),
			'id'               => array(),
			'name'             => array(),
			'value'            => array(),
			'class'            => array(),
			'style'            => array(),
			'data-attached-to' => array()
		),
		'select' => array(
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'class' => array(),
		),
		'option' => array(
			'value'    => array(),
			'selected' => array(),
		),
	);

	public function __construct() {
	}

	public function textCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id        = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$required  = isset( $args['required'] ) ? esc_attr( $args['required'] ) : '';
		$className = isset( $args['className'] ) ? esc_attr( $args['className'] ) : '';
		$value     = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];

		$html = '<input id="' . $id . '" class="' . $className . '" ' . $required . ' name="plugnmeet_settings[' . $id . ']" type="text" size="40" value="' . $value . '">';
		echo wp_kses( $html, $this->allowedHtml );
	}

	public function selectCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id            = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$value         = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];
		$selectOptions = $args['options'];

		$html = '<select id="' . $args['id'] . '" name="plugnmeet_settings[' . $id . ']" value="' . $value . '">';

		foreach ( $selectOptions as $option ) {
			if ( $value === $option ) {
				$html .= '<option value="' . $option . '" selected>' . $option . '</option>';
			} else {
				$html .= '<option value="' . $option . '" >' . $option . '</option>';
			}
		}

		$html .= '</select>';

		echo wp_kses( $html, $this->allowedHtml );
	}

	public function numberCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id       = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$required = isset( $args['required'] ) ? esc_attr( $args['required'] ) : '';
		$value    = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];

		$html = '<input id="' . $id . '" required="' . $required . '" name="plugnmeet_settings[' . $id . ']" type="number" size="10" value="' . $value . '">';
		echo wp_kses( $html, $this->allowedHtml );
	}

	public function mediaCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );
		$id      = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$value   = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : "";

		$html = '<input style="margin-right: 20px;" id="' . $id . '" type="text" size="36" name="plugnmeet_settings[' . $id . ']" value="' . $value . '" />';
		$html .= '<input data-attached-to="' . $id . '" class="button upload_media_button" type="button" value="' . __( 'Upload/Select image', 'plugnmeet' ) . '" />';

		echo wp_kses( $html, $this->allowedHtml );
	}

	public function clientUpdateCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id       = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$required = isset( $args['required'] ) ? esc_attr( $args['required'] ) : '';
		$value    = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];

		$html = '<input style="margin-right: 20px;" id="' . $id . '" required="' . $required . '" name="plugnmeet_settings[' . $id . ']" type="text" size="40" value="' . $value . '">';
		$html .= '<input id="update_client_button" class="button" type="button" value="' . __( 'Update', 'plugnmeet' ) . '" />';

		echo wp_kses( $html, $this->allowedHtml );
	}

	public function validation( $input ) {
		$options        = get_option( 'plugnmeet_settings' );
		$requiredFields = array(
			"plugnmeet_server_url",
			"plugnmeet_api_key",
			"plugnmeet_secret",
			"livekit_server_url",
			"client_download_url"
		);
		foreach ( $input as $key => $val ) {
			if ( ! $val && in_array( $key, $requiredFields ) ) {
				if ( ! $this->hasError ) {
					add_settings_error(
						'plugnmeet_settings',
						'Missing value error',
						__( $key . ' can\'t be empty.', 'plugnmeet' ),
						'error'
					);
					$this->hasError = true;
				}

				return $options;
			}
		}
		if ( ! $this->hasError && ! $this->show ) {
			$this->show = true;
			add_settings_error(
				'plugnmeet_settings',
				'Success',
				__( 'Setting saved', 'plugnmeet' ),
				'success'
			);
		}

		return $input;
	}

	public function checkError() {
		if ( ! $this->isRegister ) {
			$this->isRegister = true;
			settings_errors( 'plugnmeet_settings' );
		}
	}

	public function plugnmeet_register_settings() {
		register_setting(
			'plugnmeet_settings',
			'plugnmeet_settings',
			[ $this, 'validation' ]
		);

		$this->configSection();
		$this->optionsSection();
		$this->designCustomization();
	}

	private function configSection() {
		add_settings_section(
			'plugnmeet_settings_config_section',
			__( 'Server Settings', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		add_settings_field(
			'plugnmeet_server_url',
			__( 'plugNmeet Server URL', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_config_section',
			[ 'id' => 'plugnmeet_server_url', 'required' => "required", 'default' => "https://demo.plugnmeet.com" ]
		);

		add_settings_field(
			'plugnmeet_api_key',
			__( 'plugNmeet API Key', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_config_section',
			[ 'id' => 'plugnmeet_api_key', 'required' => "required", 'default' => "plugnmeet" ]
		);

		add_settings_field(
			'plugnmeet_secret',
			__( 'plugNmeet Secret', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_config_section',
			[
				'id'       => 'plugnmeet_secret',
				'required' => "required",
				'default'  => "zumyyYWqv7KR2kUqvYdq4z4sXg7XTBD2ljT6"
			]
		);

		add_settings_field(
			'client_load',
			__( 'Client load', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_config_section',
			[ 'id' => 'client_load', 'options' => array( "remote", "local" ), 'default' => "automatic" ]
		);

		add_settings_field(
			'client_download_url',
			__( 'Local client download url', 'plugnmeet' ),
			[ $this, 'clientUpdateCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_config_section',
			[
				'id'       => 'client_download_url',
				'required' => "required",
				'default'  => "https://github.com/mynaparrot/plugNmeet-client/releases/latest/download/client.zip"
			]
		);
	}

	private function optionsSection() {
		add_settings_section(
			'plugnmeet_settings_options_section',
			__( 'Options', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		add_settings_field(
			'enable_dynacast',
			__( 'Enable Dynacast', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'enable_dynacast', 'options' => array( "true", "false" ), 'default' => "true" ]
		);

		add_settings_field(
			'enable_simulcast',
			__( 'Enable Simulcast', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'enable_simulcast', 'options' => array( "true", "false" ), 'default' => "true" ]
		);

		add_settings_field(
			'video_codec',
			__( 'Video Codec', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'video_codec', 'options' => array( 'vp8', 'h264', 'vp9', 'av1' ), 'default' => "vp8" ]
		);

		add_settings_field(
			'default_webcam_resolution',
			__( 'Webcam Resolution', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[
				'id'      => 'default_webcam_resolution',
				'options' => array( 'h90', 'h180', 'h216', 'h360', 'h540', 'h720', 'h1080', 'h1440', 'h2160' ),
				'default' => "h720"
			]
		);

		add_settings_field(
			'default_screen_share_resolution',
			__( 'Screen Sharing Resolution', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[
				'id'      => 'default_screen_share_resolution',
				'options' => array( 'h360fps3', 'h720fps5', 'h720fps15', 'h1080fps15', 'h1080fps30' ),
				'default' => "h1080fps15"
			]
		);

		add_settings_field(
			'default_audio_preset',
			__( 'Audio preset', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[
				'id'      => 'default_audio_preset',
				'options' => array(
					'telephone',
					'speech',
					'music',
					'musicStereo',
					'musicHighQuality',
					'musicHighQualityStereo'
				),
				'default' => "music"
			]
		);

		add_settings_field(
			'stop_mic_track_on_mute',
			__( 'Stop mic track on mute', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'stop_mic_track_on_mute', 'options' => array( "true", "false" ), 'default' => "true" ]
		);

		add_settings_field(
			'logo',
			__( 'Custom logo', 'plugnmeet' ),
			[ $this, 'mediaCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'logo' ]
		);

		// copyright
		add_settings_field(
			'copyright_display',
			__( 'Display copyright text', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'copyright_display', 'options' => array( "true", "false" ), 'default' => "false" ]
		);
		add_settings_field(
			'copyright_text',
			__( 'Copyright text', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[
				'id'      => 'copyright_text',
				'default' => "Powered by plugNmeet"
			]
		);
	}

	private function designCustomization() {
		add_settings_section(
			'plugnmeet_design_customization_section',
			__( 'Design Customization', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		add_settings_field(
			'custom_css_url',
			__( 'Custom CSS URL', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'custom_css_url' ]
		);

		add_settings_field(
			'primary_color',
			__( 'Primary Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'primary_color', 'className' => 'colorPickerItem' ]
		);

		add_settings_field(
			'secondary_color',
			__( 'Secondary Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'secondary_color', 'className' => 'colorPickerItem' ]
		);

		add_settings_field(
			'background_color',
			__( 'Background Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'background_color', 'className' => 'colorPickerItem' ]
		);

		add_settings_field(
			'background_image',
			__( 'Background Image', 'plugnmeet' ),
			[ $this, 'mediaCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'background_image' ]
		);

		add_settings_field(
			'header_color',
			__( 'Header Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'header_color', 'className' => 'colorPickerItem' ]
		);

		add_settings_field(
			'footer_color',
			__( 'Footer Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'footer_color', 'className' => 'colorPickerItem' ]
		);

		add_settings_field(
			'left_color',
			__( 'left Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'left_color', 'className' => 'colorPickerItem' ]
		);

		add_settings_field(
			'right_color',
			__( 'Right Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_design_customization_section',
			[ 'id' => 'right_color', 'className' => 'colorPickerItem' ]
		);
	}
}
