jQuery(document).ready(function ($) {
    let isShowingPagination = false;
    let roomId = '', totalRecordings = 0, currentPage = 1, limitPerPage = 20, selectedRecordings = [];

    if ($('#plugnmeet-selected-roomId').val()) {
        initLoadRecordings();
    }

    $(document).on('click', "#plugnmeet-show-recordings", function (e) {
        e.preventDefault();
        initLoadRecordings();
    });

    $(document).on('click', '.downloadRecording', function (e) {
        e.preventDefault();
        const recordingId = $(this).attr('id');
        if (!recordingId) {
            return;
        }
        const data = {
            nonce: plugnmeet_recordings_data.nonce.download_recording,
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
        if (!confirm(plugnmeet_recordings_data.i18n.confirm_delete)) {
            return;
        }
        const data = {
            nonce: plugnmeet_recordings_data.nonce.delete_recording,
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

    $(document).on('change', '.recording-checkbox', function () {
        const recordId = $(this).val();
        if ($(this).is(':checked')) {
            if (!selectedRecordings.includes(recordId)) {
                selectedRecordings.push(recordId);
            }
        } else {
            selectedRecordings = selectedRecordings.filter(id => id !== recordId);
        }

        if (selectedRecordings.length > 1) {
            $('#plugnmeet-merge-recordings').show();
        } else {
            $('#plugnmeet-merge-recordings').hide();
        }
    });

    function initLoadRecordings() {
        roomId = $('#plugnmeet-selected-roomId').val();
        if (!roomId) {
            return;
        }
        const data = {
            nonce: plugnmeet_recordings_data.nonce.get_recordings,
            action: "plugnmeet_get_recordings",
            from: 0,
            limit: limitPerPage,
            order_by: 'DESC',
            roomId,
        };

        fetchRecordings(data);
        isShowingPagination = false;
        $('#recordingListsFooter').hide();
        selectedRecordings = [];
        $('#plugnmeet-merge-recordings').hide();
    }

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

                const result = JSON.parse(data.result);
                const recordings = result.recordingsList;
                if (!recordings) {
                    showMessage('no recordings found');
                    return;
                }
                totalRecordings = result.totalRecordings;
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
                    html += '<th scope="row" class="check-column"><input type="checkbox" class="recording-checkbox" name="recordings[]" value="' + recording.recordId + '"></th>';
                    html += '<td>' + recording.recordId + '</td>';
                    html +=
                        '<td>' +
                        new Date(recording.creationTime * 1e3).toLocaleString() +
                        '</td>';
                    html +=
                        '<td>' +
                        new Date(recording.roomCreationTime * 1e3).toLocaleString() +
                        '</td>';

                    html += '<td class="center filesize">' + parseFloat(recording.fileSize).toFixed(2);
                    html += '<div class="alignright actions"><button class="button button-primary downloadRecording" id="' +
                        recording.recordId +
                        '">' + plugnmeet_recordings_data.i18n.download + '</button>';
                    html += '<button class="button button-secondary deleteRecording" id="' +
                        recording.recordId +
                        '">' + plugnmeet_recordings_data.i18n.delete + '</button></div>';
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
            'colspan="5" ' +
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
            nonce: plugnmeet_recordings_data.nonce.get_recordings,
            action: "plugnmeet_get_recordings",
            from,
            limit: limitPerPage,
            order_by: 'DESC',
            roomId,
        };
        fetchRecordings(data);
    }

    $(document).on('click', '#plugnmeet-merge-recordings', function () {
        let listHtml = '';
        selectedRecordings.forEach(recordId => {
            listHtml += '<li>' + recordId + '</li>';
        });
        $('#plugnmeet-modal-message').hide();
        $('#plugnmeet-merge-list').html(listHtml);
        $('#plugnmeet-merge-confirm-modal').show();
    });

    $(document).on('click', '#plugnmeet-cancel-merge, #plugnmeet-cancel-merge-top', function () {
        $('#plugnmeet-merge-confirm-modal').hide();
        $('#plugnmeet-confirm-merge').prop('disabled', false);
        $('#plugnmeet-cancel-merge').prop('disabled', false);
        $('#plugnmeet-cancel-merge-top').prop('disabled', false);
    });

    $(document).on('click', '#plugnmeet-confirm-merge', function () {
        $(this).prop('disabled', true);

        const data = {
            nonce: plugnmeet_recordings_data.nonce.merge_recordings,
            action: "plugnmeet_merge_recordings",
            recordings: selectedRecordings,
            roomId
        };

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data,
            success: function (data) {
                const messageDiv = $('#plugnmeet-modal-message');
                messageDiv.text(data.msg).show();
                if (data.status) {
                    messageDiv.addClass('notice notice-success').removeClass('notice-error');
                } else {
                    messageDiv.addClass('notice notice-error').removeClass('notice-success');
                    $('#plugnmeet-confirm-merge').prop('disabled', false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                const messageDiv = $('#plugnmeet-modal-message');
                messageDiv.text(textStatus + ": " + errorThrown).addClass('notice notice-error').removeClass('notice-success').show();
                $('#plugnmeet-confirm-merge').prop('disabled', false);
            }
        });
    });

    $(document).on('change', '#cb-select-all-1', function () {
        $('.recording-checkbox').prop('checked', $(this).prop('checked')).trigger('change');
    });
});
