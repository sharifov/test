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
            'addPhoneBlackListUrl': '',
            'reconnectUrl': '',
            'ajaxCallTransferUrl': '',
            'ajaxWarmTransferToUserUrl': '',
            'ajaxCallRedirectUrl': '',
            'ajaxJoinToConferenceUrl': '',
            'csrf_param': '',
            'csrf_token': '',
            'ajaxGetPhoneListIdUrl': '',
            'createCallUrl': ''
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

        this.accept = function (call, deviceId) {
            window.phoneWidget.notifier.off(call.data.callSid);
            PhoneWidget.audio.incoming.off(call.data.callSid);
            $.ajax({
                type: 'post',
                url: this.settings.acceptCallUrl,
                dataType: 'json',
                data: {
                    act: 'accept',
                    call_sid: call.data.callSid,
                    deviceId: deviceId
                }
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Accept Call', data.message, 'error');
                        call.unSetAcceptCallRequestState();
                        window.phoneWidget.notifier.on(call.data.callSid);
                        PhoneWidget.audio.incoming.on(call.data.callSid);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Accept Call', message, 'error');
                    call.unSetAcceptCallRequestState();
                    window.phoneWidget.notifier.on(call.data.callSid);
                    PhoneWidget.audio.incoming.on(call.data.callSid);
                })
        };

        this.acceptWarmTransfer = function (call, deviceId) {
            window.phoneWidget.notifier.off(call.data.callSid);
            PhoneWidget.audio.incoming.off(call.data.callSid);
            $.ajax({
                type: 'post',
                url: this.settings.acceptWarmTransferCallUrl,
                dataType: 'json',
                data: {
                    call_sid: call.data.callSid,
                    deviceId: deviceId
                }
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Accept Call', data.message, 'error');
                        call.unSetAcceptCallRequestState();
                        window.phoneWidget.notifier.on(call.data.callSid);
                        PhoneWidget.audio.incoming.on(call.data.callSid);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Accept Call', message, 'error');
                    call.unSetAcceptCallRequestState();
                    window.phoneWidget.notifier.on(call.data.callSid);
                    PhoneWidget.audio.incoming.on(call.data.callSid);
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

        this.returnHoldCall = function (call, deviceId) {
            window.phoneWidget.notifier.off(call.data.callSid);
            PhoneWidget.audio.incoming.off(call.data.callSid);
            $.ajax({
                type: 'post',
                url: this.settings.returnHoldCallUrl,
                dataType: 'json',
                data: {
                    call_sid: call.data.callSid,
                    deviceId: deviceId
                }
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Return Hold Call', data.message, 'error');
                        call.unSetReturnHoldCallRequestState();
                        window.phoneWidget.notifier.on(call.data.callSid);
                        PhoneWidget.audio.incoming.on(call.data.callSid);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Return Hold Call', message, 'error');
                    call.unSetReturnHoldCallRequestState();
                    window.phoneWidget.notifier.on(call.data.callSid);
                    PhoneWidget.audio.incoming.on(call.data.callSid);
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

        this.hangup = function (call) {
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
                    if (typeof data.result !== 'undefined' && typeof data.result.status !== 'undefined' && data.result.status === 'completed') {
                        PhoneWidget.completeCall(call.data.callSid);
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

        this.acceptInternalCall = function (call, twilioCall) {
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
                        twilioCall.accept();
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

        this.acceptPriorityCall = function (key, deviceId) {
            PhoneWidget.queues.priority.accept();
            window.phoneWidget.notifier.off(key);
            PhoneWidget.audio.incoming.off(key);
            $.ajax({
                type: 'post',
                url: this.settings.acceptPriorityCallUrl,
                dataType: 'json',
                data: {
                    act: 'accept',
                    deviceId: deviceId
                }
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('Accept Call', data.message, 'error');
                    }
                    if (data.isRedialCall) {
                        if (data.redialError) {
                            createNotify('Accept Call', data.redialError, 'error');
                        } else {
                            window.phoneWidget.notifier.notifiers.phone.reset();
                            PhoneWidget.panes.queue.hide();
                            PhoneWidget.openCallTab();
                            // PhoneWidget.showCallingPanel();
                        }
                    } else {
                        PhoneWidget.audio.incoming.on(key);
                    }
                    PhoneWidget.queues.priority.unAccept();
                    window.phoneWidget.notifier.on(key);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Accept Call', message, 'error');
                    PhoneWidget.queues.priority.unAccept();
                    window.phoneWidget.notifier.on(key);
                    PhoneWidget.audio.incoming.on(key);
                })
        };

        this.reconnect = function (call) {
            $.ajax({
                type: 'post',
                data: {
                    sid: call.data.callSid
                },
                url: this.settings.reconnectUrl
            })
                .done(function (data) {
                    if (data.error) {
                        call.unSetReconnectRequestState();
                        createNotify('Reconnect', data.message, 'error');
                        return;
                    }
                    PhoneWidget.openHoldCallPanel();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    var message = jqXHR.responseText ? jqXHR.responseText : (jqXHR.statusText ? jqXHR.statusText : 'Server error');
                    createNotify('Reconnect', message, 'error');
                    call.unSetReconnectRequestState();
                })
        };

        this.transfer = function (callSid, objValue, objType, modal) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': callSid,
                    'id': objValue,
                    'type': objType
                },
                url: this.settings.ajaxCallTransferUrl,
                success: function (data) {
                    if (data.error) {
                        alert(data.message);
                    }
                    modal.modal('hide').find('.modal-body').html('');
                },
                error: function (error) {
                    console.error(error);
                    modal.modal('hide').find('.modal-body').html('');
                }
            });
        };

        this.warmTransferToUser = function (callSid, userId, modal) {
            $.ajax({
                type: 'post',
                data: {
                    'callSid': callSid,
                    'userId': userId
                },
                url: this.settings.ajaxWarmTransferToUserUrl,
                success: function (data) {
                    if (data.error) {
                        alert(data.message);
                    }
                    modal.modal('hide').find('.modal-body').html('');
                },
                error: function (error) {
                    console.error(error);
                    modal.modal('hide').find('.modal-body').html('');
                }
            });
        };

        this.transferNumber = function (callSid, type, from, to, modal) {
            $.ajax({
                type: 'post',
                data: {
                    'sid': callSid,
                    'type': type,
                    'from': from,
                    'to': to,
                },
                url: this.settings.ajaxCallRedirectUrl,
                success: function (data) {
                    if (data.error) {
                        alert(data.message);
                    }
                    modal.modal('hide').find('.modal-body').html('');
                },
                error: function (error) {
                    console.error(error);
                    modal.modal('hide').find('.modal-body').html('');
                }
            });
        };

        this.joinConference = function (source_type, source_type_id, call_sid, deviceId) {
            let data = {
                'call_sid': call_sid,
                'source_type_id': source_type_id,
                'deviceId': deviceId
            };
            data[this.settings.csrf_param] = this.settings.csrf_token;
            $.ajax({
                type: 'post',
                data: data,
                url: this.settings.ajaxJoinToConferenceUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotifyByObject({title: source_type, type: "error", text: data.message, hide: true});
                    }
                })
                .fail(function (error) {
                    createNotifyByObject({title: source_type, type: "error", text: "Server error", hide: true});
                    console.error(error);
                })
        };

        // todo remove and move to backend
        this.webCallLeadRedial = function (phone_from, phone_to, project_id, lead_id, type, c_source_type_id) {
            $.post(this.settings.ajaxGetPhoneListIdUrl, {'phone': phone_from}, function (data) {
                if (data.error) {
                    var text = 'Error. Try again later';
                    if (data.message) {
                        text = data.message;
                    }
                    createNotifyByObject({title: "Make call", type: "error", text: text, hide: true});
                } else {
                    let params = {
                        params: {
                            'To': phone_to,
                            'FromAgentPhone': phone_from,
                            'c_project_id': project_id,
                            'lead_id': lead_id,
                            'c_type': type,
                            'c_user_id': window.userId,
                            'c_source_type_id': c_source_type_id,
                            'is_conference_call': 1,
                            'user_identity': window.userIdentity,
                            'phone_list_id': data.phone_list_id
                        }
                    };

                    if (window.TwilioDevice) {
                        console.log('Calling ' + params.params.To + '...');
                        connection = window.TwilioDevice.connect(params);
                    }
                }
            }, 'json');
        };

        this.createCall = function (data) {
            $.ajax({
                type: 'post',
                data: data,
                url: this.settings.createCallUrl
            })
                .done(function (data) {
                    if (data.error) {
                        PhoneWidget.freeDialButton();
                        createNotify('Create Call', data.message, 'error');
                        if (data.is_on_call === true) {
                            PhoneWidget.updateCurrentCalls(data.phone_widget_data.calls, data.phone_widget_data.userStatus);
                        }
                        return;
                    }
                })
                .fail(function () {
                    createNotify('Create Call', 'Server error', 'error');
                    PhoneWidget.freeDialButton();
                })
        };
    }

    return window.phoneWidget.requesters = {
        CallRequester: CallRequester
    }
})();
