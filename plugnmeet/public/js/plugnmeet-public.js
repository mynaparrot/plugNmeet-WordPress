(function ($) {
    'use strict';
    $(document).on("submit", ".plugnmeet-login-form", (e) => {
        e.preventDefault();
        const formData = $(e.currentTarget).serialize();
        const status = $(e.currentTarget).find(".roomStatus");

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

                    const windowOpen = window.open(data.url, "_blank");
                    if (!windowOpen) {
                        setTimeout(() => {
                            // check, if still not opened
                            if (!windowOpen) {
                                window.location.href = url
                            }
                        }, 5000);
                    }

                    $("#room-password").val("")
                    status.hide();
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
        });
    });
})(jQuery);
