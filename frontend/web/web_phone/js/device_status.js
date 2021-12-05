(function () {
    const storageNames = {
        PhoneWidgetDeviceStatus: 'PhoneWidgetDeviceStatus',
        PhoneWidgetTwilioDeviceStatus: 'PhoneWidgetTwilioDeviceStatus',
        PhoneWidgetSpeakerDeviceStatus: 'PhoneWidgetSpeakerDeviceStatus',
        PhoneWidgetMicrophoneDeviceStatus: 'PhoneWidgetMicrophoneDeviceStatus'
    }

    function Switcher() {
        this.inner = '.widget-icon-inner';

        this.ready = function () {
            $(this.inner).attr('data-phone-device-status', 'ready');
        }

        this.notReady = function () {
            $(this.inner).attr('data-phone-device-status', 'not-ready');
        }
    }

    function Devices() {
        this.twilioOk = function () {
            $('.tab-device-status-twilio').html('Twilio device: <span style="color: #00a35b">OK');
        };

        this.twilioError = function () {
            $('.tab-device-status-twilio').html('Twilio device: <span style="color: #761c19">Error');
        };

        this.speakerOk = function () {
            $('.tab-device-status-speaker').html('Speaker device: <span style="color: #00a35b">OK');
        };

        this.speakerError = function () {
            $('.tab-device-status-speaker').html('Speaker device: <span style="color: #761c19">Error');
        };

        this.microphoneOk = function () {
            $('.tab-device-status-microphone').html('Microphone device: <span style="color: #00a35b">OK');
        };

        this.microphoneError = function () {
            $('.tab-device-status-microphone').html('Microphone device: <span style="color: #761c19">Error');
        };
    }

    function StateRegister() {
        this.ready = function () {
            localStorage.setItem(storageNames.PhoneWidgetDeviceStatus, 'ready');
        }

        this.notReady = function () {
            localStorage.setItem(storageNames.PhoneWidgetDeviceStatus, 'not-ready');
        }

        this.twilioDeviceReady = function () {
            localStorage.setItem(storageNames.PhoneWidgetTwilioDeviceStatus, 'ready');
        }

        this.twilioDeviceNotReady = function () {
            localStorage.setItem(storageNames.PhoneWidgetTwilioDeviceStatus, 'not-ready');
        }

        this.speakerDeviceReady = function () {
            localStorage.setItem(storageNames.PhoneWidgetSpeakerDeviceStatus, 'ready');
        }

        this.speakerDeviceNotReady = function () {
            localStorage.setItem(storageNames.PhoneWidgetSpeakerDeviceStatus, 'not-ready');
        }

        this.microphoneDeviceReady = function () {
            localStorage.setItem(storageNames.PhoneWidgetMicrophoneDeviceStatus, 'ready');
        }

        this.microphoneDeviceNotReady = function () {
            localStorage.setItem(storageNames.PhoneWidgetMicrophoneDeviceStatus, 'not-ready');
        }
    }

    function DevicePageStatus(logger) {
        this.isDeviceRegistered = false;
        this.isSpeakerSelected = false;
        this.isMicrophoneSelected = false;
        this.switcher = new Switcher()
        this.stateRegister = new StateRegister();
        this.logger = logger;
        this.devices = new Devices();

        this.reset = function () {
            this.isDeviceRegistered = false;
            this.devices.twilioError();
            this.isSpeakerSelected = false;
            this.devices.speakerError();
            this.isMicrophoneSelected = false;
            this.devices.microphoneError();
        };

        this.ready = function () {
            this.stateRegister.ready();
            this.switcher.ready();
            this.logger.add('Phone Widget Ready!', '#4e9e22');
            console.log('Phone Widget Ready!');
        };

        this.notReady = function () {
            this.stateRegister.notReady();
            this.switcher.notReady();
            this.logger.add('Phone Widget Not Ready!', '#f41b1b');
            console.log('Phone Widget Not Ready!');
        };

        this.isReady = function () {
            return this.isDeviceRegistered && this.isSpeakerSelected && this.isMicrophoneSelected;
        };

        this.deviceRegister = function () {
            this.stateRegister.twilioDeviceReady();
            this.isDeviceRegistered = true;
            this.logger.add('Device Registered!');
            this.devices.twilioOk();
            if (this.isReady()) {
                this.ready();
            }
        }

        this.deviceUnregister = function () {
            this.stateRegister.twilioDeviceNotReady();
            this.isDeviceRegistered = false;
            this.logger.add('Device UnRegistered!');
            this.devices.twilioError();
            this.notReady();
        }

        this.speakerSelected = function () {
            this.stateRegister.speakerDeviceReady()
            this.isSpeakerSelected = true;
            this.logger.add('Speaker Selected!');
            this.devices.speakerOk();
            if (this.isReady()) {
                this.ready();
            }
        }

        this.speakerUnselected = function () {
            this.stateRegister.speakerDeviceNotReady()
            this.isSpeakerSelected = false;
            this.logger.add('Speaker UnSelected!');
            this.devices.speakerError();
            this.notReady();
        }

        this.microphoneSelected = function () {
            this.stateRegister.microphoneDeviceReady();
            this.isMicrophoneSelected = true;
            this.logger.add('Microphone Selected!');
            this.devices.microphoneOk();
            if (this.isReady()) {
                this.ready();
            }
        }

        this.microphoneUnselected = function () {
            this.stateRegister.microphoneDeviceNotReady();
            this.isMicrophoneSelected = false;
            this.logger.add('Microphone UnSelected!');
            this.devices.microphoneError();
            this.notReady();
        }

        this.devices.twilioError();
        this.devices.speakerError();
        this.devices.microphoneError();
        this.notReady();
    }

    function OtherPageStatus(phoneIsReady, twilioDeviceIsReady, speakerIsReady, microphoneIsReady) {
        this.switcher = new Switcher();
        this.phoneIsReady = phoneIsReady;
        this.devices = new Devices();

        this.ready = function () {
            this.phoneIsReady = true;
            this.switcher.ready();
        };

        this.notReady = function () {
            this.phoneIsReady = false;
            this.switcher.notReady();
        };

        this.isReady = function () {
            return this.phoneIsReady === true;
        };

        this.deviceRegister = function () {
            this.devices.twilioOk();
        }

        this.deviceUnregister = function () {
            this.devices.twilioError();
        }

        this.speakerSelected = function () {
            this.devices.speakerOk();
        }

        this.speakerUnselected = function () {
            this.devices.speakerError();
        }

        this.microphoneSelected = function () {
            this.devices.microphoneOk();
        }

        this.microphoneUnselected = function () {
            this.devices.microphoneError();
        }

        if (phoneIsReady === true) {
            this.ready();
        } else {
            this.notReady();
        }

        if (twilioDeviceIsReady === true) {
            this.deviceRegister();
        } else {
            this.deviceUnregister();
        }

        if (speakerIsReady === true) {
            this.speakerSelected();
        } else {
            this.speakerUnselected();
        }

        if (microphoneIsReady === true) {
            this.microphoneSelected();
        } else {
            this.microphoneUnselected();
        }
    }

    function Init(isDevicePage, logger) {
        if (isDevicePage) {
            return new DevicePageStatus(logger);
        }

        window.addEventListener('storage', function (event) {
            if (event.key === storageNames.PhoneWidgetDeviceStatus) {
                if (event.newValue === 'ready') {
                    PhoneWidget.getDeviceStatus().ready();
                    return;
                }
                PhoneWidget.getDeviceStatus().notReady();
                return;
            }
            if (event.key === storageNames.PhoneWidgetTwilioDeviceStatus) {
                if (event.newValue === 'ready') {
                    PhoneWidget.getDeviceStatus().deviceRegister();
                    return;
                }
                PhoneWidget.getDeviceStatus().deviceUnregister();
                return;
            }
            if (event.key === storageNames.PhoneWidgetSpeakerDeviceStatus) {
                if (event.newValue === 'ready') {
                    PhoneWidget.getDeviceStatus().speakerSelected();
                    return;
                }
                PhoneWidget.getDeviceStatus().speakerUnselected();
                return;
            }
            if (event.key === storageNames.PhoneWidgetMicrophoneDeviceStatus) {
                if (event.newValue === 'ready') {
                    PhoneWidget.getDeviceStatus().microphoneSelected();
                    return;
                }
                PhoneWidget.getDeviceStatus().microphoneUnselected();
                return;
            }
        });
        return new OtherPageStatus(
            localStorage.getItem(storageNames.PhoneWidgetDeviceStatus) === 'ready',
            localStorage.getItem(storageNames.PhoneWidgetTwilioDeviceStatus) === 'ready',
            localStorage.getItem(storageNames.PhoneWidgetSpeakerDeviceStatus) === 'ready',
            localStorage.getItem(storageNames.PhoneWidgetMicrophoneDeviceStatus) === 'ready'
        );
    }

    window.phoneWidget.device.status = {
        Init: Init
    }
})();
