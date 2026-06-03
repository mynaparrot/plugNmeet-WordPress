<?php

/**
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/helpers
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

namespace PlugNmeet\Helpers;

use Exception;
use Mynaparrot\PlugnmeetProto\CommonNotifyEvent;
use WP_REST_Request;
use WP_REST_Response;

class WebhookReceiver {

	/**
	 * @var object
	 */
	private $setting_params;

	public function __construct() {
		$this->setting_params = (object) get_option( "plugnmeet_settings" );
		if ( ! class_exists( "plugNmeetConnect" ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
		}
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function handle( WP_REST_Request $request ): WP_REST_Response {
		try {
			$authorizationHeader = $request->get_header( 'Authorization' );

			if ( empty( $authorizationHeader ) ) {
				return new WP_REST_Response( [
					'status'  => 'error',
					'message' => 'Authorization header missing'
				], 401 );
			}
			
			$connect = new \plugNmeetConnect( $this->setting_params );
			$jwt     = $connect->getPlugnmeet()->decodeJWTData( $authorizationHeader );

			if ( ! isset( $jwt->sha256 ) ) {
				return new WP_REST_Response( [ 'status' => 'error', 'message' => 'Invalid JWT token' ], 401 );
			}

			$body            = $request->get_body();
			$hash            = hash( 'sha256', $body, true );
			$decodedSentHash = base64_decode( $jwt->sha256 );

			if ( $hash !== $decodedSentHash ) {
				return new WP_REST_Response( [ 'status' => 'error', 'message' => 'Hash mismatch' ], 401 );
			}

			$webhook = new CommonNotifyEvent();
			$webhook->mergeFromJsonString( $body, true );

			switch ( $webhook->getEvent() ) {
				case 'recording_proceeded':
					wp_cache_flush_group( 'pnm_recordings' );
					break;
				case 'artifact_created':
					wp_cache_flush_group( 'pnm_artifacts' );
					break;
			}

			do_action( 'plugnmeet_webhook_data', $webhook );
		} catch ( Exception $e ) {
			return new WP_REST_Response( [
				'status'  => 'error',
				'message' => $e->getMessage()
			], 500 );
		}

		return new WP_REST_Response( [ 'status' => 'success' ], 200 );
	}
}
