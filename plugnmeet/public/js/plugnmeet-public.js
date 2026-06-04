class PlugNMeetPublicLogin {
    constructor() {
        document.addEventListener('submit', (e) => {
            if (e.target.matches('.plugnmeet-login-form')) {
                this.handleLogin(e);
            }
        });
        // check if returned from conference
        const searchParams = new URLSearchParams(document.location.search);
        if (searchParams.has("pnm-returned", "true")) {
            // this will only work if link opened with window.open()
            window.close();
        }
    }

    displayStatusMessage(statusEl, message, type) {
        statusEl.style.display = 'block';
        statusEl.classList.remove('notice-info', 'notice-success', 'notice-danger');
        statusEl.classList.add(`notice-${type}`);
        statusEl.innerHTML = `<p>${message}</p>`;
    }

    async handleLogin(e) {
        e.preventDefault();

        const form = e.target;
        const status = form.querySelector(".roomStatus");
        const formData = new FormData(form);

        this.displayStatusMessage(status, plugnmeet_frontend.i18n.checking, 'info');

        try {
            const res = await fetch(plugnmeet_frontend.ajaxurl, {
                method: 'POST',
                body: formData
            });

            if (!res.ok) {
                this.displayStatusMessage(status, res.statusText, 'danger');
                return;
            }

            const data = await res.json();

            if (data.status) {
                this.displayStatusMessage(status, plugnmeet_frontend.i18n.redirecting, 'success');

                const windowOpen = window.open(data.url, "_blank");
                if (!windowOpen) {
                    setTimeout(() => {
                        if (!windowOpen) {
                            window.location.href = data.url;
                        }
                    }, 5000);
                }

                const passwordField = form.querySelector("#room-password");
                if (passwordField) {
                    passwordField.value = "";
                }
                setTimeout(() => {
                    status.style.display = 'none';
                }, 2000);

            } else {
                this.displayStatusMessage(status, data.msg, 'danger');
            }
        } catch (error) {
            this.displayStatusMessage(status, error.message, 'danger');
        }
    }
}

window.addEventListener('load', () => {
    new PlugNMeetPublicLogin();
});
