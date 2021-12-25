(function () {
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
        this.reasons = [];

        this.ready = function () {
            this.reasons = [];
            $(this.icon).css('display', 'none');
            $(this.heading).css('display', 'none');
            $(this.heading).attr('data-content', '');
            $(this.heading).popover('hide');
        }

        this.notReady = function (reason) {
            $(this.icon).css('display', 'block');
            if (!reason) {
                reason = '';
            }
            $(this.heading).css('display', 'block');
            this.reasons.push(reason);
            $(this.heading).popover('dispose');
            let reasons = '';
            if (this.reasons.includes(WarningMessages.phoneDisconnected)) {
                reasons = WarningMessages.phoneDisconnected;
            } else if (this.reasons.includes(WarningMessages.twilioError)) {
                reasons = WarningMessages.twilioError;
            } else {
                reasons = this.reasons.join("<br>");
            }
            $(this.heading).popover({
                content: reasons,
                html: true
            });
        }
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

        this.twilioOk = function () {
            this.twilio.removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.twilioError = function () {
            this.twilio.addClass('fa-square-o').removeClass('fa-check-square-o');
        };

        this.speakerOk = function () {
            this.speaker.removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.speakerError = function () {
            this.speaker.addClass('fa-square-o').removeClass('fa-check-square-o');
        };

        this.microphoneOk = function () {
            this.microphone.removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.microphoneError = function () {
            this.microphone.addClass('fa-square-o').removeClass('fa-check-square-o');
        };
    }

    function StatusPanel() {
        this.isPhoneConnected = false;
        this.isTwilioOk = false;
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
            if (this.isTwilioOk) {
                this.microphoneStatusShow();
                this.speakerStatusShow();
            }
        };

        this.twilioStatusHide = function () {
            this.twilio.css('display', 'none');
            this.microphoneStatusHide();
            this.speakerStatusHide();
        };

        this.twilioOk = function () {
            this.isTwilioOk = true;
            this.twilio.html('Twilio: <span style="color: #00a35b">OK');
            if (this.isPhoneConnected) {
                this.twilioStatusShow();
            }
        };

        this.twilioError = function () {
            this.isTwilioOk = false;
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

        this.speakerOk = function () {
            this.speaker.html('Speaker: <span style="color: #00a35b">OK');
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

        this.microphoneOk = function () {
            this.microphone.html('Microphone: <span style="color: #00a35b">OK');
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
            PhoneWidget.openCallTab();
        };

        this.phoneDisconnected = function () {
            this.statusPanel.phoneDisconnected();
            this.voipPage.phoneDisconnected();
            this.connectingPanel.show(this.isDevicePage);
            this.warningIndicator.notReady(WarningMessages.phoneDisconnected);
        };

        this.twilioOk = function () {
            this.statusPanel.twilioOk();
            this.voipPage.twilioOk();
        };

        this.twilioError = function () {
            this.statusPanel.twilioError();
            this.voipPage.twilioError();
            this.warningIndicator.notReady(WarningMessages.twilioError);
        };

        this.speakerOk = function () {
            this.statusPanel.speakerOk();
            this.voipPage.speakerOk();
        };

        this.speakerError = function () {
            this.statusPanel.speakerError();
            this.voipPage.speakerError();
            this.warningIndicator.notReady(WarningMessages.speakerError);
        };

        this.microphoneOk = function () {
            this.statusPanel.microphoneOk();
            this.voipPage.microphoneOk();
        };

        this.microphoneError = function () {
            this.statusPanel.speakerError();
            this.voipPage.microphoneError();
            this.warningIndicator.notReady(WarningMessages.microphoneError);
        };
    }

    function Register(initUserId, initLogger) {
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
            localStorage.setItem(getStorageNamePhoneStatus(userId), 'connected');
        }

        this.phoneDisconnected = function () {
            localStorage.setItem(getStorageNamePhoneStatus(userId), 'disconnected');
        }

        this.twilioReady = function () {
            localStorage.setItem(getStorageNameTwilioStatus(userId), 'ready');
            queue.enqueue('TwilioReady');
        }

        this.twilioNotReady = function () {
            localStorage.setItem(getStorageNameTwilioStatus(userId), 'not-ready');
            queue.enqueue('TwilioNotReady');
        }

        this.speakerReady = function () {
            localStorage.setItem(getStorageNameSpeakerStatus(userId), 'ready');
            queue.enqueue('SpeakerReady');
        }

        this.speakerNotReady = function () {
            localStorage.setItem(getStorageNameSpeakerStatus(userId), 'not-ready');
            queue.enqueue('SpeakerNotReady');
        }

        this.microphoneReady = function () {
            localStorage.setItem(getStorageNameMicrophoneStatus(userId), 'ready');
            queue.enqueue('MicrophoneReady');
        }

        this.microphoneNotReady = function () {
            localStorage.setItem(getStorageNameMicrophoneStatus(userId), 'not-ready');
            queue.enqueue('MicrophoneNotReady');
        }
    }

    function DummyRegister() {
        this.phoneConnected = function () {}
        this.phoneDisconnected = function () {}
        this.twilioReady = function () {}
        this.twilioNotReady = function () {}
        this.speakerReady = function () {}
        this.speakerNotReady = function () {}
        this.microphoneReady = function () {}
        this.microphoneNotReady = function () {}
    }

    function State(userId, register, phoneDeviceIdStorageKey, view, warningIndicator, phoneConnected, twilioIsReady, speakerIsReady, microphoneIsReady, logger) {
        this.userId = userId;
        this.isPhoneConnected = false;
        this.isTwilioRegistered = false;
        this.isSpeakerSelected = false;
        this.isMicrophoneSelected = false;
        this.register = register;
        this.logger = logger;
        this.view = view;
        this.warningIndicator = warningIndicator;
        this.phoneDeviceIdStorageKey = phoneDeviceIdStorageKey;
        this.isInitiated = true;

        this.reset = function (reason) {
            this.logger.error('Reset devices. (' + reason + ')');
            this.twilioUnregister();
            this.microphoneUnselected();
            this.speakerUnselected();
        };

        this.ready = function () {
            this.warningIndicator.ready();
            this.logger.success('Phone Device Ready.');
            console.log('Phone Device Ready.');
        };

        this.isReady = function () {
            return this.isPhoneConnected && this.isTwilioRegistered && this.isSpeakerSelected && this.isMicrophoneSelected;
        };

        this.phoneConnected = function () {
            this.isPhoneConnected = true;
            this.register.phoneConnected();
            this.view.phoneConnected();
            this.logger.success('Phone Device connected.');
            if (this.isReady()) {
                this.ready();
            }
            widgetIcon.connect();
        }

        this.phoneDisconnected = function (reason) {
            this.isPhoneConnected = false;
            this.register.phoneDisconnected();
            this.view.phoneDisconnected();
            this.logger.error('Phone Device disconnected. (' + reason + ')');
            widgetIcon.disconnect();
        }

        this.twilioRegister = function () {
            this.isTwilioRegistered = true;
            this.register.twilioReady();
            this.view.twilioOk();
            this.logger.success('Twilio Device registered.');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.twilioUnregister = function () {
            this.isTwilioRegistered = false;
            this.register.twilioNotReady();
            this.view.twilioError();
            this.logger.error('Twilio Device unregistered.');
        }

        this.speakerSelected = function () {
            this.isSpeakerSelected = true;
            this.register.speakerReady()
            this.view.speakerOk();
            this.logger.success('Speaker Selected.');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.speakerUnselected = function () {
            this.isSpeakerSelected = false;
            this.register.speakerNotReady()
            this.view.speakerError();
            this.logger.error('Speaker UnSelected.');
        }

        this.microphoneSelected = function () {
            this.isMicrophoneSelected = true;
            this.register.microphoneReady();
            this.view.microphoneOk();
            this.logger.success('Microphone Selected.');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.microphoneUnselected = function () {
            this.isMicrophoneSelected = false;
            this.register.microphoneNotReady();
            this.view.microphoneError();
            this.logger.error('Microphone UnSelected.');
        }

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

        if (phoneConnected === true) {
            this.phoneConnected();
            if (twilioIsReady === true) {
                this.twilioRegister();
                if (microphoneIsReady === true) {
                    this.microphoneSelected();
                } else {
                    this.microphoneUnselected();
                }
                if (speakerIsReady === true) {
                    this.speakerSelected();
                } else {
                    this.speakerUnselected();
                }
            } else {
                this.twilioUnregister();
            }
        } else {
            this.register.phoneDisconnected();
            this.view.phoneDisconnected();
            this.logger.add('Waiting to install Phone Device ID.');
            widgetIcon.disconnect();
        }
    }

    function Init(userId, isDevicePage, phoneDeviceIdStorageKey, logger) {
        const warningIndicator = new WarningIndicator();
        if (isDevicePage) {
            return new State(
                userId,
                new Register(userId, logger),
                phoneDeviceIdStorageKey,
                new View(true, warningIndicator),
                warningIndicator,
                false,
                false,
                false,
                false,
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
                if (event.newValue === 'connected') {
                    PhoneWidget.getDeviceState().phoneConnected();
                    return;
                }
                PhoneWidget.getDeviceState().phoneDisconnected('');
                return;
            }
            if (event.key === getStorageNameTwilioStatus(userId)) {
                if (event.newValue === 'ready') {
                    PhoneWidget.getDeviceState().twilioRegister();
                    return;
                }
                PhoneWidget.getDeviceState().twilioUnregister();
                return;
            }
            if (event.key === getStorageNameSpeakerStatus(userId)) {
                if (event.newValue === 'ready') {
                    PhoneWidget.getDeviceState().speakerSelected();
                    return;
                }
                PhoneWidget.getDeviceState().speakerUnselected();
                return;
            }
            if (event.key === getStorageNameMicrophoneStatus(userId)) {
                if (event.newValue === 'ready') {
                    PhoneWidget.getDeviceState().microphoneSelected();
                    return;
                }
                PhoneWidget.getDeviceState().microphoneUnselected();
            }
        });

        return new State(
            userId,
            new DummyRegister(),
            phoneDeviceIdStorageKey,
            new View(false, warningIndicator),
            warningIndicator,
            localStorage.getItem(getStorageNamePhoneStatus(userId)) === 'connected',
            localStorage.getItem(getStorageNameTwilioStatus(userId)) === 'ready',
            localStorage.getItem(getStorageNameSpeakerStatus(userId)) === 'ready',
            localStorage.getItem(getStorageNameMicrophoneStatus(userId)) === 'ready',
            new window.phoneWidget.logger.DummyLogger()
        );
    }

    window.phoneWidget.device.state = {
        Init: Init
    }
})();
