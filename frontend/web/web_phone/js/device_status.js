(function () {
    function Switcher() {
        this.inner = '.widget-icon-inner';

        this.register = function () {
            $(this.inner).attr('data-device-status', "registered");
        }

        this.unregister = function () {
            $(this.inner).attr('data-device-status', "unregistered");
        }
    }

    function Status(initValue, storageRegister) {
        this.registered = false;
        this.swithcer = new Switcher()
        this.storageRegister = storageRegister;

        this.isReady = function () {
            return this.registered;
        };

        this.register = function () {
            this.registered = true;
            this.swithcer.register();
            this.storageRegister.register();
        }

        this.unregister = function () {
            this.registered = false;
            this.swithcer.unregister();
            this.storageRegister.unregister();
        }

        if (initValue === true) {
            this.register();
        } else {
            this.unregister();
        }
    }

    function DevicePageStorageRegister() {
        this.register = function () {
            localStorage.setItem('TwilioDeviceStatus', 'true');
        }

        this.unregister = function () {
            localStorage.setItem('TwilioDeviceStatus', 'false');
        }
    }

    function OtherPageStorageRegister() {
        this.register = function () {}
        this.unregister = function () {}
    }

    function Init(isDevicePage) {
        if (isDevicePage) {
            if (window.TwilioDevice) {
                return new Status(window.TwilioDevice.status === "registered", new DevicePageStorageRegister());
            }
            return new Status(false, new DevicePageStorageRegister());
        }

        window.addEventListener('storage', function (event) {
            if (event.key !== 'TwilioDeviceStatus') {
                return;
            }
            if (event.newValue === 'true') {
                PhoneWidgetCall.getDeviceStatus().register();
                return;
            }
            PhoneWidgetCall.getDeviceStatus().unregister();
        });
        return new Status(localStorage.getItem('TwilioDeviceStatus') === 'true', new OtherPageStorageRegister());
    }

    window.phoneWidget.device.status = {
        Init: Init
    }
})();
