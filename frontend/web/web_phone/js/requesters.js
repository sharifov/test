(function () {
    function CallRequester() {
        this.settings = {
            'holdUrl': '',
            'unHoldUrl': '',
            'acceptCallUrl': '',
            'muteUrl': '',
            'unMuteUrl': '',
            'returnHoldCallUrl': '',
            'ajaxHangupUrl': '',
            'callAddNoteUrl': '',
            'sendDigitUrl': '',
            'prepareCurrentCallsUrl': '',
            'callLogInfoUrl': '',
            'callInfoUrl': '',
            'clientInfoUrl': '',
            'recordingEnableUrl': '',
            'recordingDisableUrl': '',
        };

        this.init = function (settings) {
            Object.assign(this.settings, settings);
        };

        this.hold = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid
                },
                url: this.settings.holdUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Hold', data.message, 'error');
                        call.unSetHoldUnHoldRequestState();
                    }
                })
                .fail(function () {
                    createNotify('Hold', 'Server error', 'error');
                    call.unSetHoldUnHoldRequestState();
                })
        };

        this.unHold = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid
                },
                url: this.settings.unHoldUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Resume', data.message, 'error');
                        call.unSetHoldUnHoldRequestState();
                    }
                })
                .fail(function () {
                    createNotify('Resume', 'Server error', 'error');
                    call.unSetHoldUnHoldRequestState();
                })
        };

        this.accept = function (call) {
            window.phoneWidget.notifier.off(call.data.callSid);
            PhoneWidgetCall.audio.incoming.off(call.data.callSid);
            $.ajax({
                type: 'post',
                url: this.settings.acceptCallUrl,
                dataType: 'json',
                data: {
                    act: 'accept',
                    call_sid: call.data.callSid
                }
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Accept Call', data.message, 'error');
                        call.unSetAcceptCallRequestState();
                        window.phoneWidget.notifier.on(call.data.callSid);
                        PhoneWidgetCall.audio.incoming.on(call.data.callSid);
                    }
                })
                .fail(function () {
                    createNotify('Accept Call', 'Server error', 'error');
                    call.unSetAcceptCallRequestState();
                    window.phoneWidget.notifier.on(call.data.callSid);
                    PhoneWidgetCall.audio.incoming.on(call.data.callSid);
                })
        };

        this.mute = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid
                },
                url: this.settings.muteUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Mute', data.message, 'error');
                        call.unSetMuteUnMuteRequestState();
                    }
                })
                .fail(function () {
                    createNotify('Mute', 'Server error', 'error');
                    call.unSetMuteUnMuteRequestState();
                })
        };

        this.unMute = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid
                },
                url: this.settings.unMuteUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('UnMute', data.message, 'error');
                        call.unSetMuteUnMuteRequestState();
                    }
                })
                .fail(function () {
                    createNotify('UnMute', 'Server error', 'error');
                    call.unSetMuteUnMuteRequestState();
                })
        };

        this.returnHoldCall = function (call) {
            window.phoneWidget.notifier.off(call.data.callSid);
            PhoneWidgetCall.audio.incoming.off(call.data.callSid);
            $.ajax({
                type: 'post',
                url: this.settings.returnHoldCallUrl,
                dataType: 'json',
                data: {
                    call_sid: call.data.callSid
                }
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Return Hold Call', data.message, 'error');
                        call.unSetReturnHoldCallRequestState();
                        window.phoneWidget.notifier.on(call.data.callSid);
                        PhoneWidgetCall.audio.incoming.on(call.data.callSid);
                    }
                })
                .fail(function () {
                    createNotify('Return Hold Call', 'Server error', 'error');
                    call.unSetReturnHoldCallRequestState();
                    window.phoneWidget.notifier.on(call.data.callSid);
                    PhoneWidgetCall.audio.incoming.on(call.data.callSid);
                })
        };

        this.hangupOutgoingCall = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid,
                },
                url: this.settings.ajaxHangupUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Hangup', data.message, 'error');
                        call.unSetHangupRequestState();
                    }
                })
                .fail(function () {
                    createNotify('Hangup', 'Server error', 'error');
                    call.unSetHangupRequestState();
                })
        };

        this.addNote = function (call, note, $container) {
            $.ajax({
                type: 'post',
                data: {
                    note: note,
                    callSid: call.data.callSid
                },
                url: this.settings.callAddNoteUrl,
                dataType: 'json'
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Add Note', data.message, 'error');
                    } else {
                        createNotify('Add Note', data.message, 'success');
                        $container.value = '';
                    }
                    call.unSetAddNoteRequestState();
                })
                .fail(function () {
                    createNotify('Add Note', 'Server error', 'error');
                    call.unSetAddNoteRequestState();
                });
        };

        this.sendDigit = function (conferenceSid, digit) {
            $.ajax({
                type: 'post',
                data: {
                    conference_sid: conferenceSid,
                    digit: digit
                },
                url: this.settings.sendDigitUrl,
                dataType: 'json'
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Send digit', data.message, 'error');
                    }
                })
                .fail(function () {
                    createNotify('Send digit', 'Server error', 'error');
                });
        };

        this.acceptInternalCall = function (call, connection) {
            $.ajax({
                type: 'post',
                data: {},
                url: this.settings.prepareCurrentCallsUrl,
                dataType: 'json'
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Prepare current call', data.message, 'error');
                        call.unSetAcceptCallRequestState();
                    } else {
                        connection.accept();
                    }
                })
                .fail(function () {
                    createNotify('Prepare current call', 'Server error', 'error');
                    call.unSetAcceptCallRequestState();
                });
        };

        this.callLogInfo = function (sid) {
            $('#call-box-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"> </i> Loading ...</div>');
            $('#call-box-modal-label').html('Call Info');
            $('#call-box-modal').modal();
            $.ajax({
                type: 'post',
                data: {sid: sid},
                url: this.settings.callLogInfoUrl,
            })
                .done(function (data) {
                    $('#call-box-modal .modal-body').html(data);
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    createNotify('Call info', xhr.responseText, 'error');
                });
        };

        this.callInfo = function (sid) {
            $('#call-box-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"> </i> Loading ...</div>');
            $('#call-box-modal-label').html('Call Info');
            $('#call-box-modal').modal();
            $.ajax({
                type: 'post',
                data: {sid: sid},
                url: this.settings.callInfoUrl,
            })
                .done(function (data) {
                    $('#call-box-modal .modal-body').html(data);
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    createNotify('Call info', xhr.responseText, 'error');
                });
        };

        this.clientInfo = function (id, isClient) {
            $('#call-box-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"> </i> Loading ...</div>');
            let text = isClient ? 'Client details' : 'Contact info';
            $('#call-box-modal-label').html(text + ' (' + id + ')');
            $('#call-box-modal').modal();
            $.ajax({
                type: 'post',
                data: {client_id: id},
                url: this.settings.clientInfoUrl
            })
                .done(function (data) {
                    $('#call-box-modal .modal-body').html(data);
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    createNotify(text, xhr.responseText, 'error');
                });
        };

        this.recordingEnable = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid
                },
                url: this.settings.recordingEnableUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Recording enable', data.message, 'error');
                        call.unSetRecordingRequestState();
                    }
                })
                .fail(function () {
                    createNotify('Recording enable', 'Server error', 'error');
                    call.unSetRecordingRequestState();
                })
        };

        this.recordingDisable = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid
                },
                url: this.settings.recordingDisableUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Recording disable', data.message, 'error');
                        call.unSetRecordingRequestState();
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    createNotify('Recording disable', jqXHR.responseText, 'error');
                    call.unSetRecordingRequestState();
                })
        };
    }

    return window.phoneWidget.requesters = {
        CallRequester: CallRequester
    }
})();
