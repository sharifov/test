(function () {
    const PhoneStatus = {
        connected: 'Connected',
        disconnected: 'Disconnected'
    }

    function Phone(storage) {
        this.status = PhoneStatus.disconnected;
        this.storage = storage;

        this.isReady = function () {
            return this.status === PhoneStatus.connected;
        };

        this.connected = function () {
            this.status = PhoneStatus.connected;
            this.storage.phoneConnected();
        };

        this.disconnected = function () {
            this.status = PhoneStatus.disconnected;
            this.storage.phoneDisconnected();
        };

        this.reset = function () {
            this.disconnected();
        };
    }

    const TwilioStatus = {
        unknown: 'Unknown',
        registered: 'Registered',
        error: 'Error'
    }

    function Twilio(storage) {
        this.status = TwilioStatus.unknown;
        this.storage = storage;

        this.isReady = function () {
            return this.status === TwilioStatus.registered;
        };

        this.unknown = function () {
            this.status = TwilioStatus.unknown;
            this.storage.twilioUnknown();
        };

        this.registered = function () {
            this.status = TwilioStatus.registered;
            this.storage.twilioRegistered();
        };

        this.error = function () {
            this.status = TwilioStatus.error;
            this.storage.twilioError();
        };

        this.reset = function () {
            this.unknown();
        };
    }

    const SpeakerStatus = {
        unknown: 'Unknown',
        selected: 'Selected',
        error: 'Error'
    }

    function Speaker(storage) {
        this.status = SpeakerStatus.unknown;
        this.storage = storage;

        this.isReady = function () {
            return this.status === SpeakerStatus.selected;
        };

        this.unknown = function () {
            this.status = SpeakerStatus.unknown;
            this.storage.speakerUnknown();
        };

        this.selected = function () {
            this.status = SpeakerStatus.selected;
            this.storage.speakerSelected();
        };

        this.error = function () {
            this.status = SpeakerStatus.error;
            this.storage.speakerError();
        };

        this.reset = function () {
            this.unknown();
        };
    }

    const MicrophoneStatus = {
        unknown: 'Unknown',
        selected: 'Selected',
        error: 'Error'
    }

    function Microphone(storage) {
        this.status = MicrophoneStatus.unknown;
        this.storage = storage;

        this.isReady = function () {
            return this.status === MicrophoneStatus.selected;
        };

        this.unknown = function () {
            this.status = MicrophoneStatus.unknown;
            this.storage.microphoneUnknown();
        };

        this.selected = function () {
            this.status = MicrophoneStatus.selected;
            this.storage.microphoneSelected();
        };

        this.error = function () {
            this.status = MicrophoneStatus.error;
            this.storage.microphoneError();
        };

        this.reset = function () {
            this.unknown();
        };
    }

    const WarningMessages = {
        phoneDisconnected: 'Phone disconnected',
        twilioError: 'Twilio error',
        speakerError: 'Speaker error',
        microphoneError: 'Microphone error'
    };

    function WarningIndicator() {
        this.icon = '.phone-widget-icon__warning';
        this.heading = '.phone-widget-heading__warning';
        this.warnings = [];

        this.ready = function () {
            this.warnings = [];
            this.refresh();
        };

        this.notReady = function (warning) {
            if (!this.warnings.includes(warning)) {
                this.warnings.push(warning || '');
            }
            this.refresh();
        };

        this.refresh = function () {
            if (this.warnings.length === 0) {
                $(this.icon).removeClass('phone-widget-icon__warning-show');
                $(this.heading).css('display', 'none');
                $(this.heading).attr('data-content', '');
                $(this.heading).popover('hide');
                return;
            }

            $(this.icon).addClass('phone-widget-icon__warning-show');
            $(this.heading).css('display', 'block');
            $(this.heading).popover('dispose');
            let warnings = '';
            if (this.warnings.includes(WarningMessages.phoneDisconnected)) {
                warnings = WarningMessages.phoneDisconnected;
            } else if (this.warnings.includes(WarningMessages.twilioError)) {
                warnings = WarningMessages.twilioError;
            } else {
                warnings = this.warnings.join("<br>");
            }
            $(this.heading).popover({
                content: warnings,
                html: true
            });
        };

        this.remove = function (warning) {
            const index = this.warnings.indexOf(warning);
            if (index > -1) {
                this.warnings.splice(index, 1);
                this.refresh();
            }
        };
    }

    function VoipPage() {
        this.phone = $('.phone-device-status');
        this.twilio = $('.phone-device-twilio-status');
        this.speaker = $('.phone-device-speaker-status');
        this.microphone = $('.phone-device-microphone-status');

        this.phoneConnected = function () {
            this.phone.removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.phoneDisconnected = function () {
            this.phone.addClass('fa-square-o').removeClass('fa-check-square-o');
        };

        this.twilioReady = function () {
            this.twilio.removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.twilioNotReady = function () {
            this.twilio.addClass('fa-square-o').removeClass('fa-check-square-o');
        };

        this.speakerReady = function () {
            this.speaker.removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.speakerNotReady = function () {
            this.speaker.addClass('fa-square-o').removeClass('fa-check-square-o');
        };

        this.microphoneReady = function () {
            this.microphone.removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.microphoneNotReady = function () {
            this.microphone.addClass('fa-square-o').removeClass('fa-check-square-o');
        };
    }

    function StatusPanel() {
        this.isPhoneConnected = false;
        this.isTwilioRegistered = false;
        this.phone = $('.tab-device-status');
        this.twilio = $('.tab-device-status-twilio');
        this.speaker = $('.tab-device-status-speaker');
        this.microphone = $('.tab-device-status-microphone');

        this.phoneConnected = function () {
            this.isPhoneConnected = true;
            this.phone.html('Phone: <span style="color: #00a35b">Connected');
            this.twilioStatusShow();
        };

        this.phoneDisconnected = function () {
            this.isPhoneConnected = false;
            this.phone.html('Phone: <span style="color: #761c19">Disconnected');
            this.twilioStatusHide();
        };

        this.twilioStatusShow = function () {
            this.twilio.css('display', 'inline');
            if (this.isTwilioRegistered) {
                this.microphoneStatusShow();
                this.speakerStatusShow();
            }
        };

        this.twilioStatusHide = function () {
            this.twilio.css('display', 'none');
            this.microphoneStatusHide();
            this.speakerStatusHide();
        };

        this.twilioUnknown = function () {
            this.isTwilioRegistered = false;
            this.twilio.html('Twilio: <span style="color: #000">Unknown');
            this.microphoneStatusHide();
            this.speakerStatusHide();
        };

        this.twilioRegistered = function () {
            this.isTwilioRegistered = true;
            this.twilio.html('Twilio: <span style="color: #00a35b">Registered');
            if (this.isPhoneConnected) {
                this.twilioStatusShow();
            }
        };

        this.twilioError = function () {
            this.isTwilioRegistered = false;
            this.twilio.html('Twilio: <span style="color: #761c19">Error');
            this.microphoneStatusHide();
            this.speakerStatusHide();
        };

        this.speakerStatusShow = function () {
            this.speaker.css('display', 'inline');
        };

        this.speakerStatusHide = function () {
            this.speaker.css('display', 'none');
        };

        this.speakerUnknown = function () {
            this.speaker.html('Speaker: <span style="color: #000">Unknown');
        };

        this.speakerSelected = function () {
            this.speaker.html('Speaker: <span style="color: #00a35b">Selected');
        };

        this.speakerError = function () {
            this.speaker.html('Speaker: <span style="color: #761c19">Error');
        };

        this.microphoneStatusShow = function () {
            this.microphone.css('display', 'inline');
        };

        this.microphoneStatusHide = function () {
            this.microphone.css('display', 'none');
        };

        this.microphoneUnknown = function () {
            this.microphone.html('Microphone: <span style="color: #000">Unknown');
        };

        this.microphoneSelected = function () {
            this.microphone.html('Microphone: <span style="color: #00a35b">Selected');
        };

        this.microphoneError = function () {
            this.microphone.html('Microphone: <span style="color: #761c19">Error');
        };
    }

    function ConnectingPanel() {
        this.show = function() {
            $('.call-pane').addClass('pw-start').addClass('pw-connecting');
            $('.phone-widget').addClass('start');
            $('.phone-widget__start').css('display', 'flex');
            $('.calling-from-info').css('display', 'none');
            $('.call-pane__number').css('display', 'none');
            $('.call-pane__dial-block').css('display', 'none');
        };

        this.hide = function () {
            $('.call-pane').removeClass('pw-start').removeClass('pw-connecting');
            $('.phone-widget').removeClass('start');
            $('.phone-widget__start').css('display', 'none');
            $('.calling-from-info').css('display', 'flex');
            $('.call-pane__number').css('display', 'block');
            $('.call-pane__dial-block').css('display', 'block');
        };
    }

    function View(warningIndicator) {
        this.warningIndicator = warningIndicator;
        this.voipPage = new VoipPage();
        this.statusPanel = new StatusPanel();
        this.connectingPanel = new ConnectingPanel();

        this.phoneConnected = function () {
            this.statusPanel.phoneConnected();
            this.voipPage.phoneConnected();
            this.connectingPanel.hide();
            this.warningIndicator.remove(WarningMessages.phoneDisconnected);
            PhoneWidget.openCallTab();
        };

        this.phoneDisconnected = function () {
            this.statusPanel.phoneDisconnected();
            this.voipPage.phoneDisconnected();
            this.connectingPanel.show();
            this.warningIndicator.notReady(WarningMessages.phoneDisconnected);
        };

        this.twilioUnknown = function () {
            this.statusPanel.twilioUnknown();
            this.voipPage.twilioNotReady();
            this.warningIndicator.notReady(WarningMessages.twilioError);
        };

        this.twilioRegistered = function () {
            this.statusPanel.twilioRegistered();
            this.voipPage.twilioReady();
            this.warningIndicator.remove(WarningMessages.twilioError);
        };

        this.twilioError = function () {
            this.statusPanel.twilioError();
            this.voipPage.twilioNotReady();
            this.warningIndicator.notReady(WarningMessages.twilioError);
        };

        this.speakerUnknown = function () {
            this.statusPanel.speakerUnknown();
            this.voipPage.speakerNotReady();
            this.warningIndicator.notReady(WarningMessages.speakerError);
        };

        this.speakerSelected = function () {
            this.statusPanel.speakerSelected();
            this.voipPage.speakerReady();
            this.warningIndicator.remove(WarningMessages.speakerError);
        };

        this.speakerError = function () {
            this.statusPanel.speakerError();
            this.voipPage.speakerNotReady();
            this.warningIndicator.notReady(WarningMessages.speakerError);
        };

        this.microphoneUnknown = function () {
            this.statusPanel.microphoneUnknown();
            this.voipPage.microphoneNotReady();
            this.warningIndicator.notReady(WarningMessages.microphoneError);
        };

        this.microphoneSelected = function () {
            this.statusPanel.microphoneSelected();
            this.voipPage.microphoneReady();
            this.warningIndicator.remove(WarningMessages.microphoneError);
        };

        this.microphoneError = function () {
            this.statusPanel.microphoneError();
            this.voipPage.microphoneNotReady();
            this.warningIndicator.notReady(WarningMessages.microphoneError);
        };
    }

    function Storage(initUserId, initLogger) {
        const userId = initUserId;
        const logger = initLogger;

        function Queue() {
            this.actions = [];
            this.enqueue = function (action) {
                this.actions.push(action);
            };
            this.dequeue = function () {
                if (this.actions.length > 0) {
                    return this.actions.shift();
                }
                return null;
            };
        }

        const queue = new Queue();

        setInterval(() => {
            let action = queue.dequeue();
            if (action === null) {
                return;
            }
            let deviceId = PhoneWidget.getDeviceState().getDeviceId();
            if (deviceId === null) {
                logger.add({
                    name: 'Change device status. Action: ' + action,
                    message: 'Device ID must be set!'
                });
                return;
            }
            socketSend('PhoneDeviceReady', action, {
                'userId': userId,
                'deviceId': deviceId
            });
        }, 200);

        this.phoneConnected = function () {
        };

        this.phoneDisconnected = function () {
        };

        this.twilioUnknown = function () {
            queue.enqueue('TwilioNotReady');
        };

        this.twilioRegistered = function () {
            queue.enqueue('TwilioReady');
        };

        this.twilioError = function () {
            queue.enqueue('TwilioNotReady');
        };

        this.speakerUnknown = function () {
            queue.enqueue('SpeakerNotReady');
        };

        this.speakerSelected = function () {
            queue.enqueue('SpeakerReady');
        };

        this.speakerError = function () {
            queue.enqueue('SpeakerNotReady');
        };

        this.microphoneUnknown = function () {
            queue.enqueue('MicrophoneNotReady');
        };

        this.microphoneSelected = function () {
            queue.enqueue('MicrophoneReady');
        };

        this.microphoneError = function () {
            queue.enqueue('MicrophoneNotReady');
        };
    }

    function State(userId, storage, view, warningIndicator, phoneStatus, twilioStatus, speakerStatus, microphoneStatus, logger) {
        this.userId = userId;
        this.phone = new Phone(storage);
        this.twilio = new Twilio(storage);
        this.speaker = new Speaker(storage);
        this.microphone = new Microphone(storage);
        this.logger = logger;
        this.view = view;
        this.warningIndicator = warningIndicator;
        this.isInitiated = true;
        this.deviceId = null;

        this.resetDevices = function (reason) {
            this.logger.error('Reset devices. (' + reason + ')');
            this.twilioUnknown();
            this.speakerUnknown();
            this.microphoneUnknown();
        };

        this.ready = function () {
            this.warningIndicator.ready();
            this.logger.success('Phone Device Ready.');
            console.log('Phone Device Ready.');
        };

        this.isReady = function () {
            return this.phone.isReady() && this.twilio.isReady() && this.speaker.isReady() && this.microphone.isReady();
        };

        this.phoneConnected = function (deviceId, devices) {
            PhoneWidget.audio.incoming.addDevice(deviceId, devices);
            this.setDeviceId(deviceId);
            this.phone.connected();
            this.view.phoneConnected();
            this.logger.success('Phone Device connected.');
            if (this.isReady()) {
                this.ready();
            }
            widgetIcon.connect();
        };

        this.phoneDisconnected = function (reason) {
            PhoneWidget.audio.incoming.removeDevice(this.deviceId);
            this.removeDeviceId();
            this.phone.disconnected();
            this.view.phoneDisconnected();
            this.logger.error('Phone Device disconnected. (' + reason + ')');
            widgetIcon.disconnect();
        };

        this.twilioUnknown = function () {
            this.twilio.unknown();
            this.view.twilioUnknown();
            this.logger.error('Twilio Device unknown.');
        };

        this.twilioRegistered = function () {
            this.twilio.registered();
            this.view.twilioRegistered();
            this.logger.success('Twilio Device registered.');
            if (this.isReady()) {
                this.ready();
            }
        };

        this.twilioError = function () {
            this.twilio.error();
            this.view.twilioError();
            this.logger.error('Twilio Device error.');
        };

        this.speakerUnknown = function () {
            this.speaker.unknown();
            this.view.speakerUnknown();
            this.logger.error('Speaker Unknown.');
        };

        this.speakerSelected = function () {
            this.speaker.selected();
            this.view.speakerSelected();
            this.logger.success('Speaker Selected.');
            if (this.isReady()) {
                this.ready();
            }
        };

        this.speakerError = function () {
            this.speaker.error();
            this.view.speakerError();
            this.logger.error('Speaker Error.');
        };

        this.microphoneUnknown = function () {
            this.microphone.unknown();
            this.view.microphoneUnknown();
            this.logger.error('Microphone Unknown.');
        };

        this.microphoneSelected = function () {
            this.microphone.selected();
            this.view.microphoneSelected();
            this.logger.success('Microphone Selected.');
            if (this.isReady()) {
                this.ready();
            }
        };

        this.microphoneError = function () {
            this.microphone.error();
            this.view.microphoneError();
            this.logger.error('Microphone Error.');
        };

        this.setDeviceId = function (deviceId) {
            this.deviceId = deviceId;
            this.logger.success('Device ID was installed.');
        };

        this.getDeviceId = function () {
            return this.deviceId;
        };

        this.removeDeviceId = function () {
            this.deviceId = null;
            this.logger.error('Device ID was removed.');
        };

        if (phoneStatus === PhoneStatus.connected) {
            this.phoneConnected();
            if (twilioStatus === TwilioStatus.registered) {
                this.twilioRegistered();
                if (speakerStatus === SpeakerStatus.selected) {
                    this.speakerSelected();
                } else if (speakerStatus === SpeakerStatus.error) {
                    this.speakerError();
                } else {
                    this.speakerUnknown();
                }
                if (microphoneStatus === MicrophoneStatus.selected) {
                    this.microphoneSelected();
                } else  if (microphoneStatus === MicrophoneStatus.error) {
                    this.microphoneError();
                } else {
                    this.microphoneUnknown();
                }
                return;
            }
            if (twilioStatus === TwilioStatus.error) {
                this.twilioError();
                return;
            }
            this.twilioUnknown();
            return;
        }

        this.phone.disconnected();
        this.view.phoneDisconnected();
        this.logger.add('Waiting to install Phone Device ID.');
        widgetIcon.disconnect();
    }

    function Init(userId, logger) {
        const warningIndicator = new WarningIndicator();
        return new State(
            userId,
            new Storage(userId, logger),
            new View(warningIndicator),
            warningIndicator,
            PhoneStatus.disconnected,
            TwilioStatus.unknown,
            SpeakerStatus.unknown,
            MicrophoneStatus.unknown,
            logger
        );
    }

    window.phoneWidget.device.state.initialize = {
        Init: Init
    }
})();
