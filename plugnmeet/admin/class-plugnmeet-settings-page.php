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
		'option'   => array(
			'value'    => array(),
			'selected' => array(),
		),
		'textarea' => array(
			'id'          => array(),
			'name'        => array(),
			'class'       => array(),
			'rows'        => array(),
			'cols'        => array(),
			'placeholder' => array(),
		),
	);

	public function __construct() {
	}

	public function plugnmeet_register_settings() {
		register_setting(
			'plugnmeet_settings',
			'plugnmeet_settings',
			[ $this, 'validation' ]
		);

		$this->configSection();
		$this->optionsSection();
		$this->frontendDesignSection();
		$this->clientDesignSection();
		$this->aiAgentSection();
		$this->aiAvatarSection();
		$this->aiByokSection();
		$this->aiMcpSection();
	}

	public function textCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id        = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$required  = isset( $args['required'] ) ? esc_attr( $args['required'] ) : '';
		$className = isset( $args['className'] ) ? esc_attr( $args['className'] ) : '';
		$type      = isset( $args['type'] ) ? esc_attr( $args['type'] ) : 'text';
		$value     = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];

		$html = '<input id="' . $id . '" class="' . $className . '" ' . $required . ' name="plugnmeet_settings[' . $id . ']" type="' . $type . '" size="40" value="' . $value . '">';
		echo wp_kses( $html, $this->allowedHtml );

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
	}

	public function textareaCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id          = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$className   = isset( $args['className'] ) ? esc_attr( $args['className'] ) : '';
		$rows        = isset( $args['rows'] ) ? (int) $args['rows'] : 5;
		$cols        = isset( $args['cols'] ) ? (int) $args['cols'] : 60;
		$placeholder = isset( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';
		$value       = isset( $options[ $id ] ) ? $options[ $id ] : $args['default'];

		$html = '<textarea id="' . $id . '" class="' . $className . '" name="plugnmeet_settings[' . $id . ']" rows="' . $rows . '" cols="' . $cols . '" placeholder="' . $placeholder . '">' . esc_textarea( $value ) . '</textarea>';
		echo wp_kses( $html, $this->allowedHtml );

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
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

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
	}

	public function labeledSelectCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id            = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$value         = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];
		$selectOptions = $args['options'];

		$html = '<select id="' . $id . '" name="plugnmeet_settings[' . $id . ']">';

		foreach ( $selectOptions as $optionValue => $optionLabel ) {
			$selected = ( $value === $optionValue ) ? ' selected' : '';
			$html     .= '<option value="' . esc_attr( $optionValue ) . '"' . $selected . '>' . $optionLabel . '</option>';
		}

		$html .= '</select>';

		echo wp_kses( $html, $this->allowedHtml );

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
	}

	public function pageSelectCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );
		$id      = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$value   = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : '';

		$pages = get_pages();
		$html  = '<select id="' . $id . '" name="plugnmeet_settings[' . $id . ']">';
		$html  .= '<option value="">' . __( 'Select a page', 'plugnmeet' ) . '</option>';

		foreach ( $pages as $page ) {
			$selected = ( $value == $page->ID ) ? 'selected="selected"' : '';
			$html     .= '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';
		}

		$html .= '</select>';
		echo $html;

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
	}

	public function numberCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id       = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$required = isset( $args['required'] ) ? esc_attr( $args['required'] ) : '';
		$value    = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];

		$html = '<input id="' . $id . '" required="' . $required . '" name="plugnmeet_settings[' . $id . ']" type="number" size="10" value="' . $value . '">';
		echo wp_kses( $html, $this->allowedHtml );

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
	}

	public function mediaCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );
		$id      = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$value   = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : "";

		$html = '<input style="margin-right: 20px;" id="' . $id . '" type="text" size="36" name="plugnmeet_settings[' . $id . ']" value="' . $value . '" />';
		$html .= '<input data-attached-to="' . $id . '" class="button upload_media_button" type="button" value="' . __( 'Upload/Select image', 'plugnmeet' ) . '" />';

		echo wp_kses( $html, $this->allowedHtml );

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
	}

	public function clientUpdateCallBack( $args ) {
		$options = get_option( 'plugnmeet_settings' );

		$id       = isset( $args['id'] ) ? esc_attr( $args['id'] ) : '';
		$required = isset( $args['required'] ) ? esc_attr( $args['required'] ) : '';
		$value    = isset( $options[ $id ] ) ? esc_attr( $options[ $id ] ) : $args['default'];

		$html = '<input style="margin-right: 20px;" id="' . $id . '" required="' . $required . '" name="plugnmeet_settings[' . $id . ']" type="text" size="40" value="' . $value . '">';
		$html .= '<input id="update_client_button" class="button" type="button" value="' . __( 'Update', 'plugnmeet' ) . '" />';

		echo wp_kses( $html, $this->allowedHtml );

		echo plugnmeet_help_text_html( 'settings.' . $args['id'] );
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
			if ( is_string( $val ) ) {
				$val           = trim( $val );
				$input[ $key ] = $val;
			}

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
		if ( isset( $input['plugnmeet_server_url'] ) ) {
			$input['plugnmeet_server_url'] = rtrim( $input['plugnmeet_server_url'], '/' );
		}

		if ( ! $this->hasError && ! $this->show ) {
			$this->show = true;
			add_settings_error(
				'plugnmeet_settings',
				'Success',
				__( 'Setting saved', 'plugnmeet' ),
				'success'
			);
			flush_rewrite_rules();
		}

		// Frontend design fields: sanitize only these new keys.
		if ( isset( $input['frontend_theme'] ) ) {
			$input['frontend_theme'] = in_array( $input['frontend_theme'], array( 'theme', 'clean', 'modern' ), true ) ? $input['frontend_theme'] : 'theme';
		} else {
			$input['frontend_theme'] = 'theme';
		}
		foreach ( array( 'frontend_primary_color', 'frontend_secondary_color' ) as $color_key ) {
			if ( isset( $input[ $color_key ] ) && ! preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $input[ $color_key ] ) ) {
				$input[ $color_key ] = '';
			}
		}

		return $input;
	}

	public function checkError() {
		if ( ! $this->isRegister ) {
			$this->isRegister = true;
			settings_errors( 'plugnmeet_settings' );
		}
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
			[ 'id' => 'plugnmeet_api_key', 'required' => "required", 'default' => "plugnmeet", 'type' => 'password' ]
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
				'type'     => 'password',
				'default'  => "zumyyYWqv7KR2kUqvYdq4z4sXg7XTBD2ljT6"
			]
		);

		add_settings_field(
			'client_load',
			__( 'Client load', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_config_section',
			[ 'id' => 'client_load', 'options' => array( "remote", "local", "redirect" ), 'default' => "remote" ]
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
			'room_host_page',
			__( 'Room Host Page', 'plugnmeet' ),
			[ $this, 'pageSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'room_host_page' ]
		);

		add_settings_field(
			'room_slug_path',
			__( 'Room Slug Path', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_options_section',
			[ 'id' => 'room_slug_path', 'required' => "required", 'default' => "plugnmeet/room/" ]
		);
	}

	private function frontendDesignSection() {
		add_settings_section(
			'plugnmeet_settings_frontend_design_section',
			__( 'Frontend design', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		add_settings_field(
			'frontend_theme',
			__( 'Frontend theme', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_frontend_design_section',
			[
				'id'      => 'frontend_theme',
				'options' => array(
					'theme'  => __( 'Theme default (no plugin CSS)', 'plugnmeet' ),
					'clean'  => __( 'Clean', 'plugnmeet' ),
					'modern' => __( 'Modern', 'plugnmeet' ),
				),
				'default' => 'theme'
			]
		);

		add_settings_field(
			'frontend_primary_color',
			__( 'Primary Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_frontend_design_section',
			[ 'id' => 'frontend_primary_color', 'className' => 'pnm-color-picker' ]
		);

		add_settings_field(
			'frontend_secondary_color',
			__( 'Secondary Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_frontend_design_section',
			[ 'id' => 'frontend_secondary_color', 'className' => 'pnm-color-picker' ]
		);
	}

	private function clientDesignSection() {
		add_settings_section(
			'plugnmeet_settings_client_design_section',
			__( 'plugNmeet Client design', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		add_settings_field(
			'custom_css_url',
			__( 'Custom CSS URL', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'custom_css_url' ]
		);

		add_settings_field(
			'primary_color',
			__( 'Primary Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'primary_color', 'className' => 'pnm-color-picker' ]
		);

		add_settings_field(
			'secondary_color',
			__( 'Secondary Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'secondary_color', 'className' => 'pnm-color-picker' ]
		);

		add_settings_field(
			'background_color',
			__( 'Background Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'background_color', 'className' => 'pnm-color-picker' ]
		);

		add_settings_field(
			'background_image',
			__( 'Background Image', 'plugnmeet' ),
			[ $this, 'mediaCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'background_image' ]
		);

		add_settings_field(
			'logo',
			__( 'Custom logo', 'plugnmeet' ),
			[ $this, 'mediaCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'logo' ]
		);

		add_settings_field(
			'header_color',
			__( 'Header Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'header_color', 'className' => 'pnm-color-picker' ]
		);

		add_settings_field(
			'footer_color',
			__( 'Footer Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'footer_color', 'className' => 'pnm-color-picker' ]
		);

		add_settings_field(
			'side_panel_bg_color',
			__( 'Side Panel Color', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'side_panel_bg_color', 'className' => 'pnm-color-picker' ]
		);

		// copyright
		add_settings_field(
			'copyright_display',
			__( 'Display copyright text', 'plugnmeet' ),
			[ $this, 'selectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[ 'id' => 'copyright_display', 'options' => array( "true", "false" ), 'default' => "false" ]
		);
		add_settings_field(
			'copyright_text',
			__( 'Copyright text', 'plugnmeet' ),
			[ $this, 'textCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_client_design_section',
			[
				'id'      => 'copyright_text',
				'default' => "Powered by plugNmeet"
			]
		);
	}

	private function aiAgentSection() {
		add_settings_section(
			'plugnmeet_settings_ai_agent_section',
			__( 'AI Agent & Autonomous Session Settings', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		$yesNo = array( '1' => __( 'Yes', 'plugnmeet' ), '0' => __( 'No', 'plugnmeet' ) );

		add_settings_field(
			'enable_ai_agent',
			__( 'Enable AI Agent', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_ai_agent_section',
			[ 'id' => 'enable_ai_agent', 'options' => $yesNo, 'default' => '1' ]
		);

		$numberFields = array(
			'ai_max_concurrent_sessions_limit' => [ __( 'Maximum concurrent AI sessions', 'plugnmeet' ), 10 ],
			'ai_default_max_session_minutes'   => [ __( 'Default AI session duration (minutes)', 'plugnmeet' ), 30 ],
			'ai_max_session_minutes_limit'     => [ __( 'Maximum AI session duration (minutes)', 'plugnmeet' ), 60 ],
		);

		foreach ( $numberFields as $id => $field ) {
			add_settings_field(
				$id,
				$field[0],
				[ $this, 'numberCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_agent_section',
				[ 'id' => $id, 'default' => $field[1] ]
			);
		}

		add_settings_field(
			'ai_allow_web_fetch_global',
			__( 'Allow web fetch globally', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_ai_agent_section',
			[ 'id' => 'ai_allow_web_fetch_global', 'options' => $yesNo, 'default' => '0' ]
		);

		add_settings_field(
			'ai_avatar_enabled_global',
			__( 'Allow AI avatar', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_ai_agent_section',
			[ 'id' => 'ai_avatar_enabled_global', 'options' => $yesNo, 'default' => '0' ]
		);
	}

	private function aiAvatarSection() {
		add_settings_section(
			'plugnmeet_settings_ai_avatar_section',
			__( 'AI Avatar (BYOK)', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		$yesNo = array( '1' => __( 'Yes', 'plugnmeet' ), '0' => __( 'No', 'plugnmeet' ) );

		add_settings_field(
			'ai_avatar_custom_enabled',
			__( 'Enable custom avatar provider (BYOK)', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_ai_avatar_section',
			[ 'id' => 'ai_avatar_custom_enabled', 'options' => $yesNo, 'default' => '0' ]
		);

		add_settings_field(
			'ai_avatar_provider',
			__( 'Avatar provider', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_ai_avatar_section',
			[
				'id'      => 'ai_avatar_provider',
				'options' => array(
					'beyond_presence' => __( 'Beyond Presence', 'plugnmeet' ),
					'anam'            => __( 'Anam', 'plugnmeet' ),
					'simli'           => __( 'Simli', 'plugnmeet' ),
				),
				'default' => 'beyond_presence'
			]
		);

		$textFields = array(
			'ai_avatar_id'          => [ __( 'Default avatar ID', 'plugnmeet' ), array() ],
			'ai_avatar_api_key'     => [ __( 'Avatar API key', 'plugnmeet' ), array( 'type' => 'password' ) ],
			'ai_avatar_endpoint'    => [ __( 'Avatar API endpoint', 'plugnmeet' ), array() ],
			'ai_avatar_auth_header' => [ __( 'Avatar authorization header', 'plugnmeet' ), array() ],
		);

		foreach ( $textFields as $id => $field ) {
			add_settings_field(
				$id,
				$field[0],
				[ $this, 'textCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_avatar_section',
				array_merge( array( 'id' => $id, 'default' => '' ), $field[1] )
			);
		}
	}

	private function aiByokSection() {
		add_settings_section(
			'plugnmeet_settings_ai_byok_section',
			__( 'Custom AI Provider (BYOK)', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		$yesNo = array( '1' => __( 'Yes', 'plugnmeet' ), '0' => __( 'No', 'plugnmeet' ) );

		add_settings_field(
			'ai_custom_provider_enabled',
			__( 'Enable custom AI provider', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_ai_byok_section',
			[ 'id' => 'ai_custom_provider_enabled', 'options' => $yesNo, 'default' => '0' ]
		);

		add_settings_field(
			'ai_custom_provider',
			__( 'Custom AI provider', 'plugnmeet' ),
			[ $this, 'labeledSelectCallBack' ],
			'plugnmeet-settings',
			'plugnmeet_settings_ai_byok_section',
			[
				'id'      => 'ai_custom_provider',
				'options' => array(
					'google' => __( 'Google Gemini', 'plugnmeet' ),
					'openai' => __( 'OpenAI / Compatible', 'plugnmeet' ),
				),
				'default' => 'google'
			]
		);

		$textFields = array(
			'ai_custom_api_key'         => [ __( 'Custom API key', 'plugnmeet' ), array( 'type' => 'password' ) ],
			'ai_custom_endpoint'        => [ __( 'Custom API endpoint', 'plugnmeet' ), array() ],
			'ai_custom_auth_header'     => [ __( 'Custom authorization header', 'plugnmeet' ), array() ],
			'ai_custom_realtime_model'  => [ __( 'Realtime model', 'plugnmeet' ), array() ],
			'ai_custom_delegation_model' => [ __( 'Delegation model', 'plugnmeet' ), array() ],
			'ai_custom_summarize_model' => [ __( 'Summarize model', 'plugnmeet' ), array() ],
		);

		foreach ( $textFields as $id => $field ) {
			add_settings_field(
				$id,
				$field[0],
				[ $this, 'textCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_byok_section',
				array_merge( array( 'id' => $id, 'default' => '' ), $field[1] )
			);
		}
	}

	private function aiMcpSection() {
		add_settings_section(
			'plugnmeet_settings_ai_mcp_section',
			__( 'MCP Servers', 'plugnmeet' ),
			[ $this, 'checkError' ],
			'plugnmeet-settings'
		);

		$yesNo  = array( '1' => __( 'Yes', 'plugnmeet' ), '0' => __( 'No', 'plugnmeet' ) );
		$scopes = array(
			'ai_agent'  => __( 'AI agent only', 'plugnmeet' ),
			'text_chat' => __( 'AI text chat only', 'plugnmeet' ),
			'shared'    => __( 'Shared (AI agent and text chat)', 'plugnmeet' ),
		);

		for ( $i = 1; $i <= 3; $i++ ) {
			add_settings_field(
				"ai_mcp_{$i}_enabled",
				sprintf( __( 'MCP server %d enabled', 'plugnmeet' ), $i ),
				[ $this, 'labeledSelectCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_mcp_section',
				[ 'id' => "ai_mcp_{$i}_enabled", 'options' => $yesNo, 'default' => '0' ]
			);

			add_settings_field(
				"ai_mcp_{$i}_name",
				sprintf( __( 'MCP server %d name', 'plugnmeet' ), $i ),
				[ $this, 'textCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_mcp_section',
				[ 'id' => "ai_mcp_{$i}_name", 'default' => '' ]
			);

			add_settings_field(
				"ai_mcp_{$i}_url",
				sprintf( __( 'MCP server %d URL', 'plugnmeet' ), $i ),
				[ $this, 'textCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_mcp_section',
				[ 'id' => "ai_mcp_{$i}_url", 'default' => '' ]
			);

			add_settings_field(
				"ai_mcp_{$i}_headers",
				sprintf( __( 'MCP server %d headers', 'plugnmeet' ), $i ),
				[ $this, 'textareaCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_mcp_section',
				[
					'id'          => "ai_mcp_{$i}_headers",
					'default'     => '',
					'rows'        => 3,
					'placeholder' => '{"Authorization": "Bearer ..."}'
				]
			);

			add_settings_field(
				"ai_mcp_{$i}_allowed_tools",
				sprintf( __( 'MCP server %d allowed tools', 'plugnmeet' ), $i ),
				[ $this, 'textCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_mcp_section',
				[ 'id' => "ai_mcp_{$i}_allowed_tools", 'default' => '' ]
			);

			add_settings_field(
				"ai_mcp_{$i}_scope",
				sprintf( __( 'MCP server %d scope', 'plugnmeet' ), $i ),
				[ $this, 'labeledSelectCallBack' ],
				'plugnmeet-settings',
				'plugnmeet_settings_ai_mcp_section',
				[ 'id' => "ai_mcp_{$i}_scope", 'options' => $scopes, 'default' => 'ai_agent' ]
			);
		}
	}
}
