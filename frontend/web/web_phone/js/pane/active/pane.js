var PhoneWidgetPaneActive = function () {

    let buttons = {
        'hold': PhoneWidgetPaneActiveBtnHold,
        'transfer': PhoneWidgetPaneActiveBtnTransfer,
        'addPerson': PhoneWidgetPaneActiveBtnAddPerson,
        'dialpad': PhoneWidgetPaneActiveBtnDialpad,
        'mute': PhoneWidgetPaneActiveBtnMute
    };

    function initActiveControls() {
        buttons.hold.initActive();
        buttons.transfer.initActive();
        buttons.addPerson.initActive();
        buttons.dialpad.initActive();
    }

    function initInactiveControls() {
        buttons.hold.initInactive();
        buttons.transfer.initInactive();
        buttons.addPerson.initInactive();
        buttons.dialpad.initInactive();
    }

    /*
        data = {
            callId,
            isMute,
            isListen,
            isHold,
            typeId,
            type,
            phone,
            name,
            duration
        }
     */
    function load(data) {
        if (data.isMute) {
            buttons.mute.mute();
        } else {
            buttons.mute.unmute();
        }

        if (data.isListen) {
            buttons.mute.mute();
            buttons.mute.disable();
            buttons.mute.inactive();
        } else {
            buttons.mute.unmute();
            buttons.mute.enable();
            buttons.mute.active();
        }

        if (data.typeId === 3) {
            initInactiveControls();
        } else {
            initActiveControls();
        }


        if (data.isHold) {
            buttons.hold.unhold();
        } else {
            buttons.hold.hold();
        }

        setCallId(data.callId);
        $('.contact-info-card__label').html(data.type);
        $('.call-pane-initial .contact-info-card__call-type').html(data.phone);
        $('#wg-active-call-name').html(data.name);
        $('.call-in-action__text').html('On Call');
        $('.call-in-action__time').html('').show().timer('remove').timer({
            format: '%M:%S',
            seconds: data.duration
        }).timer('start');
    }

    function setCallId(callId) {
        $('.call-pane-calling').attr('data-call-id', callId);
    }

    function getCallId() {
        return parseInt($('.call-pane-calling').attr('data-call-id'));
    }

    function removeCallId() {
        return $('.call-pane-calling').attr('data-call-id', '');
    }

    function show() {
        $('.call-pane__call-btns').removeClass('is-pending').addClass('is-on-call');
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-calling').addClass('is_active');
    }

    function clear() {
        $('.contact-info-card__label').html('');
        $('.call-pane-initial .contact-info-card__call-type').html('');
        $('#wg-active-call-name').html('');
        $('.call-in-action__text').html('');
        $('.call-in-action__time').html('').show().timer('remove');
    }

    return {
        buttons: buttons,
        initInactiveControls: initInactiveControls,
        initActiveControls: initActiveControls,
        load: load,
        show: show,
        clear: clear,
        getCallId: getCallId,
        removeCallId: removeCallId
    }

}();
