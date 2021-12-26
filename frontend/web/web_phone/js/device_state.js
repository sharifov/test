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
            this.storage.phoneConnected(this.status);
        };

        this.disconnected = function () {
            this.status = PhoneStatus.disconnected;
            this.storage.phoneDisconnected(this.status);
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
            this.storage.twilioUnknown(this.status);
        };

        this.registered = function () {
            this.status = TwilioStatus.registered;
            this.storage.twilioRegistered(this.status);
        };

        this.error = function () {
            this.status = TwilioStatus.error;
            this.storage.twilioError(this.status);
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
            this.storage.speakerUnknown(this.status);
        };

        this.selected = function () {
            this.status = SpeakerStatus.selected;
            this.storage.speakerSelected(this.status);
        };

        this.error = function () {
            this.status = SpeakerStatus.error;
            this.storage.speakerError(this.status);
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
            this.storage.microphoneUnknown(this.status);
        };

        this.selected = function () {
            this.status = MicrophoneStatus.selected;
            this.storage.microphoneSelected(this.status);
        };

        this.error = function () {
            this.status = MicrophoneStatus.error;
            this.storage.microphoneError(this.status);
        };

        this.reset = function () {
            this.unknown();
        };
    }

    function getStorageNamePhoneStatus(userId) {
        return 'PhoneDeviceStatus' + userId;
    }

    function getStorageNameTwilioStatus(userId) {
        return 'PhoneDeviceTwilioStatus' + userId;
    }

    function getStorageNameSpeakerStatus(userId) {
        return 'PhoneDeviceSpeakerStatus' + userId;
    }

    function getStorageNameMicrophoneStatus(userId) {
        return 'PhoneDeviceMicrophoneStatus' + userId;
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
                $(this.icon).css('display', 'none');
                $(this.heading).css('display', 'none');
                $(this.heading).attr('data-content', '');
                $(this.heading).popover('hide');
                return;
            }

            $(this.icon).css('display', 'block');
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
        this.show = function(isDevicePage) {
            $('.call-pane').addClass('pw-start').addClass('pw-connecting');
            $('.phone-widget').addClass('start');
            $('.phone-widget__start').css('display', 'flex');
            $('.calling-from-info').css('display', 'none');
            $('.call-pane__number').css('display', 'none');
            $('.call-pane__dial-block').css('display', 'none');
            if (!isDevicePage) {
                $('.phone-widget__start-btn').css('display', 'block');
                $('.phone-widget__start-subtitle').html('Connect to start call session');
            }
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

    function View(isDevicePage, warningIndicator) {
        this.isDevicePage = isDevicePage;
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
            this.connectingPanel.show(this.isDevicePage);
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

        this.phoneConnected = function (status) {
            localStorage.setItem(getStorageNamePhoneStatus(userId), status);
        };

        this.phoneDisconnected = function (status) {
            localStorage.setItem(getStorageNamePhoneStatus(userId), status);
        };

        this.twilioUnknown = function (status) {
            localStorage.setItem(getStorageNameTwilioStatus(userId), status);
            queue.enqueue('TwilioNotReady');
        };

        this.twilioRegistered = function (status) {
            localStorage.setItem(getStorageNameTwilioStatus(userId), status);
            queue.enqueue('TwilioReady');
        };

        this.twilioError = function (status) {
            localStorage.setItem(getStorageNameTwilioStatus(userId), status);
            queue.enqueue('TwilioNotReady');
        };

        this.speakerUnknown = function (status) {
            localStorage.setItem(getStorageNameSpeakerStatus(userId), status);
            queue.enqueue('SpeakerNotReady');
        };

        this.speakerSelected = function (status) {
            localStorage.setItem(getStorageNameSpeakerStatus(userId), status);
            queue.enqueue('SpeakerReady');
        };

        this.speakerError = function (status) {
            localStorage.setItem(getStorageNameSpeakerStatus(userId), status);
            queue.enqueue('SpeakerNotReady');
        };

        this.microphoneUnknown = function (status) {
            localStorage.setItem(getStorageNameMicrophoneStatus(userId), status);
            queue.enqueue('MicrophoneNotReady');
        };

        this.microphoneSelected = function (status) {
            localStorage.setItem(getStorageNameMicrophoneStatus(userId), status);
            queue.enqueue('MicrophoneReady');
        };

        this.microphoneError = function (status) {
            localStorage.setItem(getStorageNameMicrophoneStatus(userId), status);
            queue.enqueue('MicrophoneNotReady');
        };
    }

    function DummyStorage() {
        this.phoneConnected = function () {}
        this.phoneDisconnected = function () {}
        this.twilioUnknown = function (status) {}
        this.twilioRegistered = function (status) {}
        this.twilioError = function (status) {}
        this.speakerUnknown = function (status) {}
        this.speakerSelected = function (status) {}
        this.speakerError = function (status) {}
        this.microphoneUnknown = function (status) {}
        this.microphoneSelected = function (status) {}
        this.microphoneError = function (status) {}
    }

    function State(userId, storage, phoneDeviceIdStorageKey, view, warningIndicator, phoneStatus, twilioStatus, speakerStatus, microphoneStatus, logger) {
        this.userId = userId;
        this.phone = new Phone(storage);
        this.twilio = new Twilio(storage);
        this.speaker = new Speaker(storage);
        this.microphone = new Microphone(storage);
        this.logger = logger;
        this.view = view;
        this.warningIndicator = warningIndicator;
        this.phoneDeviceIdStorageKey = phoneDeviceIdStorageKey;
        this.isInitiated = true;

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

        this.phoneConnected = function () {
            this.phone.connected();
            this.view.phoneConnected();
            this.logger.success('Phone Device connected.');
            if (this.isReady()) {
                this.ready();
            }
            widgetIcon.connect();
        };

        this.phoneDisconnected = function (reason) {
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
            localStorage.setItem(this.phoneDeviceIdStorageKey, deviceId);
            this.logger.success('Device ID was installed.');
        };

        this.getDeviceId = function () {
            return localStorage.getItem(this.phoneDeviceIdStorageKey) || null;
        };

        this.removeDeviceId = function () {
            localStorage.removeItem(this.phoneDeviceIdStorageKey);
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

    function Init(userId, isDevicePage, phoneDeviceIdStorageKey, logger) {
        const warningIndicator = new WarningIndicator();
        if (isDevicePage) {
            return new State(
                userId,
                new Storage(userId, logger),
                phoneDeviceIdStorageKey,
                new View(true, warningIndicator),
                warningIndicator,
                PhoneStatus.disconnected,
                TwilioStatus.unknown,
                SpeakerStatus.unknown,
                MicrophoneStatus.unknown,
                logger
            );
        }

        window.addEventListener('storage', function (event) {
            if (event.key === 'PhoneWidgetLog' + window.userId) {
                let value = JSON.parse(event.newValue);
                PhoneWidget.addLog(value.message, value.color);
            }
        });

        window.addEventListener('storage', function (event) {
            if (event.key === getStorageNamePhoneStatus(userId)) {
                if (event.newValue === PhoneStatus.connected) {
                    PhoneWidget.getDeviceState().phoneConnected();
                    return;
                }
                PhoneWidget.getDeviceState().phoneDisconnected('');
                return;
            }
            if (event.key === getStorageNameTwilioStatus(userId)) {
                if (event.newValue === TwilioStatus.registered) {
                    PhoneWidget.getDeviceState().twilioRegistered();
                    return;
                }
                if (event.newValue === TwilioStatus.error) {
                    PhoneWidget.getDeviceState().twilioError();
                    return;
                }
                PhoneWidget.getDeviceState().twilioUnknown();
                return;
            }
            if (event.key === getStorageNameSpeakerStatus(userId)) {
                if (event.newValue === SpeakerStatus.selected) {
                    PhoneWidget.getDeviceState().speakerSelected();
                    return;
                }
                if (event.newValue === SpeakerStatus.error) {
                    PhoneWidget.getDeviceState().speakerError();
                    return;
                }
                PhoneWidget.getDeviceState().speakerUnknown();
                return;
            }
            if (event.key === getStorageNameMicrophoneStatus(userId)) {
                if (event.newValue === MicrophoneStatus.selected) {
                    PhoneWidget.getDeviceState().microphoneSelected();
                    return;
                }
                if (event.newValue === MicrophoneStatus.error) {
                    PhoneWidget.getDeviceState().microphoneError();
                    return;
                }
                PhoneWidget.getDeviceState().microphoneUnknown();
                return;
            }
        });

        return new State(
            userId,
            new DummyStorage(),
            phoneDeviceIdStorageKey,
            new View(false, warningIndicator),
            warningIndicator,
            localStorage.getItem(getStorageNamePhoneStatus(userId)),
            localStorage.getItem(getStorageNameTwilioStatus(userId)),
            localStorage.getItem(getStorageNameSpeakerStatus(userId)),
            localStorage.getItem(getStorageNameMicrophoneStatus(userId)),
            new window.phoneWidget.logger.DummyLogger()
        );
    }

    window.phoneWidget.device.state = {
        Init: Init
    }
})();
