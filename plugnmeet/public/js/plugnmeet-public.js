(function ($) {
    'use strict';
    $(document).on("submit", "#plugnmeet-login-form", (e) => {
        e.preventDefault();
        const formData = $("#plugnmeet-login-form").serialize();
        const status = $("#roomStatus");

        $.ajax({
            url: plugnmeet_frontend.ajaxurl,
            data: formData,
            method: 'POST',
            beforeSend: () => {
                status.show();
                status.removeClass("alert-success");
                status.removeClass("alert-danger");

                status.addClass("alert-primary");
                status.html("Checking...");
            },
            success: function (data) {
                status.removeClass("alert-primary");
                if (data.status) {
                    status.addClass("alert-success");
                    status.html("Redirecting...");
                    window.open(data.url, "_blank");
                    $("#room-password").val("")
                } else {
                    status.addClass("alert-danger");
                    status.html(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                status.removeClass("alert-primary");
                status.addClass("alert-danger");
                status.html(textStatus);
            }
        })
    });

})(jQuery);
