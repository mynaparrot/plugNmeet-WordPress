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
                    html += '<td>' + artifact.artifact_id + '</td>';
                    html += '<td>' + artifact.type + '</td>';
                    html += '<td>' + artifact.created + '</td>';
                    html += '<td><a href="' + artifact.view_url + '" class="button">' + plugnmeet_artifacts_data.i18n.view + '</a></td>';
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

    $(document).on('click', '.download-artifact', function(e) {
        e.preventDefault();

        const artifact_id = $(this).data('artifact-id');
        const data = {
            nonce: plugnmeet_artifacts_data.nonce.download_artifact,
            action: 'plugnmeet_download_artifact',
            artifact_id: artifact_id,
        };

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data,
            success: (data) => {
                if (data.status) {
                    window.location.href = data.url;
                } else {
                    alert(data.msg);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                alert(errorThrown);
            },
        });
    });

    $(document).on('click', '.download-analytics-excel', function(e) {
        e.preventDefault();

        const artifact_id = $(this).data('artifact-id');
        const data = {
            nonce: plugnmeet_artifacts_data.nonce.download_analytics,
            action: 'plugnmeet_download_analytics',
            artifact_id: artifact_id,
        };

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data,
            success: (data) => {
                if (data.status) {
                    window.location.href = data.url;
                } else {
                    alert(data.msg);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                alert(errorThrown);
            },
        });
    });

    $(document).on('click', '.delete-artifact', function(e) {
        e.preventDefault();

        if (!confirm(plugnmeet_artifacts_data.i18n.confirm_delete)) {
            return;
        }

        const artifact_id = $(this).data('artifact-id');
        const data = {
            nonce: plugnmeet_artifacts_data.nonce.delete_artifact,
            action: 'plugnmeet_delete_artifact',
            artifact_id: artifact_id,
        };

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data,
            success: (data) => {
                if (data.status) {
                    window.location.href = 'admin.php?page=plugnmeet-artifacts';
                } else {
                    alert(data.msg);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                alert(errorThrown);
            },
        });
    });
});
