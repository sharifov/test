var PhoneWidgetCall = function () {
    this.connection = '';

    function init (options)
    {
        muteBtnEvent();
        transferCallBtnEvent(options);
        acceptCallBtnEvent(options);
        changeUserCallStatusEvent(options);
        rejectIncomingCallEvent(options);

        console.log(options);

        if ('isCallInProgress' in options && options.isCallInProgress) {
            refreshCallStatus({
                'status': "In progress",
                'duration': options.duration | 0,
                'type': parseInt(options.type),
                'source_type_id': parseInt(options.source_type_id),
                'type_description': options.type_description,
                'is_hold': options.is_hold
            });
        } else if ('isCallRinging' in options && options.isCallRinging) {
            initIncomingCall({
                'fromInternal': options.fromInternal,
                'cua_call_id': options.call_id,
                'phoneFrom': options.phoneFrom,
                'name': options.name || '',
                'type': options.type,
                'source_type_id': parseInt(options.source_type_id),
                'type_description': options.type_description
            });
        }
    }

    function initCall(selectedNumber)
    {
        $('.calling-from-info__identifier').html(selectedNumber.from.project);
        $('.calling-from-info__number').html(selectedNumber.from.value);
        $('.call-pane-calling').find('.contact-info-card__name_text').html(selectedNumber.to.callToName);
        $('.call-pane-calling').find('.contact-info-card__call-type').html(selectedNumber.to.phone);
        $('.phone-widget-icon').addClass('is-pending');
        $('.call-pane__call-btns').addClass('is-pending');
        $('.suggested-contacts').removeClass('is_active');
        // $('.call-in-action__time').hide();
        $('.call-pane-initial').removeClass('is_active');
        $('.call-pane-calling').addClass('is_active');
        $('.call-in-action__text').html('Dialing');
        $('.call-pane-initial .contact-info-card__label').html('To');
        $('.call-in-action__time').html('').show().timer('remove').timer({format: '%M:%S', seconds: 0}).timer('start');
    }

    function cancelCall()
    {
        $('.phone-widget-icon').removeClass('is-on-call');
        $('.phone-widget-icon').removeClass('is-pending');
        $('.call-pane__call-btns').removeClass('is-on-call');
        $('.call-pane__call-btns').removeClass('is-pending');
        $('.call-pane-initial').removeClass('is_active');
        $('.call-pane').addClass('is_active');
        $('.call-in-action__time').hide();
        window.connection = '';
    }

    function rejectIncomingCallEvent(options)
    {
        $(document).on('click', '#reject-incoming-call', function(e) {
            e.preventDefault();
            var btn = $(this);

            if (window.connection) {
                window.connection.reject();
                $.get(options.ajaxSaveCallUrl + '?sid=' + window.connection.parameters.CallSid + '&user_id=' + btn.attr('data-user-id'));
                $('#call-controls2').hide();
            }
            cancelCall();
        })
    }

    function changeUserCallStatusEvent(options)
    {
        $(document).on('change', '.call-status-switcher', function () {
            var type_id = $(this).prop('checked') ? 1 : 2;
            $.ajax({
                type: 'post',
                data: {'type_id': type_id},
                url: options.callStatusUrl,
                success: function (data) {},
                error: function (error) {
                    console.error('Error: ' + error);
                }
            });
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
            let connection = _self.connection;
            let mute = $(this);
            if (mute.attr('data-is-muted') === 'false') {
                if (connection) {
                    connection.mute(true);
                    if (connection.isMuted()) {
                        mute.html('<i class="fas fa-microphone-alt-slash"></i>').attr('data-is-muted', true);
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
        });
    }

    function updateConnection(conn)
    {
        this.connection = conn;
    }

    function transferCallBtnEvent(options)
    {
        $(document).on('click', '#wg-transfer-call', function(e) {
            e.preventDefault();
            initRedirectToAgent(options);
        });
    }

    function initRedirectToAgent(options)
    {
        if (options.ajaxCallRedirectGetAgents === undefined) {
            alert('Ajax call redirect url is not set');
            return false;
        }

        let callSid = getActiveConnectionCallSid();
        if (callSid) {
            let modal = $('#web-phone-redirect-agents-modal');
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            $('#web-phone-redirect-agents-modal-label').html('Transfer Call');

            $.post(options.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
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

    function refreshCallStatus(obj) {

        $('.call-pane-initial .contact-info-card__label').html(obj.type_description);

        if (obj.status === 'In progress') {
            openWidget();
            obj.status = 'On Call';
            $('.call-pane__call-btns').removeClass('is-pending').addClass('is-on-call');
            $('.call-pane-initial .contact-info-card__call-type').html(obj.phoneFrom);
            $('.call-pane-initial .contact-info-card__name_text').html(obj.name);
            if ('type' in obj && obj.type && obj.type === 3) {
                $('#wg-transfer-call').hide();
                $('#wg-add-person').hide();
                if ('source_type_id' in obj && obj.source_type_id && obj.source_type_id === 7) {
                    $('#call-pane__mute').hide();
                } else {
                    $('#call-pane__mute').show();
                }
            } else {
                $('#call-pane__mute').show();
                $('#wg-transfer-call').show();
                $('#wg-add-person').show();
            }

            let btnHold = $('.btn-hold-call');
            if (obj.type === 3) {
                btnHold.prop('disabled', true);
                if (obj.is_hold) {
                    btnHold.html('<i class="fa fa-close"></i> <span>Unhold</span>');
                } else {
                    btnHold.html('<i class="fa fa-close"></i> <span>Hold</span>');
                }
            } else {
                btnHold.prop('disabled', false);
                if (obj.is_hold) {
                    btnHold.html('<i class="fa fa-play"></i> <span>Unhold</span>');
                    btnHold.data('mode', 'hold');
                } else {
                    btnHold.html('<i class="fa fa-pause"></i> <span>Hold</span>');
                    btnHold.data('mode', 'unhold');
                }
            }

            showCallingPanel();
        }else if(['Ringing', 'Queued'].includes(obj.status)) {
            openWidget();
            $('.call-pane-incoming.call-pane-initial .contact-info-card__label').html(obj.type_description);
            $('.call-pane-incoming.call-pane-initial .contact-info-card__name_text').html(obj.name);
            $('.call-pane-calling .contact-info-card__label').html(obj.type_description);
            $('.call-pane-calling .contact-info-card__name_text').html(obj.name);
            if ('isIn' in obj && obj.isIn) {
                initIncomingCall(obj);
            }
        }else if (obj.status === 'Completed') {
            cancelCall();
        }else {
            $('.call-pane__call-btns').removeClass('is-on-call');
        }
        $('.call-in-action__text').html(obj.status);
        $('.call-in-action__time').html('').show().timer('remove').timer({format: '%M:%S', seconds: status.duration | 0}).timer('start');

        // if ('isIn' in obj && obj.isIn) {
        //     $('.call-pane-initial .contact-info-card__label').html('Incoming');
        // } else {
        //     $('.call-pane-initial .contact-info-card__label').html('Outgoing');
        // }
    }

    function initIncomingCall(obj)
    {
        // clearCallLayersInfo();
        openWidget();
        openCallTab();
        if (typeof obj === 'object' && 'phoneFrom' in obj) {
            $('#btn-accept-call').attr('data-from-internal', obj.fromInternal | false).attr('data-call-id', obj.cua_call_id);
            showIncomingCallPanel(obj.phoneFrom, obj.name || '', obj.type_description);
        } else if (obj.cua_status_id === 5) {
            cancelCall();
        }
    }

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

    function showIncomingCallPanel(phone, name, type_description)
    {
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-incoming').addClass('is_active');
        $('#btn-accept-call').find('i').removeClass('fa fa-spinner fa-spin').addClass('fas fa-check');
        $('.call-pane-incoming .contact-info-card__label').html(type_description);
        $('.call-pane-incoming .contact-info-card__name_text').html(name);
        $('.call-pane-incoming .contact-info-card__call-type').html(phone);
    }

    function showCallingPanel()
    {
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-calling').addClass('is_active');
    }

    function acceptCallBtnEvent(options)
    {
        $(document).on('click', '#btn-accept-call', function () {

            if (typeof device == "undefined" || device == null || (device && device._status !== 'ready')) {
                new PNotify({title: "Accept call", type: "warning", text: "Please try again after some seconds. Device is not ready.", hide: true});
                return false;
            }
            var btn = $(this);
            var fromInternal = btn.attr('data-from-internal');
            if (fromInternal && window.connection) {
                window.connection.accept();
                showCallingPanel();
                $('#call-controls2').hide();
            } else {
                $.ajax({
                    type: 'post',
                    url: options.acceptCallUrl,
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

    function clearCallLayersInfo() {
        $('.call-pane-initial .contact-info-card__label').html('');
        $('.call-pane-initial .contact-info-card__name_text').html('');
        $('.call-pane-initial .contact-info-card__call-type').html('');
    }

    return {
        init: init,
        initCall: initCall,
        cancelCall: cancelCall,
        volumeIndicatorsChange: volumeIndicatorsChange,
        updateConnection: updateConnection,
        refreshCallStatus: refreshCallStatus,
        initIncomingCall: initIncomingCall,
        clearCallLayersInfo: clearCallLayersInfo
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

    function loadNotFound() {
        let content = '<li class="calls-history__item contact-info-card">' +
            '<div class="collapsible-toggler">' +
            '<div class="contact-info-card__details">' +
            '<div class="contact-info-card__line history-details">' +
            '<strong class="contact-info-card__name">No results found</strong>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</li>';
        return content;
    }

    $(document).on('click', "li.call-contact-card", function () {
        let phone = $(this).data('phone');
        let title = $(this).data('title');
        insertPhoneNumber(phone, title);
        $('.suggested-contacts').removeClass('is_active');
    });

})();