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
        };

        this.init = function (settings) {
            Object.assign(this.settings, settings);
        };

        this.hold = function (call) {
            //todo remove after removed old widget
            let btn = $('.btn-hold-call');
            btn.html('<i class="fa fa-spinner fa-spin"> </i> <span>Hold</span>');
            btn.prop('disabled', true);

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
                        btn.html('<i class="fa fa-pause"> </i> <span>Hold</span>');
                        btn.prop('disabled', false);

                        call.unSetHoldUnHoldRequestState();
                    }
                })
                .fail(function () {
                    createNotify('Hold', 'Server error', 'error');
                    btn.html('<i class="fa fa-pause"> </i> <span>Hold</span>');
                    btn.prop('disabled', false);

                    call.unSetHoldUnHoldRequestState();
                })
        };

        this.unHold = function (call) {
            //todo remove after removed old widget
            let btn = $('.btn-hold-call');
            btn.html('<i class="fa fa-spinner fa-spin"> </i> <span>Unhold</span>');
            btn.prop('disabled', true);

            $.ajax({
                type: 'post',
                data: {
                    'sid': call.data.callSid
                },
                url: this.settings.unHoldUrl
            })
                .done(function (data) {
                    if (data.error) {
                        createNotify('UnHold', data.message, 'error');
                        btn.html('<i class="fa fa-play"> </i> <span>Unhold</span>');
                        btn.prop('disabled', false);

                        call.unSetHoldUnHoldRequestState();
                    }
                })
                .fail(function () {
                    createNotify('UnHold', 'Server error', 'error');
                    btn.html('<i class="fa fa-play"> </i> <span>Unhold</span>');
                    btn.prop('disabled', false);

                    call.unSetHoldUnHoldRequestState();
                })
        };

        this.accept = function (call) {
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
                    }
                })
                .fail(function () {
                    createNotify('Accept Call', 'Server error', 'error');
                    call.unSetAcceptCallRequestState();
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
                    }
                })
                .fail(function () {
                    createNotify('Return Hold Call', 'Server error', 'error');
                    call.unSetReturnHoldCallRequestState();
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
                .done(function(data) {
                    if (data.error) {
                        createNotify('Hangup', data.message, 'error');
                        call.unSetHangupRequestState();
                    }
                })
                .fail(function() {
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
    }

    return window.phoneWidget.requesters = {
        CallRequester: CallRequester
    }
})();
