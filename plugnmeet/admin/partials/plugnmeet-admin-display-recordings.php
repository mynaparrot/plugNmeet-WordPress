<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.mynaparrot.com
 * @since      1.0.0
 *
 * @package    Plugnmeet
 * @subpackage Plugnmeet/admin/partials
 */

if (!defined('PLUGNMEET_BASE_NAME')) {
    die;
}
?>

<div class="wrap plugnmeet-recordings">
    <div class="room-selector">
        <select name="roomId" id="plugnmeet-selected-roomId">
            <option value=""><?php echo __("Select room", "plugnmeet") ?></option>
            <?php foreach ($rooms as $room): ?>
                <option value="<?php echo esc_attr($room->room_id); ?>"><?php echo esc_html($room->room_title); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="button button-primary"
                id="plugnmeet-show-recordings"><?php echo __("Show recordings", "plugnmeet") ?></button>
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list posts" id="recordingLists"
           style="margin-top: 50px">
        <thead>
        <tr>
            <th class="manage-column column-primary">
                <?php echo __("Record Id", "plugnmeet"); ?>
            </th>
            <th class="manage-column column-categories">
                <?php echo __("Recording date", "plugnmeet"); ?>
            </th>
            <th class="manage-column column-categories">
                <?php echo __("Meeting date", "plugnmeet"); ?>
            </th>
            <th class="manage-column" style="width: 22%">
                <?php echo __("File size (MB)", "plugnmeet"); ?>
            </th>
        </tr>
        </thead>
        <tbody id="recordingListsBody"></tbody>
    </table>
    <div id="recordingListsFooter" class="alignright actions" style="display: none"></div>
</div>

<script type="text/javascript">
    let isShowingPagination = false;
    let roomId = '', totalRecordings = 0, currentPage = 1, limitPerPage = 20;

    jQuery('document').ready(function ($) {
        $(document).on('click', "#plugnmeet-show-recordings", function (e) {
            e.preventDefault();

            roomId = $('#plugnmeet-selected-roomId').val();
            if (!roomId) {
                return;
            }
            const data = {
                nonce: '<?php echo wp_create_nonce('plugnmeet_get_recordings') ?>',
                action: "plugnmeet_get_recordings",
                from: 0,
                limit: limitPerPage,
                order_by: 'DESC',
                roomId,
            };

            fetchRecordings(data);
            isShowingPagination = false;
            $('#recordingListsFooter').hide();
        });

        $(document).on('click', '.downloadRecording', function (e) {
            e.preventDefault();
            const recordingId = $(this).attr('id');
            if (!recordingId) {
                return;
            }
            const data = {
                nonce: '<?php echo wp_create_nonce('plugnmeet_download_recording') ?>',
                action: "plugnmeet_download_recording",
                recordingId,
                roomId
            }

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data,
                success: function (data) {
                    if (data.status) {
                        window.open(data.url, "_blank");
                    } else {
                        alert(data.msg);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus + ": " + errorThrown);
                    console.error(textStatus + ": " + errorThrown);
                }
            })
        });

        $(document).on('click', '.deleteRecording', function (e) {
            e.preventDefault();
            const recordingId = $(this).attr('id');
            if (!recordingId) {
                return;
            }
            if (!confirm("<?php echo __("Are you sure to delete?", "plugnmeet") ?>")) {
                return;
            }
            const data = {
                nonce: '<?php echo wp_create_nonce('plugnmeet_delete_recording') ?>',
                action: "plugnmeet_delete_recording",
                recordingId,
                roomId
            }

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data,
                success: function (data) {
                    if (data.status) {
                        alert(data.msg);
                        document.getElementById(recordingId).parentElement.parentElement.parentElement.remove();
                    } else {
                        alert(data.msg);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus + ": " + errorThrown);
                    console.error(textStatus + ": " + errorThrown);
                }
            })
        });

        function fetchRecordings(data) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data,
                beforeSend: () => {
                    $('#recordingListsBody').html('');
                },
                success: (data) => {
                    if (!data.status) {
                        showMessage(data.msg);
                        return;
                    }
                    const recordings = data.result.recordings_list;
                    if (!recordings) {
                        showMessage('no recordings found');
                        return;
                    }
                    totalRecordings = data.result.total_recordings;
                    // check if pagination require
                    if (
                        totalRecordings > limitPerPage &&
                        !isShowingPagination
                    ) {
                        showPagination();
                        isShowingPagination = true;
                    }

                    let html = '';
                    for (let i = 0; i < recordings.length; i++) {
                        const recording = recordings[i];
                        html += '<tr>';
                        html += '<td>' + recording.record_id + '</td>';
                        html +=
                            '<td>' +
                            new Date(recording.creation_time * 1e3).toLocaleString() +
                            '</td>';
                        html +=
                            '<td>' +
                            new Date(recording.room_creation_time * 1e3).toLocaleString() +
                            '</td>';

                        html += '<td class="center filesize">' + recording.file_size;
                        html += '<div class="alignright actions"><button class="button button-primary downloadRecording" id="' +
                            recording.record_id +
                            '"><?php echo __("Download", "plugnmeet"); ?></button>';
                        html += '<button class="button button-secondary deleteRecording" id="' +
                            recording.record_id +
                            '"><?php echo __("Delete", "plugnmeet"); ?></button></div>';
                        html += '</td>';

                        html += '</tr>';
                    }

                    $('#recordingListsBody').html(html);
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    alert(errorThrown)
                },
            });
        }

        function showMessage(msg) {
            const data =
                '<tr>' +
                '<td ' +
                'colspan="4" ' +
                'class="center">' +
                msg +
                '</td>' +
                '</tr>';
            $('#recordingListsBody').html(data);
        }

        function showPagination() {
            currentPage = 1;

            $('#recordingListsFooter').show();

            let html = '<div class="tablenav-pages">';
            html += '<span class="pagination-links" id="backward" style="margin-right: 10px;">';
            html += '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span></span>';

            html += '<span class="pagination-links" id="forward">';
            html += '<span class="tablenav-pages-navspan button" aria-hidden="true">›</span></span>';

            html += '</div>';


            $('#recordingListsFooter').html(html);
        }

        let showPre = false,
            showNext = true;

        $(document).on('click', '#backward', function (e) {
            e.preventDefault();
            if (!showPre) {
                return;
            }
            currentPage--;
            paginate(currentPage);
        });

        $(document).on('click', '#forward', function (e) {
            e.preventDefault();
            if (!showNext) {
                return;
            }
            currentPage++;
            paginate(currentPage);
        });

        function paginate(currentPage) {
            const from = (currentPage - 1) * limitPerPage;

            if (currentPage === 1) {
                showPre = false;
                $('#backward span').addClass('disabled');
            } else {
                showPre = true;
                $('#backward span').removeClass('disabled');
            }

            if (currentPage >= totalRecordings / limitPerPage) {
                showNext = false;
                $('#forward span').addClass('disabled');
            } else {
                showNext = true;
                $('#forward span').removeClass('disabled');
            }

            const data = {
                nonce: '<?php echo wp_create_nonce('plugnmeet_get_recordings') ?>',
                action: "plugnmeet_get_recordings",
                from,
                limit: limitPerPage,
                order_by: 'DESC',
                roomId,
            };
            fetchRecordings(data);
        }
    });

</script>
