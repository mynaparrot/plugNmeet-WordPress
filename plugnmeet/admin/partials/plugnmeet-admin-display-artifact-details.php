<?php
/**
 * Renders the artifact details page.
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin/partials
 */

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
    die;
}

$metadata      = $artifact_info->getMetadata();
$is_file_based = ( $metadata && $metadata->hasFileInfo() );
$roomId        = isset( $_GET['room_id'] ) ? sanitize_text_field( $_GET['room_id'] ) : '';
$paged         = isset( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : '';
?>
<div class="wrap plugnmeet-artifact-details">
    <div class="plugnmeet-details-header">
        <h1 class="wp-heading-inline"><?php echo __( 'Artifact Details', 'plugnmeet' ); ?></h1>
        <div class="plugnmeet-header-actions">
            <a href="<?php echo admin_url( 'admin.php?page=plugnmeet-artifacts&room_id=' . $roomId . '&paged=' . $paged ); ?>"
               class="button plugnmeet-button-secondary">
                <?php echo __( 'Back to Artifacts', 'plugnmeet' ); ?>
            </a>
            <?php if ( $is_file_based ) : ?>
                <a href="#" class="button plugnmeet-button-primary download-artifact"
                   data-artifact-id="<?php echo esc_attr( $artifact_info->getArtifactId() ); ?>"><?php echo __( 'Download', 'plugnmeet' ); ?></a>
            <?php endif; ?>
            <?php if ( $context['is_analytics'] ) : ?>
                <a href="#" class="button plugnmeet-button-success download-analytics-excel"
                   data-artifact-id="<?php echo esc_attr( $artifact_info->getArtifactId() ); ?>"><?php echo __( 'Download Excel', 'plugnmeet' ); ?></a>
            <?php endif; ?>
            <?php if ( $is_file_based ) : ?>
                <a href="#" class="button plugnmeet-button-danger delete-artifact"
                   data-artifact-id="<?php echo esc_attr( $artifact_info->getArtifactId() ); ?>"><?php echo __( 'Delete', 'plugnmeet' ); ?></a>
            <?php endif; ?>
        </div>
    </div>
    <hr/>

    <table class="form-table details">
        <tbody>
        <?php foreach ( $context['details'] as $detail ) : ?>
            <tr>
                <th scope="row"><?php echo esc_html( $detail['label'] ); ?></th>
                <td><?php echo esc_html( $detail['value'] ); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ( $context['is_analytics'] ) : ?>
        <h2 class="room-analytics-header"><?php echo __( 'Room Analytics', 'plugnmeet' ); ?></h2>
        <table class="form-table">
            <tbody>
            <?php foreach ( $context['room_details'] as $detail ) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html( $detail['label'] ); ?></th>
                    <td><?php echo esc_html( $detail['value'] ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $context['has_users'] ) : ?>
            <h2 class="user-analytics-header"><?php echo __( 'User Analytics', 'plugnmeet' ); ?></h2>
            <div class="plugnmeet-analytics-table-wrapper">
                <table class="wp-list-table widefat striped">
                    <thead>
                    <tr>
                        <?php foreach ( $context['user_headers'] as $header ) : ?>
                            <th><?php echo esc_html( $header ); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $context['user_rows'] as $row ) : ?>
                        <tr>
                            <?php foreach ( $row['data'] as $cell ) : ?>
                                <td><?php echo wp_kses( $cell['value'], array( 'br' => array() ) ); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ( $context['is_meeting_summary'] ) : ?>
        <h2 class="meeting-summary-header"><?php echo __( 'Meeting Summary', 'plugnmeet' ); ?></h2>
        <div>
            <?php echo wp_kses_post( $context['meeting_summary_content'] ); ?>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $context['is_ai_session_summary'] ) && ! empty( $context['ai_summary'] ) ) : ?>
        <?php
        $ai      = $context['ai_summary'];
        $ai_rows = array(
                __( 'Agent', 'plugnmeet' )               => $ai['agent_name'],
                __( 'Topic', 'plugnmeet' )               => $ai['topic'],
                __( 'Started at', 'plugnmeet' )          => $ai['started_at'],
                __( 'Ended at', 'plugnmeet' )            => $ai['ended_at'],
                __( 'Duration', 'plugnmeet' )            => 0 < $ai['duration_minutes'] ? sprintf( __( '%d min', 'plugnmeet' ), $ai['duration_minutes'] ) : '',
                __( 'Turns', 'plugnmeet' )               => $ai['turn_count'],
                __( 'End reason', 'plugnmeet' )          => $ai['end_reason'],
                __( 'Off-topic redirects', 'plugnmeet' ) => $ai['off_topic_redirects'],
        );
        $ai_groups = array(
                array( 'label' => __( 'Concepts mastered', 'plugnmeet' ), 'items' => $ai['concepts_mastered'], 'class' => 'pnm-badge-success' ),
                array( 'label' => __( 'Concepts practiced', 'plugnmeet' ), 'items' => $ai['concepts_practiced'], 'class' => 'pnm-badge-info' ),
                array( 'label' => __( 'Struggled with', 'plugnmeet' ), 'items' => $ai['struggled_with'], 'class' => 'pnm-badge-warning' ),
                array( 'label' => __( 'Key terms', 'plugnmeet' ), 'items' => $ai['vocabulary'], 'class' => 'pnm-badge-neutral' ),
        );
        ?>
        <h2 class="ai-session-summary-header"><?php echo __( 'AI Session Report', 'plugnmeet' ); ?></h2>

        <div class="pnm-ai-report">
            <div class="pnm-ai-report-summary">
                <div>
                    <h3 class="pnm-ai-report-name"><?php echo esc_html( $ai['student_name'] ); ?></h3>
                    <?php if ( '' !== $ai['ex_user_id'] ) : ?>
                        <p class="pnm-ai-report-meta"><?php echo esc_html( sprintf( __( 'User ID: %s', 'plugnmeet' ), $ai['ex_user_id'] ) ); ?></p>
                    <?php endif; ?>
                    <?php if ( '' !== $ai['participation_level'] ) : ?>
                        <p class="pnm-ai-report-meta"><?php echo esc_html( sprintf( __( 'Participation: %s', 'plugnmeet' ), $ai['participation_level'] ) ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="pnm-ai-report-score">
                    <span class="pnm-ai-report-score-value"><?php echo (int) $ai['overall_score']; ?></span>
                    <span class="pnm-ai-report-score-label"><?php echo __( 'Overall score', 'plugnmeet' ); ?></span>
                    <?php if ( '' !== $ai['mastery_level'] ) : ?>
                        <span class="pnm-badge <?php echo esc_attr( $ai['mastery_badge_class'] ); ?>"><?php echo esc_html( $ai['mastery_level'] ); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <table class="form-table pnm-ai-report-details">
                <tbody>
                <?php foreach ( $ai_rows as $label => $value ) : ?>
                    <?php if ( '' !== trim( (string) $value ) ) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html( $label ); ?></th>
                            <td><?php echo esc_html( $value ); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ( '' !== $ai['narrative_summary'] ) : ?>
                <h3 class="pnm-ai-report-heading"><?php echo __( 'Narrative summary', 'plugnmeet' ); ?></h3>
                <p class="pnm-ai-report-text"><?php echo nl2br( esc_html( $ai['narrative_summary'] ) ); ?></p>
            <?php endif; ?>

            <?php if ( '' !== $ai['rubric_notes'] ) : ?>
                <h3 class="pnm-ai-report-heading"><?php echo __( 'Rubric notes', 'plugnmeet' ); ?></h3>
                <p class="pnm-ai-report-text"><?php echo nl2br( esc_html( $ai['rubric_notes'] ) ); ?></p>
            <?php endif; ?>

            <?php foreach ( $ai_groups as $ai_group ) : ?>
                <?php if ( ! empty( $ai_group['items'] ) ) : ?>
                    <div class="pnm-ai-report-badges">
                        <span class="pnm-ai-report-badges-label"><?php echo esc_html( $ai_group['label'] ); ?></span>
                        <?php foreach ( $ai_group['items'] as $ai_item ) : ?>
                            <span class="pnm-badge <?php echo esc_attr( $ai_group['class'] ); ?>"><?php echo esc_html( $ai_item ); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ( ! empty( $ai['has_quiz'] ) ) : ?>
                <h3 class="pnm-ai-report-heading"><?php echo __( 'Quiz evaluation', 'plugnmeet' ); ?></h3>
                <p class="pnm-ai-report-text">
                    <?php
                    echo esc_html( sprintf(
                            __( 'Quizzes: %1$d · Correct answers: %2$d · Accuracy: %3$d%%', 'plugnmeet' ),
                            $ai['quiz_total'],
                            $ai['quiz_correct'],
                            $ai['quiz_accuracy']
                    ) );
                    ?>
                </p>
                <?php if ( ! empty( $ai['quiz_questions'] ) ) : ?>
                    <table class="wp-list-table widefat striped pnm-ai-report-table">
                        <thead>
                        <tr>
                            <th><?php echo __( 'Question', 'plugnmeet' ); ?></th>
                            <th><?php echo __( 'Correct option', 'plugnmeet' ); ?></th>
                            <th class="pnm-ai-report-center"><?php echo __( 'Votes', 'plugnmeet' ); ?></th>
                            <th class="pnm-ai-report-center"><?php echo __( 'Correct votes', 'plugnmeet' ); ?></th>
                            <th class="pnm-ai-report-center"><?php echo __( 'Passed', 'plugnmeet' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $ai['quiz_questions'] as $ai_question ) : ?>
                            <tr>
                                <td><?php echo esc_html( $ai_question['question'] ); ?></td>
                                <td><?php echo esc_html( $ai_question['correct_option'] ); ?></td>
                                <td class="pnm-ai-report-center"><?php echo (int) $ai_question['total_votes']; ?></td>
                                <td class="pnm-ai-report-center"><?php echo (int) $ai_question['correct_votes']; ?></td>
                                <td class="pnm-ai-report-center">
                                    <?php if ( $ai_question['passed'] ) : ?>
                                        <span class="pnm-badge pnm-badge-success"><?php echo __( 'Yes', 'plugnmeet' ); ?></span>
                                    <?php else : ?>
                                        <span class="pnm-badge pnm-badge-danger"><?php echo __( 'No', 'plugnmeet' ); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ( ! empty( $ai['has_progress_log'] ) ) : ?>
                <h3 class="pnm-ai-report-heading"><?php echo __( 'Progress log', 'plugnmeet' ); ?></h3>
                <table class="wp-list-table widefat striped pnm-ai-report-table">
                    <thead>
                    <tr>
                        <th><?php echo __( 'Concept', 'plugnmeet' ); ?></th>
                        <th><?php echo __( 'Status', 'plugnmeet' ); ?></th>
                        <th><?php echo __( 'Notes', 'plugnmeet' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $ai['progress_log'] as $ai_entry ) : ?>
                        <tr>
                            <td><?php echo esc_html( $ai_entry['concept'] ); ?></td>
                            <td><?php echo esc_html( $ai_entry['status'] ); ?></td>
                            <td><?php echo esc_html( $ai_entry['notes'] ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ( ! empty( $ai['has_workspace'] ) ) : ?>
                <h3 class="pnm-ai-report-heading"><?php echo __( 'Workspace activity', 'plugnmeet' ); ?></h3>
                <div class="pnm-ai-report-badges">
                    <span class="pnm-ai-report-badges-label"><?php echo __( 'Whiteboard', 'plugnmeet' ); ?></span>
                    <span class="pnm-badge <?php echo $ai['whiteboard_used'] ? 'pnm-badge-success' : 'pnm-badge-neutral'; ?>">
                        <?php echo $ai['whiteboard_used'] ? __( 'Used', 'plugnmeet' ) : __( 'Not used', 'plugnmeet' ); ?>
                    </span>
                    <span class="pnm-ai-report-badges-label"><?php echo __( 'Notepad', 'plugnmeet' ); ?></span>
                    <span class="pnm-badge <?php echo $ai['notepad_used'] ? 'pnm-badge-success' : 'pnm-badge-neutral'; ?>">
                        <?php echo $ai['notepad_used'] ? __( 'Used', 'plugnmeet' ) : __( 'Not used', 'plugnmeet' ); ?>
                    </span>
                    <?php foreach ( $ai['tools_used'] as $ai_tool ) : ?>
                        <span class="pnm-badge pnm-badge-info">
                            <?php echo esc_html( sprintf( '%1$s (%2$d)', $ai_tool['name'], $ai_tool['count'] ) ); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $ai['has_continuation'] ) ) : ?>
                <h3 class="pnm-ai-report-heading"><?php echo __( 'Next session plan', 'plugnmeet' ); ?></h3>
                <?php if ( '' !== $ai['next_session_focus'] ) : ?>
                    <p class="pnm-ai-report-text"><strong><?php echo __( 'Focus', 'plugnmeet' ); ?>:</strong> <?php echo esc_html( $ai['next_session_focus'] ); ?></p>
                <?php endif; ?>
                <?php if ( '' !== $ai['recommended_warm_up'] ) : ?>
                    <p class="pnm-ai-report-text"><strong><?php echo __( 'Warm up', 'plugnmeet' ); ?>:</strong> <?php echo esc_html( $ai['recommended_warm_up'] ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $ai['unfinished_subtopics'] ) ) : ?>
                    <div class="pnm-ai-report-badges">
                        <span class="pnm-ai-report-badges-label"><?php echo __( 'Unfinished subtopics', 'plugnmeet' ); ?></span>
                        <?php foreach ( $ai['unfinished_subtopics'] as $ai_subtopic ) : ?>
                            <span class="pnm-badge pnm-badge-warning"><?php echo esc_html( $ai_subtopic ); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ( '' !== $ai['previous_session_context_prompt'] ) : ?>
                <details class="pnm-ai-report-context">
                    <summary><?php echo __( 'Previous session context', 'plugnmeet' ); ?></summary>
                    <p class="pnm-ai-report-text"><?php echo nl2br( esc_html( $ai['previous_session_context_prompt'] ) ); ?></p>
                </details>
            <?php endif; ?>

            <?php if ( ! empty( $ai['has_usage'] ) ) : ?>
                <p class="pnm-ai-report-text">
                    <?php
                    echo esc_html( sprintf(
                            __( 'Total tokens: %1$s · Estimated cost: %2$s', 'plugnmeet' ),
                            number_format_i18n( $ai['total_tokens'] ),
                            $ai['estimated_cost']
                    ) );
                    ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
