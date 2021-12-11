(function () {
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

    function Panel() {
        this.twilioOk = function () {
            $('.tab-device-status-twilio').html('Twilio: <span style="color: #00a35b">OK');
        };

        this.twilioError = function () {
            $('.tab-device-status-twilio').html('Twilio: <span style="color: #761c19">Error');
        };

        this.speakerOk = function () {
            $('.tab-device-status-speaker').html('Speaker: <span style="color: #00a35b">OK');
        };

        this.speakerError = function () {
            $('.tab-device-status-speaker').html('Speaker: <span style="color: #761c19">Error');
        };

        this.microphoneOk = function () {
            $('.tab-device-status-microphone').html('Microphone: <span style="color: #00a35b">OK');
        };

        this.microphoneError = function () {
            $('.tab-device-status-microphone').html('Microphone: <span style="color: #761c19">Error');
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
        this.twilioReady = function () {}
        this.twilioNotReady = function () {}
        this.speakerReady = function () {}
        this.speakerNotReady = function () {}
        this.microphoneReady = function () {}
        this.microphoneNotReady = function () {}
    }

    function State(userId, register, phoneDeviceIdStorageKey, twilioIsReady, speakerIsReady, microphoneIsReady, logger) {
        this.userId = userId;
        this.isTwilioRegistered = false;
        this.isSpeakerSelected = false;
        this.isMicrophoneSelected = false;
        this.register = register;
        this.logger = logger;
        this.switcher = new Switcher()
        this.panel = new Panel();
        this.statusReady = true;
        this.phoneDeviceIdStorageKey = phoneDeviceIdStorageKey;

        this.reset = function (reason) {
            this.switcher.notReady('Reset devices. ' + reason);
            this.logger.error('Reset devices! ' + reason);

            this.twilioUnregister();
            this.speakerUnselected();
            this.microphoneUnselected();
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
            return this.isTwilioRegistered && this.isSpeakerSelected && this.isMicrophoneSelected;
        };

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
    }

    function Init(userId, isDevicePage, phoneDeviceIdStorageKey, logger) {
        if (isDevicePage) {
            return new State(
                userId,
                new Register(userId, logger),
                phoneDeviceIdStorageKey,
                false,
                false,
                false,
                logger
            );
        }

        window.addEventListener('storage', function (event) {
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
