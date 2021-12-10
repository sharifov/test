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

    function Register(userId) {
        this.userId = userId;

        this.twilioReady = function () {
            localStorage.setItem(getStorageNameTwilioStatus(this.userId), 'ready');
        }

        this.twilioNotReady = function () {
            localStorage.setItem(getStorageNameTwilioStatus(this.userId), 'not-ready');
        }

        this.speakerReady = function () {
            localStorage.setItem(getStorageNameSpeakerStatus(this.userId), 'ready');
        }

        this.speakerNotReady = function () {
            localStorage.setItem(getStorageNameSpeakerStatus(this.userId), 'not-ready');
        }

        this.microphoneReady = function () {
            localStorage.setItem(getStorageNameMicrophoneStatus(this.userId), 'ready');
        }

        this.microphoneNotReady = function () {
            localStorage.setItem(getStorageNameMicrophoneStatus(this.userId), 'not-ready');
        }
    }

    function DummyRegister(userId) {
        this.twilioReady = function () {}
        this.twilioNotReady = function () {}
        this.speakerReady = function () {}
        this.speakerNotReady = function () {}
        this.microphoneReady = function () {}
        this.microphoneNotReady = function () {}
    }

    function State(userId, register, logger, twilioIsReady, speakerIsReady, microphoneIsReady) {
        this.userId = userId;
        this.isTwilioRegistered = false;
        this.isSpeakerSelected = false;
        this.isMicrophoneSelected = false;
        this.register = register;
        this.logger = logger;
        this.switcher = new Switcher()
        this.panel = new Panel();
        this.statusReady = true;

        this.reset = function () {
            this.switcher.notReady('Reset devices');
            this.logger.error('Reset devices!');

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
                this.logger.error('Phone Device Not Ready!');
                console.log('Phone Device Not Ready!');
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
            this.logger.success('Twilio Device Registered!');
            if (this.isReady()) {
                this.ready();
            }
        }

        this.twilioUnregister = function () {
            this.isTwilioRegistered = false;
            this.register.twilioNotReady();
            this.panel.twilioError();
            this.logger.error('Twilio Device UnRegistered!');
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

        this.notReady('Load devices');

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

    function Init(userId, isDevicePage, logger) {
        if (isDevicePage) {
            return new State(userId, new Register(userId), logger, false, false, false);
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
                return;
            }
        });
        return new State(
            userId,
            new DummyRegister(userId),
            new window.phoneWidget.logger.DummyLogger(),
            localStorage.getItem(getStorageNameTwilioStatus(userId)) === 'ready',
            localStorage.getItem(getStorageNameSpeakerStatus(userId)) === 'ready',
            localStorage.getItem(getStorageNameMicrophoneStatus(userId)) === 'ready'
        );
    }

    window.phoneWidget.device.state = {
        Init: Init
    }
})();
