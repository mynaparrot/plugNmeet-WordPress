(function ($) {
    'use strict';

    function showPlugnmeetAdminNotice(message, type) {
        const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
        $('#wpbody-content > .wrap').before(notice);
        setTimeout(function () {
            notice.fadeOut();
        }, 5000);
    }

    function toggleDisplayClientDownloadUrl(client_load) {
        if (client_load === "local") {
            $("#client_download_url").parent().parent().show();
        } else {
            $("#client_download_url").parent().parent().hide();
        }
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
                } else if (data.msg) {
                    showPlugnmeetAdminNotice(data.msg, 'error');
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

    const AI_AGENT_PREFIX = 'insights_features[ai_agent_features]';
    const MAX_AI_USERS = 32;

    function aiAgentField(name) {
        return $('[name="' + AI_AGENT_PREFIX + '[' + name + ']"]');
    }

    function debounce(fn, wait) {
        let timer = null;

        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                fn.apply(context, args);
            }, wait);
        };
    }

    function currentRoomMode() {
        // Room mode is only choosable when the header switcher is rendered.
        if (!$('#pnm-mode-switcher').length) {
            return 'conference';
        }

        const $checked = $('[name="room_mode"]:checked');
        if ($checked.length) {
            return $checked.val() === 'ai_agent' ? 'ai_agent' : 'conference';
        }

        return $('[name="room_mode"]').val() === 'ai_agent' ? 'ai_agent' : 'conference';
    }

    function updateRoomModeUi() {
        const aiMode = currentRoomMode() === 'ai_agent';
        const hiddenTabs = '.nav-tab[href="#room-features"], .nav-tab[href="#other-features"], .nav-tab[href="#lock"]';

        $('[name="room_mode"]').each(function () {
            $(this).closest('.pnm-mode-option').toggleClass('is-active', this.checked);
        });

        $(hiddenTabs).toggle(!aiMode);
        $('[name="max_participants"]').closest('tr').toggle(!aiMode);
        $('.nav-tab[href="#insights"]').text(aiMode ? ajax_admin.i18n.ai_agent_tab : ajax_admin.i18n.insights_tab);

        if (aiMode && $(hiddenTabs).filter('.nav-tab-active').length) {
            $('.nav-tab[href="#basic"]').trigger('click');
        }

        updateInsightsCards();
    }

    function updateInsightsCards() {
        const $card = $('#pnm-ai-agent-card');

        if (!$card.length) {
            return;
        }

        const aiMode = currentRoomMode() === 'ai_agent';
        const agentOn = aiMode || aiAgentField('is_allow').val() === '1';
        const controlled = aiMode && aiAgentField('session_mode').val() === '1';

        $('#pnm-insights-standard-card').toggle(!aiMode);
        $card.show();
        $card.find('tr.pnm-ai-agent-main').toggle(!aiMode);
        $card.find('tr.pnm-ai-agent-session').toggle(aiMode);
        $card.find('tr.pnm-ai-agent-multi').toggle(aiMode && !controlled);
        $card.find('tr.pnm-ai-agent-participants').toggle(controlled);
        $card.find('tr.pnm-ai-agent-detail').toggle(agentOn);
    }

    function selectedUserIds() {
        return $('#pnm-selected-users input[type="hidden"]').map(function () {
            return String($(this).val());
        }).get();
    }

    function updateSelectedCount() {
        $('#pnm-selected-users-count, .pnm-selected-count').text(selectedUserIds().length);
    }

    function removeUserChip(id) {
        $('#pnm-selected-users input[type="hidden"]').filter(function () {
            return String($(this).val()) === String(id);
        }).closest('.pnm-user-chip').remove();
        updateSelectedCount();
    }

    function addUserChip(user) {
        const ids = selectedUserIds();

        if (ids.indexOf(String(user.id)) !== -1) {
            return;
        }

        if (ids.length >= MAX_AI_USERS) {
            showPlugnmeetAdminNotice(ajax_admin.i18n.max_users, 'error');
            return;
        }

        const $chip = $('<span class="pnm-user-chip"></span>');
        $chip.append($('<span class="pnm-user-chip-label"></span>').text(user.display_name + ' (' + user.user_email + ')'));
        $chip.append($('<button type="button" class="pnm-user-chip-remove"></button>')
            .attr('aria-label', ajax_admin.i18n.remove_user)
            .html('&times;'));
        $chip.append($('<input type="hidden">')
            .attr('name', AI_AGENT_PREFIX + '[auto_session_config][allowed_participant_ids][]')
            .val(user.id));

        $('#pnm-selected-users').append($chip);
        updateSelectedCount();
    }

    function searchUsers() {
        const $results = $('#pnm-user-search-results').empty();

        $results.append($('<li class="pnm-user-search-empty"></li>').text(ajax_admin.i18n.searching));

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'plugnmeet_search_users',
                nonce: ajax_admin.nonce_search_users,
                search: $('#pnm-user-search-input').val() || '',
                role: $('#pnm-user-role-filter').val() || ''
            },
            success: function (data) {
                $results.empty();

                if (!data.status || !data.users.length) {
                    $results.append($('<li class="pnm-user-search-empty"></li>').text(ajax_admin.i18n.no_users));
                    return;
                }

                const ids = selectedUserIds();

                data.users.forEach(function (user) {
                    const $checkbox = $('<input type="checkbox">')
                        .val(user.id)
                        .prop('checked', ids.indexOf(String(user.id)) !== -1);

                    $checkbox.on('change', function () {
                        if ($(this).is(':checked')) {
                            addUserChip(user);
                        } else {
                            removeUserChip(user.id);
                        }
                    });

                    const label = ' ' + user.display_name + ' (' + user.user_email + ') [' + user.roles.join(', ') + ']';
                    $results.append($('<li></li>').append($('<label></label>').append($checkbox).append(document.createTextNode(label))));
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showPlugnmeetAdminNotice(textStatus + ': ' + errorThrown, 'error');
            }
        });
    }

    function toggleFrontendColorFields(val) {
        if (val === "clean" || val === "modern") {
            $("#frontend_primary_color").parent().parent().show();
            $("#frontend_secondary_color").parent().parent().show();
        } else {
            $("#frontend_primary_color").parent().parent().hide();
            $("#frontend_secondary_color").parent().parent().hide();
        }
    }

    $(document).ready(function () {
        $('.pnm-color-picker').wpColorPicker();
        toggleDisplayClientDownloadUrl($("#client_load").val())
        toggleFrontendColorFields($("#frontend_theme").val())

        const urlParams = new URLSearchParams(window.location.search);
        const roomId = urlParams.get('id');
        const storageKey = 'pnm_room_last_tab_' + roomId;

        $('.nav-tab').on('click', function (e) {
            e.preventDefault();
            const target = $(this).attr('href');

            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            $('.plugnmeet-tab-content').hide();
            $(target).show();

            if (roomId) {
                sessionStorage.setItem(storageKey, target);
            }
        });

        if (roomId) {
            const lastTab = sessionStorage.getItem(storageKey);
            if (lastTab) {
                $('.nav-tab[href="' + lastTab + '"]').click();
            }
        }

        updateRoomModeUi();
        updateSelectedCount();
    })

    $(document).on("change", '[name="room_mode"]', updateRoomModeUi)
    $(document).on("change", '[name="' + AI_AGENT_PREFIX + '[is_allow]"], [name="' + AI_AGENT_PREFIX + '[session_mode]"]', updateInsightsCards)

    $(document).on("change", '[name="' + AI_AGENT_PREFIX + '[preset_template]"]', function () {
        const presets = ajax_admin.ai_presets || {};
        const preset = presets[$(this).val()];

        if (!preset) {
            return;
        }

        const $prompt = aiAgentField('system_prompt');
        const current = String($prompt.val()).trim();

        if (current !== '' && Object.values(presets).indexOf(current) === -1 && !confirm(ajax_admin.i18n.replace_prompt)) {
            return;
        }

        $prompt.val(preset);
        ['allow_whiteboard_write', 'allow_vision_capture', 'allow_polls', 'allow_notepad_write', 'allow_chat_message']
            .forEach(function (field) {
                aiAgentField(field).val('1');
            });
    })

    $(document).on("click", "#pnm-select-users", function (e) {
        e.preventDefault();
        $("#pnm-user-search-modal").show();
        $("#pnm-user-search-input").trigger("focus");
        searchUsers();
    })

    $(document).on("click", "#pnm-user-search-close, #pnm-user-search-done, .pnm-modal-backdrop", function (e) {
        e.preventDefault();
        $("#pnm-user-search-modal").hide();
    })

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            $("#pnm-user-search-modal").hide();
        }
    })

    $(document).on("input", "#pnm-user-search-input", debounce(searchUsers, 300))
    $(document).on("change", "#pnm-user-role-filter", searchUsers)

    $(document).on("click", ".pnm-user-chip-remove", function (e) {
        e.preventDefault();
        $(this).closest(".pnm-user-chip").remove();
        updateSelectedCount();
    })

    $(document).on("click", "#pnm-clear-selected-users", function (e) {
        e.preventDefault();
        $("#pnm-selected-users").empty();
        updateSelectedCount();
    })

    $(document).on("change", "#client_load", (e) => {
        toggleDisplayClientDownloadUrl($(e.target).val())
    })

    $(document).on("change", "#frontend_theme", (e) => {
        toggleFrontendColorFields($(e.target).val())
    })

    // Collapsible card logic. Toggling is data-attribute/CSS based only,
    // so collapsed (display: none) inputs still serialize with the form.
    $(document).on("click", ".pnm-card-toggle", (e) => {
        e.preventDefault();
        const $toggle = $(e.currentTarget);
        const $card = $toggle.closest(".pnm-card");
        const collapsed = $card.attr("data-collapsed") === "true";

        $card.attr("data-collapsed", collapsed ? "false" : "true");
        $toggle.attr("aria-expanded", collapsed ? "true" : "false");
    })

    $(document).on("click", ".pnm-expand-all", (e) => {
        e.preventDefault();
        $(e.currentTarget).closest(".plugnmeet-tab-content")
            .find('.pnm-card[data-collapsed="true"]')
            .attr("data-collapsed", "false")
            .find(".pnm-card-toggle")
            .attr("aria-expanded", "true");
    })

    $(document).on("click", ".pnm-collapse-all", (e) => {
        e.preventDefault();
        $(e.currentTarget).closest(".plugnmeet-tab-content")
            .find('.pnm-card[data-collapsed="false"]')
            .attr("data-collapsed", "true")
            .find(".pnm-card-toggle")
            .attr("aria-expanded", "false");
    })
})(jQuery);
