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
            'acceptPriorityCallUrl': '',
            'acceptWarmTransferCallUrl': '',
            'addPhoneBlackListUrl': ''
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Hold', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Resume', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Accept Call', message, 'error');
                    call.unSetAcceptCallRequestState();
                    window.phoneWidget.notifier.on(call.data.callSid);
                    PhoneWidgetCall.audio.incoming.on(call.data.callSid);
                })
        };

        this.acceptWarmTransfer = function (call) {
            window.phoneWidget.notifier.off(call.data.callSid);
            PhoneWidgetCall.audio.incoming.off(call.data.callSid);
            $.ajax({
                type: 'post',
                url: this.settings.acceptWarmTransferCallUrl,
                dataType: 'json',
                data: {
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Accept Call', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Mute', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('UnMute', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Return Hold Call', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Hangup', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Add Note', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Send digit', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Prepare current call', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Call info', message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Call info', message, 'error');
                });
        };

        this.addPhoneBlackList = function (phone) {
            if (confirm('Confirm adding ' + phone + ' number to blacklist')) {
                let btnIcon = $('.btn-add-in-blacklist').html();
                $.ajax({
                    url: this.settings.addPhoneBlackListUrl,
                    type: 'post',
                    data: {phone: phone},
                    dataType: 'json',
                    beforeSend: function () {
                        $('.btn-add-in-blacklist').html('<i class="fa fa-spin fa-spinner"></i>').prop('disabled', true);
                    },
                    success: function (resp) {
                        if (resp.error) {
                            createNotify('Error', resp.message, 'error');
                        } else {
                            if (resp.notifier) {
                                createNotify('Success', 'Phone number: ' + phone + ' added to blacklist', 'success');
                            }
                            $('.btn-add-in-blacklist[data-phone="'+phone+'"]').remove();
                        }
                    },
                    error: function (xhr) {
                        createNotify('Error', xhr.responseText, 'error');
                    },
                    complete: function () {
                        $('.btn-add-in-blacklist').html(btnIcon).prop('disabled', false);
                    }
                });
            }
        };

        this.clientInfo = function (id, callSid, isClient) {
            $('#call-box-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"> </i> Loading ...</div>');
            let text = isClient ? 'Client details' : 'Contact info';
            $('#call-box-modal-label').html(text + ' (' + id + ')');
            $('#call-box-modal').modal();
            $.ajax({
                type: 'post',
                data: {client_id: id, callSid: callSid},
                url: this.settings.clientInfoUrl
            })
                .done(function (data) {
                    $('#call-box-modal .modal-body').html(data);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify(text, message, 'error');
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
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Recording enable', message, 'error');
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
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Recording disable', message, 'error');
                    call.unSetRecordingRequestState();
                })
        };

        this.acceptPriorityCall = function (key) {
            PhoneWidgetCall.queues.priority.accept();
            window.phoneWidget.notifier.off(key);
            PhoneWidgetCall.audio.incoming.off(key);
            $.ajax({
                type: 'post',
                url: this.settings.acceptPriorityCallUrl,
                dataType: 'json',
                data: {
                    act: 'accept',
                }
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Accept Call', data.message, 'error');
                    }
                    if (data.isRedialCall) {
                        window.phoneWidget.notifier.notifiers.phone.reset();
                        PhoneWidgetCall.panes.queue.hide();
                        PhoneWidgetCall.openCallTab();
                        PhoneWidgetCall.showCallingPanel();
                        webCallLeadRedialPriority(data.redialCall);
                    } else {
                        PhoneWidgetCall.audio.incoming.on(key);
                    }
                    PhoneWidgetCall.queues.priority.unAccept();
                    window.phoneWidget.notifier.on(key);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Accept Call', message, 'error');
                    PhoneWidgetCall.queues.priority.unAccept();
                    window.phoneWidget.notifier.on(key);
                    PhoneWidgetCall.audio.incoming.on(key);
                })
        };
    }

    return window.phoneWidget.requesters = {
        CallRequester: CallRequester
    }
})();
