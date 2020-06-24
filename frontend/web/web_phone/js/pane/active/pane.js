var PhoneWidgetPaneActive = function () {

    let state = {
        'callId': null
    };

    let $pane = $('.call-pane-calling');
    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    function render(data) {
        let html = '';
        let template = activeTpl;
        $.each(data, function (k, v) {
            html = template.split('{{' + k + '}}').join(v);
            template = html;
        });
        return html;
    }

    let buttons = {
        'hold': new PhoneWidgetPaneActiveBtnHold($pane),
        'transfer': new PhoneWidgetPaneActiveBtnTransfer($pane),
        'addPerson': new PhoneWidgetPaneActiveBtnAddPerson($pane),
        'dialpad': new PhoneWidgetPaneActiveBtnDialpad($pane),
        'mute': new PhoneWidgetPaneActiveBtnMute($pane)
    };

    function initActiveControls() {
        buttons.hold.initActive();
        buttons.transfer.initActive();
        buttons.addPerson.initActive();
        buttons.dialpad.initActive();
        buttons.mute.init();
    }

    function initInactiveControls() {
        buttons.hold.initInactive();
        buttons.transfer.initInactive();
        buttons.addPerson.initInactive();
        buttons.dialpad.initInactive();
        buttons.mute.init();
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
            duration,
            projectName,
            sourceName,
            contact = {
                name
            }
        }
     */
    function load(data) {
        if (data.typeId === 3) {
            // initInactiveControls();
            data.activeControls = false;
        } else {
            // initActiveControls();
            data.activeControls = true;
        }

        ReactDOM.render(
            React.createElement(ActivePane, data),
            document.getElementById('call-pane-calling')
        );

        contactInfo.load(data.contact);
        state.callId = data.callId;
    }

    function setCallId(callId) {
        $pane.attr('data-call-id', callId);
    }

    function getCallId() {
        return parseInt($pane.attr('data-call-id'));
    }

    function removeCallId() {
        return $pane.attr('data-call-id', '');
    }

    function show() {
        contactInfo.hide();
        dialpad.hide();

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $pane.addClass('is_active');
        $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', true);
    }

    function hide() {
        $pane.removeClass('is_active');
        removeCallInProgressIndicator();
    }

    function removeCallInProgressIndicator() {
        $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', false);
    }

    function isActive() {
        return $pane.hasClass('is_active');
    }

    function init(data) {
        load(data);
        show();
        if (data.holdDuration) {
            widgetIcon.update({type: 'hold', timer: true, 'timerStamp': data.holdDuration, text: 'on hold', currentCalls: null, status: 'online'});
            return;
        }
        widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': data.duration, text: 'on call', currentCalls: '', status: 'online'});
    }

    return {
        buttons: buttons,
        init: init,
        load: load,
        show: show,
        hide: hide,
        getCallId: getCallId,
        removeCallId: removeCallId,
        isActive: isActive,
        removeCallInProgressIndicator: removeCallInProgressIndicator
    }

}();
