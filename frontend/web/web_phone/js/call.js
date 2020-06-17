var PhoneWidgetCall = function () {

    this.connection = '';
    this.obj;

    let statusCheckbox = null;

    const $addNoteInput = $('#active_call_add_note');
    const $addNoteSubmit = $('#active_call_add_note_submit');

    let settings = {
        'ajaxCallRedirectGetAgents': '',
        'acceptCallUrl': '',
        'callStatusUrl': '',
        'ajaxSaveCallUrl': '',
        'muteUrl': '',
        'unmuteUrl': '',
        'callAddNoteUrl': ''
    };

    let panes = {
        'active': PhoneWidgetPaneActive,
        'incoming': PhoneWidgetPaneIncoming,
        'outgoing': PhoneWidgetPaneOutgoing
    };

    function init(options)
    {
        console.log(options);

        settings.ajaxCallRedirectGetAgents = options.ajaxCallRedirectGetAgents;
        settings.acceptCallUrl = options.acceptCallUrl;
        settings.callStatusUrl = options.callStatusUrl;
        settings.ajaxSaveCallUrl = options.ajaxSaveCallUrl;
        settings.muteUrl = options.muteUrl;
        settings.unmuteUrl = options.unmuteUrl;
        settings.callAddNoteUrl = options.callAddNoteUrl;

        statusCheckbox = new widgetStatus('.call-status-switcher', options.updateStatusUrl);

        setCountMissedCalls(options.countMissedCalls);

        muteBtnEvent();
        transferCallBtnEvent();
        acceptCallBtnEvent();
        rejectIncomingCallEvent();
        callAddNoteEvent();
    }

    function incomingCall(data) {
        console.log('incoming call');
        // console.log(data);

        panes.incoming.load(data);
        panes.incoming.show();
        openWidget();
        openCallTab();
    }

    function outgoingCall(data) {
        console.log('outgoing call');
        // console.log(data);

        panes.outgoing.load(data);
        panes.outgoing.show();
        openWidget();
        openCallTab();
    }

    function activeCall(data) {
        console.log('active call');
        // console.log(data);

        panes.active.load(data);
        panes.active.show();
        openWidget();
        openCallTab();
    }

    function cancelCall(callId)
    {
        if (panes.active.getCallId() === callId) {
            panes.active.removeCallId();
            panes.active.clear();
            $('.phone-widget-icon').removeClass('is-on-call').removeClass('is-pending');
            $('.call-pane__call-btns').removeClass('is-on-call').removeClass('is-pending');
            $('.call-pane-initial').removeClass('is_active');
            $('.call-pane').addClass('is_active');
            $('.call-in-action__time').hide();
            window.connection = '';
        }
        if (panes.outgoing.getCallId() === callId) {
            panes.outgoing.removeCallId();
            panes.outgoing.hide();
        }
        if (panes.incoming.getCallId() === callId) {
            panes.incoming.removeCallId();
            panes.incoming.hide();
        }
        if (panes.active.getCallId()) {
            panes.active.show();
        } else if (panes.outgoing.getCallId()) {
            panes.outgoing.show();
        } else {
            $('.call-pane').addClass('is_active');
        }
    }

    function rejectIncomingCallEvent()
    {
        $(document).on('click', '#reject-incoming-call', function(e) {
            e.preventDefault();
            if (window.connection) {
                window.connection.reject();
                $.get(settings.ajaxSaveCallUrl + '?sid=' + window.connection.parameters.CallSid);
                $('#call-controls2').hide();
            }
            cancelCall();
        })
    }

    function callAddNoteEvent() {
        var _self = this;
        $addNoteSubmit.on('click', function (e) {
            e.preventDefault();
            let btnHtml = $(this).html();
            let callSid = getActiveConnectionCallSid();
            let callId = _self.obj ? _self.obj.id : null;
            if (!callSid && !callId) {
                createNotify('Warning', 'Call Sid & Call Id is undefined', 'warning');
                return false;
            }

            let value = $addNoteInput.val().trim();
            if (!value) {
                createNotify('Warning', 'Note value is empty', 'warning');
                return false;
            }

            $.ajax({
                type: 'post',
                data: {'callSid': callSid, note: value, callId: callId},
                url: settings.callAddNoteUrl,
                dataType: 'json',
                beforeSend: function () {
                    $addNoteSubmit.html('<i class="fa fa-spinner fa-spin" style="color: #fff;"></i>').attr('disabled', 'disabled');
                },
                success: function (data) {
                    if (data.error) {
                        createNotify('Error', data.message, 'error');
                    } else {
                        createNotify('Success', data.message, 'success');
                    }
                },
                error: function (error) {
                    createNotify('Error', error.responseText, 'error');
                },
                complete: function () {
                    $addNoteSubmit.html(btnHtml).removeAttr('disabled');
                }
            })

        });
    }
    
    // function bindVolumeIndicators(connection)
    // {
    //     connection.on('volume', function (inputVolume, outputVolume) {
    //         volumeIndicatorsChange(inputVolume, outputVolume);
    //     });
    // }

    function volumeIndicatorsChange(inputVolume, outputVolume) {
        $('#wg-call-microphone .sound-ovf').css('right', -Math.floor(inputVolume*100) + '%');
        $('#wg-call-volume .sound-ovf').css('right', -Math.floor(outputVolume*100) + '%');
    }

    function muteBtnEvent()
    {
        let _self = this;
        $(document).on('click', '#call-pane__mute', function(e) {

            let muteBtn = $(this);

            if (conferenceBase) {

                let callSid = getActiveConnectionCallSid();

                if (callSid) {
                    if (muteBtn.attr('data-is-muted') === 'false') {
                       mute(callSid);
                    } else if (muteBtn.attr('data-is-muted') === 'true') {
                       unmute(callSid);
                    }
                } else {
                    alert('Error: Not found active Connection CallSid');
                }

            } else {
                let connection = _self.connection;
                if (muteBtn.attr('data-is-muted') === 'false') {
                    if (connection) {
                        connection.mute(true);
                        if (connection.isMuted()) {
                            muteBtn.html('<i class="fas fa-microphone-alt-slash"></i>').attr('data-is-muted', true);
                        } else {
                            new PNotify({title: "Mute", type: "error", text: "Error", hide: true});
                        }
                    }
                } else {
                    if (connection) {
                        connection.mute(false);
                        if (!connection.isMuted()) {
                            $(this).html('<i class="fas fa-microphone"></i>').attr('data-is-muted', false);
                        } else {
                            new PNotify({title: "Unmute", type: "error", text: "Error", hide: true});
                        }
                    }
                }
            }
        });
    }

    function mute(callSid) {
        let btn = panes.active.buttons.mute;
        btn.sendRequest();

        $.ajax({type: 'post', data: {'sid': callSid}, url: settings.muteUrl})
            .done(function (data) {
                if (data.error) {
                    new PNotify({title: "Mute", type: "error", text: data.message, hide: true});
                    btn.unmute();
                } else {
                    // new PNotify({title: "Hold", type: "success", text: 'Wait', hide: true});
                }
            })
            .fail(function (error) {
                new PNotify({title: "Hold", type: "error", text: data.message, hide: true});
                btn.unmute();
                console.error(error);
            })
            .always(function () {

            });
    }

    function unmute(callSid) {
        let btn = panes.active.buttons.mute;
        btn.sendRequest();

        $.ajax({type: 'post', data: {'sid': callSid}, url: settings.unmuteUrl})
            .done(function (data) {
                if (data.error) {
                    new PNotify({title: "Unmute", type: "error", text: data.message, hide: true});
                    btn.mute();
                } else {
                    // new PNotify({title: "Unmute", type: "success", text: 'Wait', hide: true});
                }
            })
            .fail(function (error) {
                new PNotify({title: "Unmute", type: "error", text: data.message, hide: true});
                btn.mute();
                console.error(error);
            })
            .always(function () {

            });
    }

    function updateConnection(conn)
    {
        this.connection = conn;
    }

    function transferCallBtnEvent()
    {
        $(document).on('click', '#wg-transfer-call', function(e) {
            e.preventDefault();
            if (!panes.active.buttons.transfer.can()) {
                return false;
            }
            initRedirectToAgent();
        });
    }

    function initRedirectToAgent()
    {
        if (settings.ajaxCallRedirectGetAgents === undefined) {
            alert('Ajax call redirect url is not set');
            return false;
        }

        let callSid = getActiveConnectionCallSid();
        if (callSid) {
            let modal = $('#web-phone-redirect-agents-modal');
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            $('#web-phone-redirect-agents-modal-label').html('Transfer Call');

            $.post(settings.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
                .done(function(data) {
                    modal.find('.modal-body').html(data);
                });
        } else {
            alert('Error: Not found active connection Call SID!');
        }

        // let connection = this.connection;
        // if (connection && connection.parameters.CallSid) {
        //     let callSid = connection.parameters.CallSid;
        //     let modal = $('#web-phone-redirect-agents-modal');
        //     modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        //     $('#web-phone-redirect-agents-modal-label').html('Transfer Call');
        //
        //     $.post(options.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
        //         .done(function(data) {
        //             modal.find('.modal-body').html(data);
        //         });
        // } else {
        //     alert('Error: Not found Call connection or Call SID!');
        // }
        return false;
    }

    function refreshCallStatus(obj)
    {
        if (obj.status === 'In progress') {
            activeCall(obj);
        } else if (obj.status === 'Ringing' || obj.status === 'Queued') {
            if (obj.typeId === 2) {
                incomingCall(obj);
            } else if (obj.typeId === 1) {
                outgoingCall(obj);
            }
        } else if (obj.status === 'Completed' || obj.isEnded) {
            cancelCall(obj.callId);
        }

        this.obj = obj;

    }

    function updateProjectAndSourceUI(projectName, sourceName)
    {
        if (projectName) {
            $('.cw-project_name').html(projectName).show();
        } else {
            $('.cw-project_name').html('').hide();
        }
        if (sourceName) {
            $('.cw-source_name').html(sourceName);
            $('.cw-source_name *').show();
        } else {
            $('.cw-source_name').html('');
            $('.cw-source_name *').hide();
        }
    }

    // function refreshCallStatus(obj) {
    //
    //     $('.call-pane-initial .contact-info-card__label').html(obj.type_description);
    //
    //     if (obj.is_mute) {
    //         panes.active.buttons.mute.mute();
    //     } else {
    //         panes.active.buttons.mute.unmute();
    //     }
    //
    //     if (obj.is_listen) {
    //         panes.active.buttons.mute.disable();
    //     } else {
    //         panes.active.buttons.mute.enable();
    //     }
    //
    //     if (obj.status === 'In progress') {
    //         openWidget();
    //         obj.status = 'On Call';
    //         $('.call-pane__call-btns').removeClass('is-pending').addClass('is-on-call');
    //         $('.call-pane-initial .contact-info-card__call-type').html(obj.phoneFrom);
    //         $('.call-pane-initial .contact-info-card__name_text').html(obj.name);
    //
    //         if ('type' in obj && obj.type && obj.type === 3) {
    //             panes.active.initInactiveControls();
    //         } else {
    //             panes.active.initActiveControls();
    //         }
    //
    //         if (obj.type === 3) {
    //             if (obj.is_hold) {
    //                 panes.active.buttons.hold.unhold();
    //             } else {
    //                 panes.active.buttons.hold.hold();
    //             }
    //         } else {
    //             if (obj.is_hold) {
    //                 panes.active.buttons.hold.unhold();
    //             } else {
    //                 panes.active.buttons.hold.hold();
    //             }
    //         }
    //
    //         showCallingPanel();
    //         $('#cw-client_name').html(obj.name);
    //         $('#cw-project_name').html(obj.projectName);
    //         $('#cw-source_name').html(obj.sourceName);
    //     }else if(['Ringing', 'Queued'].includes(obj.status)) {
    //         openWidget();
    //         $('.call-pane-incoming.call-pane-initial .contact-info-card__label').html(obj.type_description);
    //         $('.call-pane-incoming.call-pane-initial .contact-info-card__name_text').html(obj.name);
    //         $('.call-pane-calling .contact-info-card__label').html(obj.type_description);
    //         $('.call-pane-calling .contact-info-card__name_text').html(obj.name);
    //         if ('isIn' in obj && obj.isIn) {
    //             initIncomingCall(obj);
    //             showCallingPanel();
    //         } else if ('isIn' in obj && !obj.isIn) {
    //             initIncomingCall(obj);
    //         } else {
    //             showCallingPanel();
    //         }
    //         $('#cw-client-name').html(obj.name);
    //         $('#cw-client_name').html(obj.name);
    //         $('#cw-project_name').html(obj.projectName);
    //         $('#cw-source_name').html(obj.sourceName);
    //         // $('.call-pane__call-btns').addClass('is-pending');
    //     }else if (obj.status === 'Completed') {
    //         cancelCall();
    //     }else {
    //         $('.call-pane__call-btns').removeClass('is-on-call');
    //     }
    //     $('.call-in-action__text').html(obj.status);
    //     $('.call-in-action__time').html('').show().timer('remove').timer({format: '%M:%S', seconds: obj.duration | 0}).timer('start');
    //
    //     // if ('isIn' in obj && obj.isIn) {
    //     //     $('.call-pane-initial .contact-info-card__label').html('Incoming');
    //     // } else {
    //     //     $('.call-pane-initial .contact-info-card__label').html('Outgoing');
    //     // }
    // }

    function openWidget()
    {
        $('.phone-widget').addClass('is_active');
        $('.js-toggle-phone-widget').removeClass('is-mirror');
    }

    function openCallTab()
    {
        $('.phone-widget__tab').removeClass('is_active');
        $('[data-toggle-tab]').removeClass('is_active');
        $('#tab-phone').addClass('is_active');
    }

    function showIncomingCallPanel(phone, name, type_description, projectName, sourceName)
    {
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-incoming').addClass('is_active');
        $('#btn-accept-call').find('i').removeClass('fa fa-spinner fa-spin').addClass('fas fa-check');
        $('.call-pane-incoming .contact-info-card__label').html(type_description);
        $('#cw-client_name').html(name);
        updateProjectAndSourceUI(projectName, sourceName);
        $('.call-pane-incoming .contact-info-card__call-type').html(phone);
    }

    function updateProjectAndSourceUI(pane, projectName, sourceName)
    {
        if (projectName) {
            $('.cw-project_name').html(projectName).show();
        } else {
            $('.cw-project_name').html('').hide();
        }
        if (sourceName) {
            $('.cw-source_name').html(sourceName);
            $('.cw-source_name *').show();
        } else {
            $('.cw-source_name').html('');
            $('.cw-source_name *').hide();
        }
    }

    function showCallingPanel()
    {
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-calling').addClass('is_active');
    }

    function acceptCallBtnEvent()
    {
        $(document).on('click', '#btn-accept-call', function () {

            if (typeof device == "undefined" || device == null || (device && device._status !== 'ready')) {
                new PNotify({title: "Accept call", type: "warning", text: "Please try again after some seconds. Device is not ready.", hide: true});
                return false;
            }

            var btn = $(this);
            var fromInternal = btn.attr('data-from-internal');
            if (fromInternal !== 'false' && window.connection) {
                window.connection.accept();
                showCallingPanel();
                $('#call-controls2').hide();
            } else {
                $.ajax({
                    type: 'post',
                    url: settings.acceptCallUrl,
                    dataType: 'json',
                    data: {act: 'accept', call_id: btn.attr('data-call-id')},
                    beforeSend: function () {
                        btn.addClass('disabled');
                        btn.find('i').removeClass('fas fa-check').addClass('fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        if (data.error) {
                             new PNotify({
                                title: "Error",
                                type: "error",
                                text: data.message,
                                hide: true
                            });
                        } else {
                            showCallingPanel();
                        }
                    },
                    complete: function () {
                        btn.removeClass('disabled');
                        btn.find('i').addClass('fas fa-check').removeClass('fa fa-spinner fa-spin');
                    }
                })
            };
        });
    }

    function changeStatus(status) {
        statusCheckbox.setStatus(status);
    }

    function setCountMissedCalls(count) {
        $('[data-toggle-tab="tab-history"]').attr('data-missed-calls', count);
    }

    return {
        init: init,
        cancelCall: cancelCall,
        volumeIndicatorsChange: volumeIndicatorsChange,
        updateConnection: updateConnection,
        refreshCallStatus: refreshCallStatus,
        panes: panes,
        incomingCall: incomingCall,
        activeCall: activeCall,
        outgoingCall: outgoingCall,
        changeStatus: changeStatus
    };
}();

(function() {

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    $("#call-pane__dial-number").on('keyup', delay(function() {
        $('.suggested-contacts').removeClass('is_active');
        let contactList = $("#contact-list-calls-ajax");
        let q = contactList.find("input[name=q]").val();
        if (q.length < 3) {
            return false;
        }
        contactList.submit();
    }, 300));

    let timeout = '';
    $('#contact-list-calls-ajax').on('beforeSubmit', function (e) {
        e.preventDefault();
        let yiiform = $(this);
        let q = yiiform.find("input[name=q]").val();
        if (q.length < 3) {
            //  new PNotify({
            //     title: "Search contacts",
            //     type: "warning",
            //     text: 'Minimum 2 symbols',
            //     hide: true
            // });
            return false;
        }
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: yiiform.serializeArray(),
                dataType: 'json',
            }
        )
            .done(function(data) {
                let content = '';
                if (timeout) {
                    clearTimeout(timeout);
                }
                if (data.results.length < 1) {
                    // content += loadNotFound();
                    // timeout = setTimeout(function () {
                    //     $('.suggested-contacts').removeClass('is_active');
                    // }, 2000);
                } else {
                    $.each(data.results, function(i, item) {
                        content += loadContact(item);
                    });
                    $('.suggested-contacts').html(content).addClass('is_active');
                    $('.call-pane__dial-clear-all').addClass('is-shown')
                }
                //$('.suggested-contacts').html(content).addClass('is_active');
                //$('.call-pane__dial-clear-all').addClass('is-shown')
            })
            .fail(function () {
                new PNotify({
                    title: "Search contacts",
                    type: "error",
                    text: 'Server Error. Try again later',
                    hide: true
                });
            });
        return false;
    });

    function loadContact(contact) {
        //  type = 3 = Internal contact
        console.log(contact);
        let contactIcon = '';
        if (contact['type'] === 3) {
            contactIcon = '<div class="contact-info-card__status">' +
                '<i class="far fa-user ' + contact['user_status_class'] + ' "></i>' +
                '</div>';
        }
        let content = '<li class="calls-history__item contact-info-card call-contact-card" data-phone="' + contact['phone'] + '" data-title="' + contact['title'] + '">' +
            '<div class="collapsible-toggler">' +
            contactIcon
            + '<div class="contact-info-card__details">' +
            '<div class="contact-info-card__line history-details">' +
            '<strong class="contact-info-card__name">' + contact['name'] + '</strong>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</li>';
        return content;
    }

    // function loadNotFound() {
    //     let content = '<li class="calls-history__item contact-info-card">' +
    //         '<div class="collapsible-toggler">' +
    //         '<div class="contact-info-card__details">' +
    //         '<div class="contact-info-card__line history-details">' +
    //         '<strong class="contact-info-card__name">No results found</strong>' +
    //         '</div>' +
    //         '</div>' +
    //         '</div>' +
    //         '</li>';
    //     return content;
    // }

    $(document).on('click', "li.call-contact-card", function () {
        let phone = $(this).data('phone');
        let title = $(this).data('title');
        insertPhoneNumber(phone, title);
        $('.suggested-contacts').removeClass('is_active');
    });

})();