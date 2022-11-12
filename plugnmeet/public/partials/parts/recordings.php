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

if ( ! defined( 'PLUGNMEET_BASE_NAME' ) ) {
	die;
}
?>

<div class="column-full recordings">
    <h1 class="headline"><?php echo __( "Recordings", "plugnmeet" ); ?></h1>
    <div class="br">
        <div class="br-inner"></div>
    </div>
    <div class="recording-table">
        <div class="table-inner">
            <div class="table-head">
                <div class="recording-date"><?php echo __( "Recording date", "plugnmeet" ); ?></div>
                <div class="meeting-date"><?php echo __( "Meeting date", "plugnmeet" ); ?></div>
                <div class="file-size"><?php echo __( "File size (MB)", "plugnmeet" ); ?></div>
                <div class="action"></div>
            </div>
            <div id="recordingListsBody"></div>
        </div>
    </div>
    <ul class="pagination" style="display: none">
        <button id="backward"><?php echo __( "Pre", "plugnmeet" ); ?></button>
        <button id="forward"><?php echo __( "Next", "plugnmeet" ); ?></button>
    </ul>
    <div id="playbackModal" style="display:none">
        <video id="modalPlayer" width="100%" height="400" controls controlsList="nodownload" src=""
               oncontextmenu="return false"></video>
    </div>
</div>

<script>
    const CAN_PLAY = <?php echo isset( $role['can_play'] ) && $role['can_play'] === "on" ? 1 : 0 ?>;
    const CAN_DOWNLOAD = <?php echo isset( $role['can_download'] ) && $role['can_download'] === "on" ? 1 : 0 ?>;
    const CAN_DELETE = <?php echo isset( $role['can_delete'] ) && $role['can_delete'] === "on" ? 1 : 0 ?>;
    const roomId = '<?php echo $roomInfo->room_id; ?>';
    let isShowingPagination = false,
        totalRecordings = 0,
        currentPage = 1,
        limitPerPage = 10,
        showPre = false,
        showNext = true;

    window.addEventListener('load', () => {
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

        jQuery('body').on('thickbox:removed', function () {
            document.getElementById("modalPlayer").src = "";
        });

        fetchRecordings();
    });

    const fetchRecordings = async (from = 0, limitPerPage = 10) => {
        const formData = new FormData();
        formData.append('nonce', '<?php echo wp_create_nonce( 'plugnmeet_get_recordings' ) ?>');
        formData.append('action', 'plugnmeet_get_recordings');
        formData.append('from', from);
        formData.append('limit', limitPerPage);
        formData.append('roomId', roomId);
        formData.append('order_by', 'DESC');

        const data = await sendRequest(formData);
        if (!data) {
            return;
        }

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
        if (
            totalRecordings > limitPerPage &&
            !isShowingPagination
        ) {
            showPagination();
            isShowingPagination = true;
        }
        displayRecordings(recordings);
    }

    const downloadRecording = async (e) => {
        e.preventDefault();
        const recordId = e.target.attributes.getNamedItem('data-recording').value;

        const formData = new FormData();
        formData.append('nonce', '<?php echo wp_create_nonce( 'plugnmeet_download_recording' ) ?>');
        formData.append('action', 'plugnmeet_download_recording');
        formData.append('roomId', roomId);
        formData.append('recordingId', recordId);

        const res = await sendRequest(formData);
        if (!res) {
            return;
        }

        if (res.status) {
            window.open(res.url, "_blank");
        } else {
            alert(res.msg);
        }
    }

    const playRecording = async (e, i) => {
        e.preventDefault();
        const recordId = e.target.attributes.getNamedItem('data-recording').value;
        const title = document.getElementById("r_creation_" + i).innerHTML;

        const formData = new FormData();
        formData.append('nonce', '<?php echo wp_create_nonce( 'plugnmeet_download_recording' ) ?>');
        formData.append('action', 'plugnmeet_download_recording');
        formData.append('roomId', roomId);
        formData.append('recordingId', recordId);
        formData.append('role', 'can_play');

        const res = await sendRequest(formData);
        if (!res) {
            return;
        }

        if (res.status) {
            const modalPlayer = document.getElementById("modalPlayer");
            modalPlayer.src = res.url;
            tb_show(title, '#TB_inline?height=450&amp;inlineId=playbackModal');
            setTimeout(() => {
                modalPlayer.setAttribute('style', '');
            }, 200);
        } else {
            alert(res.msg);
        }
    }

    const deleteRecording = async (e) => {
        e.preventDefault();

        if (confirm('<?php echo __( "Are you sure to delete?", "plugnmeet" ); ?>') !== true) {
            return;
        }

        const recordId = e.target.attributes.getNamedItem('data-recording').value;
        const formData = new FormData();
        formData.append('nonce', '<?php echo wp_create_nonce( 'plugnmeet_delete_recording' ) ?>');
        formData.append('action', 'plugnmeet_delete_recording');
        formData.append('roomId', roomId);
        formData.append('recordingId', recordId);

        const res = await sendRequest(formData);
        if (!res) {
            return;
        }

        if (res.status) {
            alert(res.msg);
            document.getElementById(recordId).remove();
        } else {
            alert(res.msg);
        }
    }

    const displayRecordings = (recordings) => {
        let html = '';
        for (let i = 0; i < recordings.length; i++) {
            const recording = recordings[i];
            html += '<div class="table-item" id="' + recording.record_id + '">';
            html +=
                '<div class="recording-date" id="r_creation_' + i + '">' +
                new Date(recording.creation_time * 1e3).toLocaleString() +
                '</div>';
            html +=
                '<div class="meeting-date">' +
                new Date(recording.room_creation_time * 1e3).toLocaleString() +
                '</div>';
            html += '<div class="file-size">' + recording.file_size + '</div>';

            html += '<div class="action">';
            if (CAN_PLAY) {
                html +=
                    '<a href="#" class="download" data-recording="' +
                    recording.record_id +
                    '" onclick="playRecording(event, ' + i + ')"><?php echo __( "Play", "plugnmeet" ); ?></a>';
            }

            if (CAN_DOWNLOAD) {
                html +=
                    '<a href="#" class="download" data-recording="' +
                    recording.record_id +
                    '" onclick="downloadRecording(event)"><?php echo __( "Download", "plugnmeet" ); ?></a>';
            }

            if (CAN_DELETE) {
                html +=
                    '<a href="#" class="delete" data-recording="' +
                    recording.record_id +
                    '" onclick="deleteRecording(event)"><?php echo __( "Delete", "plugnmeet" ); ?></a>';
            }
            html += '</div>';

            html += '</div>';
        }

        document.getElementById('recordingListsBody').innerHTML = html;
    }

    const showPagination = () => {
        currentPage = 1;
        document.querySelector('.pagination').style.display = '';
        paginate(currentPage);
    }

    const paginate = (currentPage) => {
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

    const showMessage = (msg) => {
        document.getElementById('recordingListsBody').innerHTML = msg;
    }

    const sendRequest = async (formData) => {
        const res = await fetch(plugnmeet_frontend.ajaxurl, {
            method: 'POST',
            body: formData
        })

        if (!res.ok) {
            console.error(res.status, res.statusText);
            alert(res.statusText);
            return null;
        }

        try {
            return await res.json();
        } catch (e) {
            console.error(e);
            alert(e);
        }

        return null;
    }
</script>
