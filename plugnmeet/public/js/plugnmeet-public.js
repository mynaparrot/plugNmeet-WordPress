class PlugNMeetPublicLogin {
    constructor(formElement) {
        this.formElement = formElement;
        this.roomId = formElement.dataset.roomId;

        this.formElement.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent default form submission
            this.handleLogin(e);
        });
    }

    displayStatusMessage(statusEl, message, type) {
        statusEl.style.display = 'block';
        statusEl.classList.remove('notice-info', 'notice-success', 'notice-danger');
        statusEl.classList.add(`notice-${type}`);
        statusEl.innerHTML = `<p>${message}</p>`;
    }

    async handleLogin(e) {
        const form = e.target;
        const status = this.formElement.querySelector(".roomStatus");
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

                const passwordField = this.formElement.querySelector("#room-password");
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
    const loginForms = document.querySelectorAll('.plugnmeet-login-form');
    loginForms.forEach(form => {
        new PlugNMeetPublicLogin(form);
    });

    // check if returned from conference
    const searchParams = new URLSearchParams(document.location.search);
    if (searchParams.has("pnm-returned", "true")) {
        // this will only work if link opened with window.open()
        window.close();
    }
});
