(function ($) {
    'use strict';

    $(document).on("click", ".upload_media_button", (e) => {
        e.preventDefault();
        const attachedTo = $(e.currentTarget).attr('data-attached-to');

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
            $(`#${attachedTo}`).val(attachment.url);
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
                alert(msg);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#update_client_button").removeClass("disabled");
                alert(textStatus + ": " + errorThrown);
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
                    alert(data.msg);
                    window.location.href = "admin.php?page=plugnmeet";
                } else {
                    alert(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus + ": " + errorThrown);
                console.log(textStatus + ": " + errorThrown);
            }
        })
    })

    $(document).on("click", ".deleteRoom", (e) => {
        e.preventDefault();

        if (!confirm("Are you sure to delete?")) {
            return;
        }

        const id = e.target.id;
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
                    alert(data.msg);
                    window.location.href = "admin.php?page=plugnmeet";
                } else {
                    alert(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus + ": " + errorThrown);
                console.log(textStatus + ": " + errorThrown);
            }
        })
    })

    $(document).on("submit", "#search-form", (e) => {
        e.preventDefault();
        const search_term = $("#search_term").val();
        const paged = $("#page_num").val();
        let url = "admin.php?page=plugnmeet";
        if (search_term) {
            url += "&search_term=" + search_term;
            if (paged > 1) {
                url += "&paged=" + paged;
            }
            window.location.href = url;
        } else {
            window.location.href = url;
        }
    })

    $(document).ready(function () {
        $('.colorPickerItem').colorpicker();
        if ($("#client_load").val() === "remote") {
            $("#client_download_url").parent().parent().hide();
        }
    })

    $(document).on("change", "#client_load", (e) => {
        if ($(e.target).val() === "remote") {
            $("#client_download_url").parent().parent().hide();
        } else {
            $("#client_download_url").parent().parent().show();
        }
    })

})(jQuery);
