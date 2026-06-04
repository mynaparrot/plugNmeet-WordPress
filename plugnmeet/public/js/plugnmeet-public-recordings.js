class PlugNMeetPublicRecordings {
    constructor() {
        this.CAN_PLAY = plugnmeet_recordings.can_play;
        this.CAN_DOWNLOAD = plugnmeet_recordings.can_download;
        this.CAN_DELETE = plugnmeet_recordings.can_delete;
        this.roomId = plugnmeet_recordings.room_id;
        this.isShowingPagination = false;
        this.totalRecordings = 0;
        this.currentPage = 1;
        this.limitPerPage = 10;
        this.showPre = false;
        this.showNext = true;

        this.addEventListeners();
        this.fetchRecordings();
    }

    addEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.id === 'backward') {
                e.preventDefault();
                if (!this.showPre) return;
                this.currentPage--;
                this.paginate(this.currentPage);
            } else if (e.target.id === 'forward') {
                e.preventDefault();
                if (!this.showNext) return;
                this.currentPage++;
                this.paginate(this.currentPage);
            } else if (e.target.matches('.play-recording')) {
                this.playRecording(e);
            } else if (e.target.matches('.download-recording')) {
                this.downloadRecording(e);
            } else if (e.target.matches('.delete-recording')) {
                this.deleteRecording(e);
            }
        });

        jQuery('body').on('thickbox:removed', () => {
            document.getElementById("modalPlayer").src = "";
        });
    }

    async fetchRecordings(from = 0, limit = 10) {
        const formData = new FormData();
        formData.append('nonce', plugnmeet_recordings.nonce.get_recordings);
        formData.append('action', 'plugnmeet_get_recordings');
        formData.append('from', from);
        formData.append('limit', limit);
        formData.append('roomId', this.roomId);
        formData.append('order_by', 'DESC');

        const data = await this.sendRequest(formData);
        if (!data) return;

        if (!data.status) {
            this.showMessage(data.msg);
            return;
        }

        const result = JSON.parse(data.result);
        const recordings = result.recordingsList;
        if (!recordings || recordings.length === 0) {
            this.showMessage(plugnmeet_recordings.i18n.no_recordings);
            return;
        }

        this.totalRecordings = result.totalRecordings;
        if (this.totalRecordings > this.limitPerPage && !this.isShowingPagination) {
            this.showPagination();
            this.isShowingPagination = true;
        }
        this.displayRecordings(recordings);
    }

    async downloadRecording(e) {
        e.preventDefault();
        const recordId = e.target.dataset.recording;
        const formData = new FormData();
        formData.append('nonce', plugnmeet_recordings.nonce.download_recording);
        formData.append('action', 'plugnmeet_download_recording');
        formData.append('roomId', this.roomId);
        formData.append('recordingId', recordId);

        const res = await this.sendRequest(formData);
        if (res && res.status) {
            window.open(res.url, "_blank");
        } else if (res) {
            alert(res.msg);
        }
    }

    async playRecording(e) {
        e.preventDefault();
        const recordId = e.target.dataset.recording;
        const title = e.target.closest('tr').querySelector('td:first-child').innerHTML;
        const formData = new FormData();
        formData.append('nonce', plugnmeet_recordings.nonce.download_recording);
        formData.append('action', 'plugnmeet_download_recording');
        formData.append('roomId', this.roomId);
        formData.append('recordingId', recordId);
        formData.append('role', 'can_play');

        const res = await this.sendRequest(formData);
        if (res && res.status) {
            const modalPlayer = document.getElementById("modalPlayer");
            modalPlayer.src = res.url;
            tb_show(title, '#TB_inline?height=450&amp;inlineId=playbackModal');
            setTimeout(() => {
                const player = document.getElementById('modalPlayer');
                if (player) {
                    player.style.width = '100%';
                    player.style.height = '400px';
                }
            }, 100);
        } else if (res) {
            alert(res.msg);
        }
    }

    async deleteRecording(e) {
        e.preventDefault();
        if (confirm(plugnmeet_recordings.i18n.confirm_delete) !== true) return;

        const recordId = e.target.dataset.recording;
        const formData = new FormData();
        formData.append('nonce', plugnmeet_recordings.nonce.delete_recording);
        formData.append('action', 'plugnmeet_delete_recording');
        formData.append('roomId', this.roomId);
        formData.append('recordingId', recordId);

        const res = await this.sendRequest(formData);
        if (res && res.status) {
            alert(res.msg);
            document.getElementById(recordId).remove();
        } else if (res) {
            alert(res.msg);
        }
    }

    displayRecordings(recordings) {
        let html = '';
        recordings.forEach((recording, i) => {
            const fileSize = (recording.fileSize && typeof recording.fileSize === 'number') ? recording.fileSize.toFixed(2) : '0.00';
            html += `<tr id="${recording.recordId}">
                <td>${new Date(recording.creationTime * 1e3).toLocaleString()}</td>
                <td>${new Date(recording.roomCreationTime * 1e3).toLocaleString()}</td>
                <td>${fileSize}</td>
                <td style="text-align: right">`;

            if (this.CAN_PLAY) {
                html += `<a href="#" class="button button-small play-recording" data-recording="${recording.recordId}">${plugnmeet_recordings.i18n.play}</a>`;
            }
            if (this.CAN_DOWNLOAD) {
                html += ` <a href="#" class="button button-small download-recording" data-recording="${recording.recordId}">${plugnmeet_recordings.i18n.download}</a>`;
            }
            if (this.CAN_DELETE) {
                html += ` <a href="#" class="button button-small delete-recording" data-recording="${recording.recordId}">${plugnmeet_recordings.i18n.delete}</a>`;
            }
            html += '</td></tr>';
        });
        document.getElementById('recordingListsBody').innerHTML = html;
    }

    showPagination() {
        this.currentPage = 1;
        document.querySelector('.pagination-links').style.display = '';
        this.paginate(this.currentPage);
    }

    paginate(page) {
        this.currentPage = page;
        document.getElementById('recordingListsBody').innerHTML = `<tr><td colspan="4">${plugnmeet_recordings.i18n.loading}</td></tr>`;
        const from = (this.currentPage - 1) * this.limitPerPage;

        this.showPre = this.currentPage > 1;
        document.getElementById('backward').disabled = !this.showPre;

        this.showNext = this.currentPage < this.totalRecordings / this.limitPerPage;
        document.getElementById('forward').disabled = !this.showNext;

        this.fetchRecordings(from, this.limitPerPage);
    }

    showMessage(msg) {
        document.getElementById('recordingListsBody').innerHTML = `<tr><td colspan="4">${msg}</td></tr>`;
    }

    async sendRequest(formData) {
        try {
            const res = await fetch(plugnmeet_frontend.ajaxurl, {
                method: 'POST',
                body: formData
            });
            if (!res.ok) {
                console.error(res.status, res.statusText);
                alert(res.statusText);
                return null;
            }
            return await res.json();
        } catch (e) {
            console.error(e);
            alert(e);
            return null;
        }
    }
}

window.addEventListener('load', () => {
    new PlugNMeetPublicRecordings();
});
