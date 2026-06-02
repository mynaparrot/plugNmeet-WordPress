jQuery(document).ready(function ($) {
    let isShowingPagination = false;
    let roomId = '', totalArtifacts = 0, currentPage = 1, limitPerPage = 20;

    $(document).on('click', "#plugnmeet-show-artifacts", function (e) {
        e.preventDefault();

        roomId = $('#plugnmeet-selected-roomId').val();
        if (!roomId) {
            return;
        }
        const data = {
            nonce: plugnmeet_artifacts_data.nonce.get_artifacts,
            action: "plugnmeet_get_artifacts",
            from: 0,
            limit: limitPerPage,
            order_by: 'DESC',
            roomId,
        };

        fetchArtifacts(data);
        isShowingPagination = false;
        $('#artifactListsFooter').hide();
    });

    function fetchArtifacts(data) {
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data,
            beforeSend: () => {
                $('#artifactListsBody').html('');
            },
            success: (data) => {
                if (!data.status) {
                    showMessage(data.msg);
                    return;
                }

                const result = JSON.parse(data.result);
                const artifacts = result.artifactsList;
                if (!artifacts) {
                    showMessage('no artifacts found');
                    return;
                }
                totalArtifacts = result.totalArtifacts;
                // check if pagination require
                if (
                    totalArtifacts > limitPerPage &&
                    !isShowingPagination
                ) {
                    showPagination();
                    isShowingPagination = true;
                }

                let html = '';
                for (let i = 0; i < artifacts.length; i++) {
                    const artifact = artifacts[i];
                    html += '<tr>';
                    html += '<td>' + artifact.artifactId + '</td>';
                    html += '<td>' + artifact.metadata.fileInfo.filePath + '</td>';
                    html += '<td>' + parseFloat(artifact.metadata.fileInfo.fileSize).toFixed(2) + '</td>';
                    html +=
                        '<td>' +
                        new Date(artifact.created * 1e3).toLocaleString() +
                        '</td>';
                    html += '</tr>';
                }

                $('#artifactListsBody').html(html);
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
        $('#artifactListsBody').html(data);
    }

    function showPagination() {
        currentPage = 1;

        $('#artifactListsFooter').show();

        let html = '<div class="tablenav-pages">';
        html += '<span class="pagination-links" id="backward" style="margin-right: 10px;">';
        html += '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span></span>';

        html += '<span class="pagination-links" id="forward">';
        html += '<span class="tablenav-pages-navspan button" aria-hidden="true">›</span></span>';

        html += '</div>';


        $('#artifactListsFooter').html(html);
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

        if (currentPage >= totalArtifacts / limitPerPage) {
            showNext = false;
            $('#forward span').addClass('disabled');
        } else {
            showNext = true;
            $('#forward span').removeClass('disabled');
        }

        const data = {
            nonce: plugnmeet_artifacts_data.nonce.get_artifacts,
            action: "plugnmeet_get_artifacts",
            from,
            limit: limitPerPage,
            order_by: 'DESC',
            roomId,
        };
        fetchArtifacts(data);
    }
});
