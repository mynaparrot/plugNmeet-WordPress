jQuery(document).ready(function ($) {
    let isShowingPagination = false;
    let roomId = '', totalArtifacts = 0, currentPage = 1, limitPerPage = 20;

    if ($('#plugnmeet-selected-roomId').val()) {
        const urlParams = new URLSearchParams(window.location.search);
        const paged = parseInt(urlParams.get('paged'));
        if (paged > 1) {
            currentPage = paged;
        }
        initLoadArtifacts();
    }

    $(document).on('click', "#plugnmeet-show-artifacts", function (e) {
        e.preventDefault();
        currentPage = 1;
        initLoadArtifacts();
    });

    function initLoadArtifacts() {
        roomId = $('#plugnmeet-selected-roomId').val();
        if (!roomId) {
            return;
        }
        const from = (currentPage - 1) * limitPerPage;
        const data = {
            nonce: plugnmeet_artifacts_data.nonce.get_artifacts,
            action: "plugnmeet_get_artifacts",
            from: from,
            limit: limitPerPage,
            order_by: 'DESC',
            roomId,
        };

        fetchArtifacts(data);
    }

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
                    $('#plugnmeet-artifacts-info').hide();
                    return;
                }

                const result = JSON.parse(data.result);
                const artifacts = result.artifactsList;
                totalArtifacts = result.totalArtifacts;

                if (!artifacts || artifacts.length === 0) {
                    showMessage('no artifacts found');
                    $('#plugnmeet-artifacts-info').hide();
                    return;
                }

                if (totalArtifacts > limitPerPage) {
                    if (!isShowingPagination) {
                        showPagination();
                        isShowingPagination = true;
                    }
                    updatePaginationButtons();
                } else {
                    $('#artifactListsFooter').hide();
                }

                updateArtifactsInfo();

                let html = '';
                for (let i = 0; i < artifacts.length; i++) {
                    const artifact = artifacts[i];
                    html += '<tr>';
                    html += '<td>' + artifact.artifact_id + '</td>';
                    html += '<td>' + artifact.type + '</td>';
                    html += '<td>' + artifact.created + '</td>';
                    html += '<td>';
                    html += '<div class="alignright actions">';
                    html += '<a href="' + artifact.view_url + '" class="button">' + plugnmeet_artifacts_data.i18n.view + '</a>';
                    html += '</div>';
                    html += '</td>';
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
        $('#artifactListsFooter').show();

        let html = '<div class="tablenav-pages">';
        html += '<span class="pagination-links" id="backward" style="margin-right: 10px;">';
        html += '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span></span>';

        html += '<span class="pagination-links" id="forward">';
        html += '<span class="tablenav-pages-navspan button" aria-hidden="true">›</span></span>';

        html += '</div>';

        $('#artifactListsFooter').html(html);
    }

    function updateArtifactsInfo() {
        const totalPages = Math.ceil(totalArtifacts / limitPerPage);
        let infoText = plugnmeet_artifacts_data.i18n.total_artifacts + ': ' + totalArtifacts;
        if (totalPages > 1) {
            infoText += ' | ' + plugnmeet_artifacts_data.i18n.page + ': ' + currentPage + '/' + totalPages;
        }
        $('#plugnmeet-artifacts-info').html(infoText).show();
    }

    function updatePaginationButtons() {
        if (currentPage === 1) {
            $('#backward span').addClass('disabled');
        } else {
            $('#backward span').removeClass('disabled');
        }

        if (currentPage >= totalArtifacts / limitPerPage) {
            $('#forward span').addClass('disabled');
        } else {
            $('#forward span').removeClass('disabled');
        }
    }

    $(document).on('click', '#backward', function (e) {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            paginate(currentPage);
        }
    });

    $(document).on('click', '#forward', function (e) {
        e.preventDefault();
        if (currentPage < totalArtifacts / limitPerPage) {
            currentPage++;
            paginate(currentPage);
        }
    });

    function paginate(page) {
        currentPage = page;
        const from = (currentPage - 1) * limitPerPage;
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

    $(document).on('click', '.download-artifact', function (e) {
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

    $(document).on('click', '.download-analytics-excel', function (e) {
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

    $(document).on('click', '.delete-artifact', function (e) {
        e.preventDefault();

        if (!confirm(plugnmeet_artifacts_data.i18n.confirm_delete)) {
            return;
        }

        let params = new URLSearchParams(document.location.search);
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
                    window.location.href = 'admin.php?page=plugnmeet-artifacts&room_id=' + params.get('room_id');
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
