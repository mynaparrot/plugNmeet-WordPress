<?php
/**
 *
 * @since      1.0.10
 * @package    Plugnmeet
 * @subpackage Plugnmeet/helpers
 * @author     Jibon Costa <jibon@mynaparrot.com>
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}

class Plugnmeet_AiSummaryHelper {
	private $artifact_id;
	private $setting_params;
	private $summary = [];

	public function __construct( $artifact_id ) {
		$this->artifact_id    = $artifact_id;
		$this->setting_params = (object) get_option( "plugnmeet_settings" );

		if ( ! class_exists( "plugNmeetConnect" ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/plugNmeetConnect.php';
		}

		$key    = sprintf( "ai-summary-%s", $artifact_id );
		$cached = wp_cache_get( $key, "pnm_artifact" );
		if ( false !== $cached ) {
			$this->summary = $cached;

			return;
		}

		$pnc = new plugNmeetConnect( $this->setting_params );
		$res = $pnc->getArtifactDownloadToken( $this->artifact_id );
		if ( ! $res->getStatus() ) {
			throw new Exception( $res->getMsg() );
		}

		$server_url = rtrim( trim( (string) $this->setting_params->plugnmeet_server_url ), '/' );
		$response   = wp_remote_get( $server_url . "/download/artifact/" . $res->getToken(), array( 'timeout' => 60 ) );
		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		$summary = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $summary ) || ! is_array( $summary ) ) {
			throw new Exception( "Invalid AI Summary Data" );
		}

		$this->summary = $summary;
		wp_cache_set( $key, $summary, "pnm_artifact", DAY_IN_SECONDS );
	}

	public function get_summary_data() {
		$summary = $this->summary;

		$user_info        = (array) ( $summary['user_info'] ?? [] );
		$session          = (array) ( $summary['session'] ?? [] );
		$evaluation       = (array) ( $summary['evaluation'] ?? [] );
		$quiz             = (array) ( $summary['quiz_evaluation'] ?? [] );
		$workspace        = (array) ( $summary['workspace_activity'] ?? [] );
		$continuation     = (array) ( $summary['continuation'] ?? [] );
		$ex_user_id       = (string) ( $user_info['ex_user_id'] ?? '' );
		$mastery_level    = (string) ( $evaluation['mastery_level'] ?? '' );
		$duration_seconds = (int) ( $session['duration_seconds'] ?? 0 );

		$student_name = (string) ( $user_info['name'] ?? '' );
		if ( ctype_digit( $ex_user_id ) ) {
			$user = get_userdata( (int) $ex_user_id );
			if ( $user ) {
				$student_name = $user->display_name . ' (' . $user->user_email . ')';
			}
		}
		if ( '' === $student_name ) {
			$student_name = '' !== $ex_user_id ? $ex_user_id : (string) ( $user_info['user_id'] ?? '' );
		}

		$questions = [];
		foreach ( (array) ( $quiz['questions'] ?? [] ) as $question ) {
			$questions[] = [
					'question'       => (string) ( $question['question'] ?? '' ),
					'correct_option' => (string) ( $question['correct_option'] ?? '' ),
					'total_votes'    => (int) ( $question['total_votes'] ?? 0 ),
					'correct_votes'  => (int) ( $question['correct_votes'] ?? 0 ),
					'passed'         => ! empty( $question['passed'] ),
			];
		}

		$progress_log = [];
		foreach ( (array) ( $summary['progress_log'] ?? [] ) as $entry ) {
			$progress_log[] = [
					'concept' => (string) ( $entry['concept'] ?? '' ),
					'status'  => (string) ( $entry['status'] ?? '' ),
					'notes'   => (string) ( $entry['notes'] ?? '' ),
			];
		}

		$tools_used = [];
		foreach ( (array) ( $workspace['tools_used'] ?? [] ) as $tool => $count ) {
			$tools_used[] = [
					'name'  => (string) $tool,
					'count' => (int) $count,
			];
		}

		$usage_rows        = [];
		$total_prompt      = 0;
		$total_completion  = 0;
		$total_tokens      = 0;
		$estimated_cost    = 0;
		foreach ( (array) ( $summary['usage'] ?? [] ) as $role => $usage ) {
			$row = [
					'role'              => ucfirst( (string) $role ),
					'prompt_tokens'     => (int) ( $usage['prompt_tokens'] ?? 0 ),
					'completion_tokens' => (int) ( $usage['completion_tokens'] ?? 0 ),
					'total_tokens'      => (int) ( $usage['total_tokens'] ?? 0 ),
					'cost'              => (float) ( $usage['total_tokens_estimated_cost'] ?? 0 ),
			];
			if ( $row['total_tokens'] < 1 && $row['cost'] <= 0 ) {
				continue;
			}
			$usage_rows[]     = $row;
			$total_prompt     += $row['prompt_tokens'];
			$total_completion += $row['completion_tokens'];
			$total_tokens     += $row['total_tokens'];
			$estimated_cost   += $row['cost'];
		}

		$unfinished_subtopics = array_values( (array) ( $continuation['unfinished_subtopics'] ?? [] ) );
		$whiteboard_used      = ! empty( $workspace['whiteboard_used'] );
		$notepad_used         = ! empty( $workspace['notepad_used'] );

		return [
				'student_name'                     => $student_name,
				'ex_user_id'                       => '' !== $ex_user_id ? $ex_user_id : (string) ( $user_info['user_id'] ?? '' ),
				'agent_name'                       => (string) ( $session['agent_display_name'] ?? '' ),
				'topic'                            => (string) ( $session['topic'] ?? '' ),
				'started_at'                       => $this->format_time( $session['started_at'] ?? 0 ),
				'ended_at'                         => $this->format_time( $session['ended_at'] ?? 0 ),
				'duration_minutes'                => (int) round( $duration_seconds / 60 ),
				'turn_count'                       => (int) ( $session['turns_count'] ?? 0 ),
				'end_reason'                       => (string) ( $session['end_reason'] ?? '' ),
				'off_topic_redirects'              => (int) ( $session['off_topic_redirects_count'] ?? 0 ),

				'overall_score'                    => max( 0, min( 100, (int) ( $evaluation['overall_score'] ?? 0 ) ) ),
				'mastery_level'                   => ucfirst( $mastery_level ),
				'mastery_badge_class'             => $this->mastery_badge_class( $mastery_level ),
				'participation_level'              => ucfirst( (string) ( $evaluation['participation_level'] ?? '' ) ),
				'narrative_summary'                => (string) ( $summary['narrative_summary'] ?? '' ),
				'rubric_notes'                     => (string) ( $evaluation['rubric_notes'] ?? '' ),
				'concepts_mastered'                => array_values( (array) ( $evaluation['concepts_mastered'] ?? [] ) ),
				'concepts_practiced'               => array_values( (array) ( $evaluation['concepts_practiced'] ?? [] ) ),
				'struggled_with'                   => array_values( (array) ( $evaluation['struggled_with'] ?? [] ) ),
				'vocabulary'                       => array_values( (array) ( $evaluation['vocabulary_or_key_terms'] ?? [] ) ),

				'has_quiz'                         => (int) ( $quiz['total_quizzes'] ?? 0 ) > 0,
				'quiz_total'                       => (int) ( $quiz['total_quizzes'] ?? 0 ),
				'quiz_correct'                     => (int) ( $quiz['correct_responses'] ?? 0 ),
				'quiz_accuracy'                    => (int) ( $quiz['accuracy_percent'] ?? 0 ),
				'quiz_questions'                   => $questions,

				'has_progress_log'                 => ! empty( $progress_log ),
				'progress_log'                     => $progress_log,
				'has_workspace'                    => ! empty( $tools_used ) || $whiteboard_used || $notepad_used,
				'whiteboard_used'                  => $whiteboard_used,
				'notepad_used'                     => $notepad_used,
				'tools_used'                       => $tools_used,

				'has_continuation'                 => '' !== (string) ( $continuation['next_session_focus'] ?? '' )
						|| '' !== (string) ( $continuation['recommended_warm_up'] ?? '' )
						|| ! empty( $unfinished_subtopics ),
				'next_session_focus'               => (string) ( $continuation['next_session_focus'] ?? '' ),
				'recommended_warm_up'              => (string) ( $continuation['recommended_warm_up'] ?? '' ),
				'unfinished_subtopics'             => $unfinished_subtopics,
				'previous_session_context_prompt'  => (string) ( $continuation['previous_session_context_prompt'] ?? '' ),

				'has_usage'                        => $total_tokens > 0,
				'usage_rows'                       => $usage_rows,
				'total_prompt_tokens'              => $total_prompt,
				'total_completion_tokens'          => $total_completion,
				'total_tokens'                     => $total_tokens,
				'estimated_cost'                   => number_format( $estimated_cost, 4 ),
		];
	}

	public function generate_xlsx_file() {
		$data         = $this->get_summary_data();
		$spreadsheet  = new Spreadsheet();
		$header_style = [
				'font' => [
						'bold'  => true,
						'color' => [ 'rgb' => '1171A3' ],
						'size'  => 12,
				],
		];

		$this->format_overview_xlsx( $spreadsheet, $header_style, $data );
		$this->format_usage_xlsx( $spreadsheet, $header_style, $data );
		$this->format_quiz_xlsx( $spreadsheet, $header_style, $data );
		$this->format_progress_log_xlsx( $spreadsheet, $header_style, $data );

		$writer     = new Xlsx( $spreadsheet );
		$upload_dir = wp_upload_dir();
		$filename   = 'plugnmeet_ai_summary_' . $this->artifact_id . '.xlsx';
		$path       = $upload_dir['path'] . '/' . $filename;
		$writer->save( $path );

		return [ 'path' => $path, 'url' => $upload_dir['url'] . '/' . $filename ];
	}

	private function format_overview_xlsx( $spreadsheet, $header_style, $data ) {
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle( __( 'Overview', 'plugnmeet' ) );
		$sheet->getColumnDimension( 'A' )->setWidth( 30 );
		$sheet->getColumnDimension( 'B' )->setWidth( 60 );

		$rows = [
				__( 'Student', 'plugnmeet' )               => $data['student_name'],
				__( 'User ID', 'plugnmeet' )               => $data['ex_user_id'],
				__( 'Agent', 'plugnmeet' )                 => $data['agent_name'],
				__( 'Topic', 'plugnmeet' )                 => $data['topic'],
				__( 'Started at', 'plugnmeet' )           => $data['started_at'],
				__( 'Ended at', 'plugnmeet' )             => $data['ended_at'],
				__( 'Duration', 'plugnmeet' )              => 0 < $data['duration_minutes'] ? sprintf( __( '%d min', 'plugnmeet' ), $data['duration_minutes'] ) : '',
				__( 'Turns', 'plugnmeet' )                => $data['turn_count'],
				__( 'End reason', 'plugnmeet' )            => $data['end_reason'],
				__( 'Off-topic redirects', 'plugnmeet' )   => $data['off_topic_redirects'],
				__( 'Overall score', 'plugnmeet' )         => $data['overall_score'],
				__( 'Mastery level', 'plugnmeet' )         => $data['mastery_level'],
				__( 'Participation', 'plugnmeet' )          => $data['participation_level'],
				__( 'Concepts mastered', 'plugnmeet' )     => implode( ', ', $data['concepts_mastered'] ),
				__( 'Concepts practiced', 'plugnmeet' )     => implode( ', ', $data['concepts_practiced'] ),
				__( 'Struggled with', 'plugnmeet' )         => implode( ', ', $data['struggled_with'] ),
				__( 'Key terms', 'plugnmeet' )              => implode( ', ', $data['vocabulary'] ),
				__( 'Narrative summary', 'plugnmeet' )     => $data['narrative_summary'],
				__( 'Rubric notes', 'plugnmeet' )          => $data['rubric_notes'],
				__( 'Next session focus', 'plugnmeet' )    => $data['next_session_focus'],
				__( 'Warm up', 'plugnmeet' )               => $data['recommended_warm_up'],
				__( 'Unfinished subtopics', 'plugnmeet' )  => implode( ', ', $data['unfinished_subtopics'] ),
				__( 'Total tokens', 'plugnmeet' )          => $data['total_tokens'],
				__( 'Total cost', 'plugnmeet' )            => $data['estimated_cost'],
		];

		if ( $data['has_quiz'] ) {
			$rows[ __( 'Quizzes', 'plugnmeet' ) ]        = $data['quiz_total'];
			$rows[ __( 'Correct answers', 'plugnmeet' ) ] = $data['quiz_correct'];
			$rows[ __( 'Accuracy', 'plugnmeet' ) ]        = $data['quiz_accuracy'] . '%';
		}

		$row_index = 1;
		foreach ( $rows as $label => $value ) {
			$sheet->getCell( 'A' . $row_index )->setValue( $label );
			$sheet->getStyle( 'A' . $row_index )->applyFromArray( $header_style );
			$sheet->getCell( 'B' . $row_index )->setValue( (string) $value );
			$row_index ++;
		}
	}

	private function format_usage_xlsx( $spreadsheet, $header_style, $data ) {
		if ( empty( $data['usage_rows'] ) ) {
			return;
		}

		$sheet = $spreadsheet->createSheet();
		$sheet->setTitle( __( 'Usage', 'plugnmeet' ) );
		$sheet->getColumnDimension( 'A' )->setWidth( 20 );
		foreach ( array( 'B', 'C', 'D', 'E' ) as $col ) {
			$sheet->getColumnDimension( $col )->setWidth( 18 );
		}

		$headers = array(
				__( 'Role', 'plugnmeet' ),
				__( 'Prompt tokens', 'plugnmeet' ),
				__( 'Completion tokens', 'plugnmeet' ),
				__( 'Total tokens', 'plugnmeet' ),
				__( 'Estimated cost', 'plugnmeet' ),
		);
		foreach ( $headers as $i => $title ) {
			$sheet->getCell( chr( 65 + $i ) . '1' )->setValue( $title );
		}
		$sheet->getStyle( 'A1:E1' )->applyFromArray( $header_style );

		$row_index = 2;
		foreach ( $data['usage_rows'] as $row ) {
			$sheet->getCell( 'A' . $row_index )->setValue( $row['role'] );
			$sheet->getCell( 'B' . $row_index )->setValue( $row['prompt_tokens'] );
			$sheet->getCell( 'C' . $row_index )->setValue( $row['completion_tokens'] );
			$sheet->getCell( 'D' . $row_index )->setValue( $row['total_tokens'] );
			$sheet->getCell( 'E' . $row_index )->setValue( $row['cost'] );
			$row_index ++;
		}

		$sheet->getCell( 'A' . $row_index )->setValue( __( 'Total', 'plugnmeet' ) );
		$sheet->getCell( 'B' . $row_index )->setValue( $data['total_prompt_tokens'] );
		$sheet->getCell( 'C' . $row_index )->setValue( $data['total_completion_tokens'] );
		$sheet->getCell( 'D' . $row_index )->setValue( $data['total_tokens'] );
		$sheet->getCell( 'E' . $row_index )->setValue( (float) $data['estimated_cost'] );
		$sheet->getStyle( 'A' . $row_index . ':E' . $row_index )->getFont()->setBold( true );
	}

	private function format_quiz_xlsx( $spreadsheet, $header_style, $data ) {
		if ( ! $data['has_quiz'] || empty( $data['quiz_questions'] ) ) {
			return;
		}

		$sheet = $spreadsheet->createSheet();
		$sheet->setTitle( __( 'Quiz', 'plugnmeet' ) );
		$sheet->getColumnDimension( 'A' )->setWidth( 50 );
		$sheet->getColumnDimension( 'B' )->setWidth( 30 );
		foreach ( array( 'C', 'D', 'E' ) as $col ) {
			$sheet->getColumnDimension( $col )->setWidth( 15 );
		}

		$headers = array(
				__( 'Question', 'plugnmeet' ),
				__( 'Correct option', 'plugnmeet' ),
				__( 'Votes', 'plugnmeet' ),
				__( 'Correct votes', 'plugnmeet' ),
				__( 'Passed', 'plugnmeet' ),
		);
		foreach ( $headers as $i => $title ) {
			$sheet->getCell( chr( 65 + $i ) . '1' )->setValue( $title );
		}
		$sheet->getStyle( 'A1:E1' )->applyFromArray( $header_style );

		$row_index = 2;
		foreach ( $data['quiz_questions'] as $question ) {
			$sheet->getCell( 'A' . $row_index )->setValue( $question['question'] );
			$sheet->getCell( 'B' . $row_index )->setValue( $question['correct_option'] );
			$sheet->getCell( 'C' . $row_index )->setValue( $question['total_votes'] );
			$sheet->getCell( 'D' . $row_index )->setValue( $question['correct_votes'] );
			$sheet->getCell( 'E' . $row_index )->setValue( $question['passed'] ? __( 'Yes', 'plugnmeet' ) : __( 'No', 'plugnmeet' ) );
			$row_index ++;
		}
	}

	private function format_progress_log_xlsx( $spreadsheet, $header_style, $data ) {
		if ( ! $data['has_progress_log'] ) {
			return;
		}

		$sheet = $spreadsheet->createSheet();
		$sheet->setTitle( __( 'Progress Log', 'plugnmeet' ) );
		$sheet->getColumnDimension( 'A' )->setWidth( 30 );
		$sheet->getColumnDimension( 'B' )->setWidth( 20 );
		$sheet->getColumnDimension( 'C' )->setWidth( 60 );

		$headers = array( __( 'Concept', 'plugnmeet' ), __( 'Status', 'plugnmeet' ), __( 'Notes', 'plugnmeet' ) );
		foreach ( $headers as $i => $title ) {
			$sheet->getCell( chr( 65 + $i ) . '1' )->setValue( $title );
		}
		$sheet->getStyle( 'A1:C1' )->applyFromArray( $header_style );

		$row_index = 2;
		foreach ( $data['progress_log'] as $entry ) {
			$sheet->getCell( 'A' . $row_index )->setValue( $entry['concept'] );
			$sheet->getCell( 'B' . $row_index )->setValue( $entry['status'] );
			$sheet->getCell( 'C' . $row_index )->setValue( $entry['notes'] );
			$row_index ++;
		}
	}

	private function format_time( $milliseconds ) {
		$milliseconds = (int) $milliseconds;
		if ( $milliseconds <= 0 ) {
			return '';
		}

		return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) floor( $milliseconds / 1000 ) );
	}

	private function mastery_badge_class( $level ) {
		switch ( strtolower( trim( (string) $level ) ) ) {
			case 'advanced':
				return 'pnm-badge-success';
			case 'proficient':
				return 'pnm-badge-info';
			case 'developing':
				return 'pnm-badge-warning';
		}

		return 'pnm-badge-neutral';
	}
}
