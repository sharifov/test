var PhoneWidgetCall = function () {
    this.connection = '';

    function init (options)
    {
        muteBtnEvent();
        transferCallBtnEvent(options);
        acceptCallBtnEvent(options);
    }

    function initCall(selectedNumber)
    {
        $('.calling-from-info__identifier').html(selectedNumber.from.project);
        $('.calling-from-info__number').html(selectedNumber.from.value);
        $('.call-pane-calling').find('.contact-info-card__name').html(selectedNumber.to.callToName);
        $('.call-pane-calling').find('.contact-info-card__call-type').html(selectedNumber.to.phone);
        $('.phone-widget-icon').addClass('is-pending');
        $('.call-pane__call-btns').addClass('is-pending');
        $('.suggested-contacts').removeClass('is_active');
        // $('.call-in-action__time').hide();
        $('.call-pane').removeClass('is_active');
        $('.call-pane-calling').addClass('is_active');
        $('.call-in-action__text').html('Dialing');
        $('.call-in-action__time').html('').show().timer('remove').timer({format: '%M:%S', seconds: 0}).timer('start');
    }

    function cancelCall()
    {
        $('.phone-widget-icon').removeClass('is-on-call');
        $('.phone-widget-icon').removeClass('is-pending');
        $('.call-pane__call-btns').removeClass('is-on-call');
        $('.call-pane__call-btns').removeClass('is-pending');
        $('.call-pane-calling').removeClass('is_active');
        $('.call-pane').addClass('is_active');
        $('.call-in-action__time').hide();
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
            console.log(mute.attr('data-is-muted') === 'false');
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
        let connection = this.connection;
        if (options.ajaxCallRedirectGetAgents === undefined) {
            alert('Ajax call redirect url is not set');
            return false;
        }

        if (connection && connection.parameters.CallSid) {
            let callSid = connection.parameters.CallSid;
            let modal = $('#web-phone-redirect-agents-modal');
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            $('#web-phone-redirect-agents-modal-label').html('Transfer Call');

            $.post(options.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
                .done(function(data) {
                    modal.find('.modal-body').html(data);
                });
        } else {
            alert('Error: Not found Call connection or Call SID!');
        }
        return false;
    }

    function refreshCallStatus(obj)
    {
        if ('isIn' in obj && obj.isIn) {
            initIncomingCall(obj);
        }

        if (obj.status === 'In progress') {
            obj.status = 'On Call';
            $('.call-pane__call-btns').removeClass('is-pending').addClass('is-on-call');
        }else {
            $('.call-pane__call-btns').removeClass('is-on-call');
        }
        $('.call-in-action__text').html(obj.status);
        $('.call-in-action__time').html('').show().timer('remove').timer({format: '%M:%S', seconds: status.duration | 0}).timer('start');
    }

    function initIncomingCall(obj)
    {
        openWidget();
        openCallTab();
        if (typeof obj === 'object' && 'phoneFrom' in obj) {
            $('#btn-accept-call').attr('data-call-id', obj.cua_call_id);
            showIncomingCallPanel(obj.phoneFrom, obj.name || '');
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

    function showIncomingCallPanel(phone, name)
    {
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-incoming').addClass('is_active');
        $('#btn-accept-call').find('i').removeClass('fa fa-spinner fa-spin').addClass('fas fa-check');
        $('.call-pane-incoming .contact-info-card__name').html(name);
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
            var btn = $(this);
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
        });
    }

    return {
        init: init,
        initCall: initCall,
        cancelCall: cancelCall,
        volumeIndicatorsChange: volumeIndicatorsChange,
        updateConnection: updateConnection,
        refreshCallStatus: refreshCallStatus,
        initIncomingCall: initIncomingCall
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
                    content += loadNotFound();
                    timeout = setTimeout(function () {
                        $('.suggested-contacts').removeClass('is_active');
                    }, 2000);
                } else {
                    $.each(data.results, function(i, item) {
                        content += loadContact(item);
                    });
                    $('.suggested-contacts').html(content);
                    $('.suggested-contacts').addClass('is_active');
                    $('.call-pane__dial-clear-all').addClass('is-shown')
                }
                $('.suggested-contacts').html(content);
                $('.suggested-contacts').addClass('is_active');
                $('.call-pane__dial-clear-all').addClass('is-shown')
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
        let contactIcon = '';
        if (contact['type'] === 3) {
            contactIcon = '<div class="contact-info-card__status">' +
                '<i class="far fa-user ' + contact['user_status_class'] + ' "></i>' +
                '</div>';
        }
        let content = '<li class="calls-history__item contact-info-card call-contact-card" data-phone="' + contact['phone'] + '">' +
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
        $("#call-pane__dial-number").val(phone);
        $('.suggested-contacts').removeClass('is_active');
    });

})();