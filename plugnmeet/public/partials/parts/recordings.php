<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
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
?>

<div class="column-full recordings">
    <h1 class="headline"><?php echo __("Recordings", "plugnmeet"); ?></h1>
    <div class="br">
        <div class="br-inner"></div>
    </div>
    <div class="recording-table">
        <div class="table-inner">
            <div class="table-head">
                <div class="recording-date"><?php echo __("Recording created", "plugnmeet"); ?></div>
                <div class="meeting-date"><?php echo __("Meeting create", "plugnmeet"); ?></div>
                <div class="file-size"><?php echo __("File size (MB)", "plugnmeet"); ?></div>
                <div class="action"></div>
            </div>
            <div id="recordingListsBody"></div>
        </div>
    </div>
    <ul class="pagination" style="display: none">
        <button id="backward"><?php echo __("Pre", "plugnmeet"); ?></button>
        <button id="forward"><?php echo __("Next", "plugnmeet"); ?></button>
    </ul>
</div>

<script>
    const CAN_DELETE = <?php echo isset($role['can_delete']) && $role['can_delete'] === "on" ? 1 : 0 ?>;
    const roomId = '<?php echo $roomInfo->room_id; ?>';
    let isShowingPagination = false,
        totalRecordings = 0,
        currentPage = 1,
        limitPerPage = 10,
        showPre = false,
        showNext = true;

    function downloadRecording(e) {
        e.preventDefault();
        const recordId = e.target.attributes.getNamedItem('data-recording').value;

        const data = {
            nonce: '<?php echo wp_create_nonce('plugnmeet_download_recording') ?>',
            action: "plugnmeet_download_recording",
            roomId,
            recordingId: recordId
        }

        jQuery.ajax({
            url: plugnmeet_frontend.ajaxurl,
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
    }

    function deleteRecording(e) {
        e.preventDefault();

        if (confirm('<?php echo __("Are you sure to delete?", "plugnmeet"); ?>') !== true) {
            return;
        }

        const recordId = e.target.attributes.getNamedItem('data-recording').value;
        const data = {
            nonce: '<?php echo wp_create_nonce('plugnmeet_delete_recording') ?>',
            action: "plugnmeet_delete_recording",
            roomId,
            recordingId: recordId
        }

        jQuery.ajax({
            url: plugnmeet_frontend.ajaxurl,
            method: 'POST',
            data,
            success: function (data) {
                if (data.status) {
                    alert(data.msg);
                    document.getElementById(recordId).remove();
                } else {
                    alert(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus + ": " + errorThrown);
                console.error(textStatus + ": " + errorThrown);
            }
        })
    }

    jQuery('document').ready(function ($) {
        document.addEventListener('click', function (e) {
            if (e.target.id === 'backward') {
                e.preventDefault();
                if (!showPre) {
                    return;
                }
                currentPage--;
                paginate(currentPage);
            } else if (e.target.id === 'forward') {
                e.preventDefault();
                if (!showNext) {
                    return;
                }
                currentPage++;
                paginate(currentPage);
            }
        });

        function fetchRecordings(from = 0, limitPerPage = 10) {
            const data = {
                nonce: '<?php echo wp_create_nonce('plugnmeet_get_recordings') ?>',
                action: "plugnmeet_get_recordings",
                from: from,
                limit: limitPerPage,
                order_by: 'DESC',
                roomId,
            };

            $.ajax({
                url: plugnmeet_frontend.ajaxurl,
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
                    if (
                        data.result.total_recordings > recordings.length &&
                        !isShowingPagination
                    ) {
                        totalRecordings = data.result.total_recordings;
                        showPagination();
                        isShowingPagination = true;
                    }
                    displayRecordings(recordings);
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    alert(errorThrown)
                },
            });
        }

        fetchRecordings();

        function displayRecordings(recordings) {
            let html = '';
            for (let i = 0; i < recordings.length; i++) {
                const recording = recordings[i];
                html += '<div class="table-item" id="' + recording.record_id + '">';
                html +=
                    '<div class="recording-date">' +
                    new Date(recording.creation_time * 1e3).toLocaleString() +
                    '</div>';
                html +=
                    '<div class="meeting-date">' +
                    new Date(recording.room_creation_time * 1e3).toLocaleString() +
                    '</div>';
                html += '<div class="file-size">' + recording.file_size + '</div>';

                html += '<div class="action">';
                html +=
                    '<a href="#" class="download" data-recording="' +
                    recording.record_id +
                    '" onclick="downloadRecording(event)">Download</a>';
                if (CAN_DELETE) {
                    html +=
                        '<a href="#" class="delete" data-recording="' +
                        recording.record_id +
                        '" onclick="deleteRecording(event)">Delete</a>';
                }
                html += '</div>';

                html += '</div>';
            }

            document.getElementById('recordingListsBody').innerHTML = html;
        }

        function showPagination() {
            currentPage = 1;
            document.querySelector('.pagination').style.display = '';
            paginate(currentPage);
        }

        function paginate(currentPage) {
            document.getElementById('recordingListsBody').innerHTML = '';
            const from = (currentPage - 1) * limitPerPage;

            if (currentPage === 1) {
                showPre = false;
                document.getElementById('backward').setAttribute('disabled', 'disabled');
            } else {
                showPre = true;
                document.getElementById('backward').removeAttribute('disabled');
            }

            if (currentPage >= totalRecordings / limitPerPage) {
                showNext = false;
                document.getElementById('forward').setAttribute('disabled', 'disabled');
            } else {
                showNext = true;
                document.getElementById('forward').removeAttribute('disabled');
            }

            fetchRecordings(from, limitPerPage);
        }

        function showMessage(msg) {
            document.getElementById('recordingListsBody').innerHTML = msg;
        }
    });


</script>
