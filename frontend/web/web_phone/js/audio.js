(function () {
    const storageKeys = {
        audioDevices: 'audio_devices',
        audioDevicesIsMuted: 'audio_devices_is_muted',
        audioDevicesActiveChanged: 'audio_devices_active_changed',
    };

    function Collection() {
        this.isActive = function (id) {
            let collection = this.load();
            return parseInt(collection.activeDeviceId) === parseInt(id);
        };

        this.remove = function (id) {
            let collection = this.load();
            if (collection.devices.length === 0) {
                return;
            }
            let index = collection.devices.findIndex((el) => el === id);
            if (index < 0) {
                return;
            }
            collection.devices.splice(index, 1);
            this.save(collection);
        };

        this.add = function (deviceId, devices) {
            let collection = this.load();
            collection.devices = devices.map((v) => parseInt(v));
            if (!collection.devices.includes(deviceId)) {
                collection.devices.push(deviceId);
            }
            this.save(collection);
        };

        this.load = function () {
            let collection = localStorage.getItem(storageKeys.audioDevices);
            if (collection) {
                return JSON.parse(collection);
            }
            return {
                devices: [],
                activeDeviceId: null
            };
        };

        this.save = function (collection) {
            let oldActiveDeviceId = collection.activeDeviceId;
            collection.activeDeviceId = Math.min(...collection.devices);
            localStorage.setItem(storageKeys.audioDevices, JSON.stringify(collection));
            if (oldActiveDeviceId === collection.activeDeviceId) {
                return;
            }
            let oldChangedValue = localStorage.getItem(storageKeys.audioDevicesActiveChanged);
            let newChangedValue = '0';
            if (oldChangedValue === '0') {
                newChangedValue = '1';
            }
            localStorage.setItem(storageKeys.audioDevicesActiveChanged, newChangedValue);
        };
    }

    function Incoming(queues, notifier, incomingPane, outgoingPane, isMuted) {
        this.queues = queues;
        this.notifier = notifier;
        this.incomingPane = incomingPane;
        this.outgoingPane = outgoingPane;
        this.devices = new Collection();

        this.audio = new Audio('/js/sounds/incoming_call.mp3');
        this.audio.volume = 0.3;
        this.audio.loop = true;

        this.isOn = true;
        this.offKey = null;

        this.playing = false;

        this.addDevice = function (deviceId, devices) {
            this.devices.add(deviceId, devices);
        };

        this.removeDevice = function (deviceId) {
            this.devices.remove(deviceId);
        };

        this.play = function () {
            if (!this.devices.isActive(PhoneWidget.getDeviceState().getDeviceId())) {
                return;
            }
            this.audio.play();
            this.playing = true;
        };

        this.stop = function () {
            this.audio.pause();
            this.audio.currentTime = 0;
            this.playing = false;
        };

        this.muted = function (withOutSave) {
            this.audio.muted = true;
            this.indicatorMuted();
            if (withOutSave) {
                return;
            }
            localStorage.setItem(storageKeys.audioDevicesIsMuted, '1');
        };

        this.unMuted = function (withOutSave) {
            this.audio.muted = false;
            this.indicatorUnMuted();
            if (withOutSave) {
                return;
            }
            localStorage.setItem(storageKeys.audioDevicesIsMuted, '0');
        };

        this.isMuted = function () {
            return this.audio.muted === true;
        };

        this.refresh = function () {
            if (!this.isOn) {
                this.stop();
                return;
            }
            if (this.queues.active.count() > 0) {
                this.stop();
                return;
            }
            if (this.outgoingPane.isActive()) {
                this.stop();
                return;
            }
            if (this.incomingPane.isActive()) {
                this.play();
                return;
            }
            if (this.notifier.getVisibleNotifications().length > 0) {
                this.play();
                return;
            }
            this.stop();
        };

        this.indicatorMuted = function () {
            $('#incoming-sound-indicator').attr('data-status', 0).html('<i class="fa fa-volume-off text-danger"> </i>').attr('title', 'Incoming Call - Volume OFF');
        };

        this.indicatorUnMuted = function () {
            $('#incoming-sound-indicator').attr('data-status', 1).html('<i class="fa fa-volume-up text-success"> </i>').attr('title', 'Incoming Call - Volume ON');
        };

        this.on = function (key) {
            if (this.isOff() && this.offKey !== key) {
                return;
            }
            this.isOn = true;
        };

        this.off = function (key) {
            this.isOn = false;
            this.offKey = key;
        };

        this.isOff = function () {
            return this.isOn === false;
        };

        if (isMuted) {
            this.muted(true);
        }
    }

    function Init(queues, notifier, incomingPane, outgoingPane) {
        return new Incoming(queues, notifier, incomingPane, outgoingPane, localStorage.getItem(storageKeys.audioDevicesIsMuted) === '1');
    }

    window.addEventListener("beforeunload", function (e) {
        let deviceId = PhoneWidget.getDeviceState().getDeviceId();
        if (deviceId) {
            PhoneWidget.audio.incoming.removeDevice(deviceId);
        }
    });

    window.addEventListener("storage", function (e) {
        if (e.key === storageKeys.audioDevicesIsMuted) {
            if (e.newValue === '1') {
                PhoneWidget.audio.incoming.muted(true);
                return;
            }
            PhoneWidget.audio.incoming.unMuted(true);
        }
    });

    window.addEventListener("storage", function (e) {
        if (e.key === storageKeys.audioDevicesActiveChanged) {
            PhoneWidget.audio.incoming.refresh();
        }
    });

    window.phoneWidget.audio = {
        Incoming: Init
    }
})();