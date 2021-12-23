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

    function Switcher() {
        this.icon = '.phone-widget-icon__warning';
        this.heading = '.phone-widget-heading__warning';

        this.ready = function () {
            $(this.icon).css('display', 'none');
            $(this.heading).css('display', 'none');
            $(this.heading).attr('data-content', '');
        }

        this.notReady = function (reason) {
            $(this.icon).css('display', 'block');
            if (!reason) {
                reason = '';
            }
            $(this.heading).attr('data-content', reason);
            $(this.heading).css('display', 'block');
        }
    }

    function Panel(isDevicePage) {
        this.isDevicePage = isDevicePage;
        this.isPhoneConnected = false;
        this.isTwilioOk = false;

        this.phoneConnected = function () {
            this.isPhoneConnected = true;
            this.twilioStatusShow();
            $('.tab-device-status').html('Phone: <span style="color: #00a35b">Connected');
            $('.call-pane').removeClass('pw-start').removeClass('pw-connecting');
            $('.phone-widget').removeClass('start');
            $('.phone-widget__start').css('display', 'none');
            $('.phone-device-status').removeClass('fa-square-o').addClass('fa-check-square-o');
            $('.calling-from-info').css('display', 'flex');
            $('.call-pane__number').css('display', 'block');
            $('.call-pane__dial-block').css('display', 'block');
            PhoneWidget.openCallTab();
        };

        this.phoneDisconnected = function () {
           this.isPhoneConnected = false;
           this.twilioStatusHide();
           $('.tab-device-status').html('Phone: <span style="color: #761c19">Disconnected');
           $('.call-pane').addClass('pw-start').addClass('pw-connecting');
           $('.phone-widget').addClass('start');
           $('.phone-widget__start').css('display', 'flex');
           $('.phone-device-status').addClass('fa-square-o').removeClass('fa-check-square-o');
           $('.calling-from-info').css('display', 'none');
           $('.call-pane__number').css('display', 'none');
           $('.call-pane__dial-block').css('display', 'none');
           if (!this.isDevicePage) {
               $('.phone-widget__start-btn').css('display', 'block');
               $('.phone-widget__start-subtitle').html('Connect to start call session');
           }
        };

        this.twilioStatusShow = function () {
            $('.tab-device-status-twilio').css('display', 'inline');
            if (this.isTwilioOk) {
                this.microphoneStatusShow();
                this.speakerStatusShow();
            }
        };

        this.twilioStatusHide = function () {
            $('.tab-device-status-twilio').css('display', 'none');
            this.microphoneStatusHide();
            this.speakerStatusHide();
        };

        this.twilioOk = function () {
            this.isTwilioOk = true;
            $('.tab-device-status-twilio').html('Twilio: <span style="color: #00a35b">OK');
            $('.phone-device-twilio-status').removeClass('fa-square-o').addClass('fa-check-square-o');
            if (this.isPhoneConnected) {
                this.twilioStatusShow();
            }
        };

        this.twilioError = function () {
            this.isTwilioOk = false;
            $('.tab-device-status-twilio').html('Twilio: <span style="color: #761c19">Error');
            $('.phone-device-twilio-status').addClass('fa-square-o').removeClass('fa-check-square-o');
            this.microphoneStatusHide();
            this.speakerStatusHide();
        };

        this.speakerStatusShow = function () {
            $('.tab-device-status-speaker').css('display', 'inline');
        };

        this.speakerStatusHide = function () {
            $('.tab-device-status-speaker').css('display', 'none');
        };

        this.speakerOk = function () {
            $('.tab-device-status-speaker').html('Speaker: <span style="color: #00a35b">OK');
            $('.phone-device-speaker-status').removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.speakerError = function () {
            $('.tab-device-status-speaker').html('Speaker: <span style="color: #761c19">Error');
            $('.phone-device-speaker-status').addClass('fa-square-o').removeClass('fa-check-square-o');
        };

        this.microphoneStatusShow = function () {
            $('.tab-device-status-microphone').css('display', 'inline');
        };

        this.microphoneStatusHide = function () {
            $('.tab-device-status-microphone').css('display', 'none');
        };

        this.microphoneOk = function () {
            $('.tab-device-status-microphone').html('Microphone: <span style="color: #00a35b">OK');
            $('.phone-device-microphone-status').removeClass('fa-square-o').addClass('fa-check-square-o');
        };

        this.microphoneError = function () {
            $('.tab-device-status-microphone').html('Microphone: <span style="color: #761c19">Error');
            $('.phone-device-microphone-status').addClass('fa-square-o').removeClass('fa-check-square-o');
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

    function State(userId, register, phoneDeviceIdStorageKey, panel, phoneConnected, twilioIsReady, speakerIsReady, microphoneIsReady, logger) {
        this.userId = userId;
        this.isPhoneConnected = false;
        this.isTwilioRegistered = false;
        this.isSpeakerSelected = false;
        this.isMicrophoneSelected = false;
        this.register = register;
        this.logger = logger;
        this.switcher = new Switcher()
        this.panel = panel;
        this.statusReady = true;
        this.phoneDeviceIdStorageKey = phoneDeviceIdStorageKey;
        this.isInitiated = true;

        this.reset = function (reason) {
            this.switcher.notReady('Reset devices. ' + reason);
            this.logger.error('Reset devices! ' + reason);

            this.microphoneUnselected();
            this.speakerUnselected();
            this.twilioUnregister();
        };

        this.ready = function () {
            this.statusReady = true;
            this.switcher.ready();
            this.logger.success('Phone Device Ready!');
            console.log('Phone Device Ready!');
        };

        this.notReady = function (reason) {
            this.switcher.notReady(reason);
            if (this.statusReady === true) {
                this.logger.error('Phone Device Not Ready! ' + reason);
                console.log('Phone Device Not Ready! ' + reason);
            }
            this.statusReady = false;
        };

        this.isReady = function () {
            return this.isPhoneConnected && this.isTwilioRegistered && this.isSpeakerSelected && this.isMicrophoneSelected;
        };

        this.phoneConnected = function () {
            this.isPhoneConnected = true;
            this.register.phoneConnected();
            this.panel.phoneConnected();
            this.logger.success('Phone Device connected!');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.phoneDisconnected = function (reason) {
            this.isPhoneConnected = false;
            this.register.phoneDisconnected();
            this.panel.phoneDisconnected();
            this.logger.error('Phone Device disconnected! ' + reason);
            this.notReady('Phone Device: disconnected');
        }

        this.twilioRegister = function () {
            this.isTwilioRegistered = true;
            this.register.twilioReady();
            this.panel.twilioOk();
            this.logger.success('Twilio Device registered!');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.twilioUnregister = function () {
            this.isTwilioRegistered = false;
            this.register.twilioNotReady();
            this.panel.twilioError();
            this.logger.error('Twilio Device unregistered!');
            this.notReady('Twilio Device: unregistered');
        }

        this.speakerSelected = function () {
            this.isSpeakerSelected = true;
            this.register.speakerReady()
            this.panel.speakerOk();
            this.logger.success('Speaker Selected!');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.speakerUnselected = function () {
            this.isSpeakerSelected = false;
            this.register.speakerNotReady()
            this.panel.speakerError();
            this.logger.error('Speaker UnSelected!');
            this.notReady('Speaker: unselected');
        }

        this.microphoneSelected = function () {
            this.isMicrophoneSelected = true;
            this.register.microphoneReady();
            this.panel.microphoneOk();
            this.logger.success('Microphone Selected!');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.microphoneUnselected = function () {
            this.isMicrophoneSelected = false;
            this.register.microphoneNotReady();
            this.panel.microphoneError();
            this.logger.error('Microphone UnSelected!');
            this.notReady('Microphone unselected');
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

        this.notReady('Init devices');

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

        if (twilioIsReady === true) {
            this.twilioRegister();
        } else {
            this.twilioUnregister();
        }

        if (phoneConnected === true) {
            this.phoneConnected();
        } else {
            this.phoneDisconnected('Init.');
        }
    }

    function Init(userId, isDevicePage, phoneDeviceIdStorageKey, logger) {
        if (isDevicePage) {
            return new State(
                userId,
                new Register(userId, logger),
                phoneDeviceIdStorageKey,
                new Panel(true),
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
            new Panel(false),
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
