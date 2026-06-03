(function ($) {
    'use strict';

    function showPlugnmeetAdminNotice(message, type) {
        const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
        $('#wpbody-content > .wrap').before(notice);
        setTimeout(function () {
            notice.fadeOut();
        }, 5000);
    }

    $(document).on("click", ".upload_media_button", (e) => {
        e.preventDefault();
        const attachedTo = String($(e.currentTarget).data('attached-to'));

        //Extend the wp.media object
        const custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            const attachment = custom_uploader.state().get('selection').first().toJSON();
            const targetInput = $(`#${attachedTo}`);

            if (targetInput.length) {
                targetInput.val(attachment.url);
            }
        });
        //Open the uploader dialog
        custom_uploader.open();
    });

    $(document).on("click", "#update_client_button", (e) => {
        e.preventDefault();

        const data = {
            action: "plugnmeet_update_client",
            nonce: ajax_admin.nonce
        }

        $.ajax({
            url: ajaxurl,
            data,
            method: 'POST',
            beforeSend: () => {
                $("#update_client_button").addClass("disabled");
            },
            success: function (data) {
                $("#update_client_button").removeClass("disabled");
                let msg = data.msg;
                if (typeof msg === "object") {
                    msg = JSON.parse(msg);
                }
                showPlugnmeetAdminNotice(msg, data.type);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#update_client_button").removeClass("disabled");
                showPlugnmeetAdminNotice(textStatus + ": " + errorThrown, 'error');
                console.log(textStatus + ": " + errorThrown);
            }
        })
    })

    $(document).on("submit", "#plugnmeet-form", (e) => {
        e.preventDefault();
        const formData = $("#plugnmeet-form").serialize();
        $.ajax({
            url: ajaxurl,
            data: formData,
            method: 'POST',
            success: function (data) {
                if (data.status) {
                    if (data.url) {
                        window.location.href = data.url;
                    } else {
                        window.location.href = "admin.php?page=plugnmeet";
                    }
                } else {
                    // The server will set a transient for the error notice
                    window.location.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showPlugnmeetAdminNotice(textStatus + ": " + errorThrown, 'error');
                console.log(textStatus + ": " + errorThrown);
            }
        })
    })

    $(document).on("click", ".pnm-delete-room", (e) => {
        e.preventDefault();

        if (!confirm(ajax_admin.i18n.confirm_delete)) {
            return;
        }

        const id = $(e.currentTarget).data('id');
        const data = {
            id,
            action: "plugnmeet_delete_room",
            nonce: ajax_admin.nonce
        }

        $.ajax({
            url: ajaxurl,
            data: data,
            method: 'POST',
            success: function (data) {
                if (data.status) {
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showPlugnmeetAdminNotice(textStatus + ": " + errorThrown, 'error');
                console.log(textStatus + ": " + errorThrown);
            }
        })
    })

    $(document).ready(function () {
        $('.colorPickerItem').colorpicker();
        if ($("#client_load").val() === "remote") {
            $("#client_download_url").parent().parent().hide();
        }
    })

    $(document).on("change", "#client_load", (e) => {
        if ($(e.target).val() === "local") {
            $("#client_download_url").parent().parent().show();
        } else {
            $("#client_download_url").parent().parent().hide();
        }
    })

})(jQuery);
