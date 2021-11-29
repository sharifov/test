(function () {
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

        this.ringtoneOk = function () {
            $('.tab-device-status-ringtone').html('Ringtone device: <span style="color: #00a35b">OK');
        };

        this.ringtoneError = function () {
            $('.tab-device-status-ringtone').html('Ringtone device: <span style="color: #761c19">Error');
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
            localStorage.setItem('PhoneWidgetDeviceStatus', 'ready');
        }

        this.notReady = function () {
            localStorage.setItem('PhoneWidgetDeviceStatus', 'not-ready');
        }
    }

    function DevicePageStatus(logger) {
        this.isDeviceRegistered = false;
        this.isSpeakerSelected = false;
        this.isRingtoneSelected = false;
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
            this.isRingtoneSelected = false;
            this.devices.ringtoneError();
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
            return this.isDeviceRegistered && this.isSpeakerSelected && this.isRingtoneSelected && this.isMicrophoneSelected;
        };

        this.deviceRegister = function () {
            this.isDeviceRegistered = true;
            this.logger.add('Device Registered!');
            this.devices.twilioOk();
            if (this.isReady()) {
                this.ready();
            }
        }

        this.deviceUnregister = function () {
            this.isDeviceRegistered = false;
            this.logger.add('Device UnRegistered!');
            this.devices.twilioError();
            this.notReady();
        }

        this.speakerSelected = function () {
            this.isSpeakerSelected = true;
            this.logger.add('Speaker Selected!');
            this.devices.speakerOk();
            if (this.isReady()) {
                this.ready();
            }
        }

        this.speakerUnselected = function () {
            this.isSpeakerSelected = false;
            this.logger.add('Speaker UnSelected!');
            this.devices.speakerError();
            this.notReady();
        }

        this.ringtoneSelected = function () {
            this.isRingtoneSelected = true;
            this.logger.add('Ringtone Selected!');
            this.devices.ringtoneOk();
            if (this.isReady()) {
                this.ready();
            }
        }

        this.ringtoneUnselected = function () {
            this.isRingtoneSelected = false;
            this.logger.add('Ringtone UnSelected!');
            this.devices.ringtoneError();
            this.notReady();
        }

        this.microphoneSelected = function () {
            this.isMicrophoneSelected = true;
            this.logger.add('Microphone Selected!');
            this.devices.microphoneOk();
            if (this.isReady()) {
                this.ready();
            }
        }

        this.microphoneUnselected = function () {
            this.isMicrophoneSelected = false;
            this.logger.add('Microphone UnSelected!');
            this.devices.microphoneError();
            this.notReady();
        }

        this.devices.microphoneError();
        this.devices.ringtoneError();
        this.devices.speakerError();
        this.devices.twilioError();
        this.notReady();
    }

    function OtherPageStatus(isReady) {
        this.switcher = new Switcher();
        this.isReadyValue = isReady;

        this.ready = function () {
            this.isReadyValue = true;
            this.switcher.ready();
        };

        this.notReady = function () {
            this.isReadyValue = false;
            this.switcher.notReady();
        };

        this.isReady = function () {
            return this.isReadyValue === true;
        };

        if (isReady === true) {
            this.ready();
        } else {
            this.notReady();
        }
    }

    function Init(isDevicePage, logger) {
        if (isDevicePage) {
            return new DevicePageStatus(logger);
        }

        window.addEventListener('storage', function (event) {
            if (event.key !== 'PhoneWidgetDeviceStatus') {
                return;
            }
            if (event.newValue === 'ready') {
                PhoneWidget.getDeviceStatus().ready();
                return;
            }
            PhoneWidget.getDeviceStatus().notReady();
        });
        return new OtherPageStatus(localStorage.getItem('PhoneWidgetDeviceStatus') === 'ready');
    }

    window.phoneWidget.device.status = {
        Init: Init
    }
})();
