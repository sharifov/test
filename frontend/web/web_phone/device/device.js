(function () {
    function Init(remoteLogsEnabled, deviceId) {
        initDeviceId = deviceId;
        localStorage.setItem(window.phoneDeviceIdStorageKey, initDeviceId);

        if (initiated) {
            PhoneWidget.addLogError('device already initiated');
            return;
        }

        initiated = true;

        const twilioTokenUrl = '/phone/get-token?deviceId=' + initDeviceId;

        PhoneWidget.addLog("Requesting Twilio Access Token...");
        $.getJSON(twilioTokenUrl)
            .then(function (response) {
                PhoneWidget.addLog("Got a Twilio Access token.");
                initDevice({"token": response.data.token, "refreshTime": response.data.refreshTime}, remoteLogsEnabled);
            })
            .catch(function (error) {
                PhoneWidget.addLogError("Get Twilio Access token error. Reload page!");
                console.error(error);
            });

        function initDevice(token, remoteLogsEnabled) {
            PhoneWidget.addLog("Init Twilio Device...");

           const twilioLogger = Twilio.Logger;
           twilioLogger.setLevel('ERROR');
           twilioLogger.getLogger = function () {}; // fix for remote logger

            // const originalFactory = twilioLogger.methodFactory;
            // twilioLogger.methodFactory = function (methodName, logLevel, loggerName) {
            //     const method = originalFactory(methodName, logLevel, loggerName);
            //     return function (message) {
            //         const prefix = '[My Application]';
            //         method(prefix + message);
            //     };
            // };
            // twilioLogger.setLevel(twilioLogger.getLevel());

            if (remoteLogsEnabled) {
                remote.apply(
                    twilioLogger,
                    {
                        url: '/voip/log',
                        interval: 30000,
                        stacktrace: {
                            levels: ['error'],
                            depth: 10,
                            excess: 0
                        },
                        format: log => ({
                            deviceId: initDeviceId,
                            level: log.level.value,
                            message: log.message,
                            timestamp: log.timestamp,
                            stacktrace: log.stacktrace
                        }),
                        timestamp: function () {
                            let date = new Date();
                            let day = ('0' + date.getDate()).slice(-2);
                            let month = ('0' + (date.getMonth() + 1)).slice(-2);
                            let year = date.getFullYear();
                            let hours = ('0' + date.getHours()).slice(-2);
                            let minutes = ('0' + date.getMinutes()).slice(-2);
                            let seconds = ('0' + date.getSeconds()).slice(-2);
                            return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
                        }
                    }
                );
            }

            const device = new Twilio.Device(token.token, {
                closeProtection: true,
                codecPreferences: ["opus", "pcmu"]
            });

            const speakerDevices = document.getElementById("speaker-devices");
            const microphoneDevices = document.getElementById("microphone-devices");

            speakerDevices.addEventListener("change", updateOutputDevice);

            function updateOutputDevice() {
                const selectedDevices = Array.from(speakerDevices.children)
                    .filter((node) => node.selected)
                    .map((node) => node.getAttribute("data-id"));

                device.audio.speakerDevices.set(selectedDevices);
            }

            function updateOutputDevices(selectEl, selectedDevices) {
                selectEl.innerHTML = '';
                device.audio.availableOutputDevices.forEach(function (device, id) {
                    let isActive = (selectedDevices.size === 0 && id === 'default');
                    selectedDevices.forEach(function (device) {
                        if (device.deviceId === id) {
                            isActive = true;
                        }
                    });
                    let option = document.createElement('option');
                    option.label = device.label;
                    option.setAttribute('data-id', id);
                    if (isActive) {
                        option.setAttribute('selected', 'selected');
                    }
                    selectEl.appendChild(option);
                });
            }

            const updateInputDevice = function () {
                microphoneDevices.innerHTML = '';

                if (device.audio.availableInputDevices.size < 1) {
                    twilioLogger.error(createError({
                        name: 'Update input device',
                        message: 'Not found Microphone device'
                    }));
                    PhoneWidget.addLogError('Not found Microphone device');
                    PhoneWidget.getDeviceState().microphoneUnselected();
                    return;
                }

                let isSelected = false;
                device.audio.availableInputDevices.forEach(device => {
                    const option = document.createElement('option');
                    option.label = device.label;
                    option.value = device.deviceId;
                    option.innerText = device.label;
                    if (isSelected === false) {
                        option.setAttribute('selected', 'selected');
                        isSelected = true;
                    }
                    microphoneDevices.appendChild(option);
                });
                PhoneWidget.getDeviceState().microphoneSelected();
            }

            function updateAllAudioDevices() {
                updateOutputDevices(speakerDevices, this.audio.speakerDevices.get());
            }

            const updateToken = () => {
                PhoneWidget.addLog("Update Twilio Access Token...");
                $.getJSON(twilioTokenUrl)
                    .then(function (response) {
                        PhoneWidget.addLogSuccess("Got a Twilio Access token.");
                        device.updateToken(response.data.token);
                        setTimeout(async () => updateToken(), response.data.refreshTime * 1000);
                    })
                    .catch(function (error) {
                        PhoneWidget.addLogError("Get Twilio Access token error. Reload page!");
                        error.url = twilioTokenUrl;
                        if (!error.message) {
                            error.message = error.responseText || 'Twilio token update';
                        }
                        twilioLogger.error('%j', error);
                    });
            };

           setTimeout(async () => updateToken(), token.refreshTime * 1000);

            device.on('registering', () => {
                PhoneWidget.addLogSuccess("Twilio.Device Registering.");
            });

            const incomingCallHandler = (call) => {
                PhoneWidget.incomingSoundOff();

                call.on('accept', call => {
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    PhoneWidget.freeDialButton();
                    PhoneWidget.setActiveCall(call);
                    PhoneWidget.incomingSoundOff();
                    PhoneWidget.soundConnect();
                    call.on("volume", function (inputVolume, outputVolume) {
                        PhoneWidget.volumeIndicatorsChange(inputVolume, outputVolume)
                    });
                });
                call.on('cancel', () => {
                    PhoneWidget.freeDialButton();
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    PhoneWidget.incomingSoundOff();
                });
                call.on('disconnect', call => {
                    try {
                        device.audio.unsetInputDevice();
                    } catch (error) {
                        console.log(error);
                    }
                    PhoneWidget.freeDialButton();
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    PhoneWidget.soundDisconnect();
                    PhoneWidget.incomingSoundOff();
                    window.sendCommandUpdatePhoneWidgetCurrentCalls(call.parameters.CallSid, window.userId, window.generalLinePriorityIsEnabled);
                });
                call.on('error', error => {
                    createNotify('Call error', 'More info in logs panel', 'error');
                    twilioLogger.error('%j', error);
                    PhoneWidget.addLog(error);
                });

                let autoAccept = null;
                let isInternal = null;
                let requestCall = null;

                call.customParameters.forEach(function (value, key) {
                    if (key === 'autoAccept' && value === 'false') {
                        autoAccept = false;
                    } else if (key === 'isInternal' && value === 'true') {
                        isInternal = true;
                    } else if (key === 'requestCall') {
                        requestCall = value;
                    }
                });

                if (autoAccept === false) {
                    if (isInternal === true && requestCall !== null) {
                        let callObj = JSON.parse(atob(requestCall), function (k, v) {
                            if (v === 'false') {
                                return false;
                            } else if (v === 'true') {
                                return true;
                            }
                            return v;
                        });
                        callObj.callSid = call.parameters.CallSid;
                        PhoneWidget.setTwilioInternalIncomingConnection(call);
                        PhoneWidget.refreshCallStatus(callObj);
                    }
                } else {
                    device.audio.setInputDevice(microphoneDevices.value)
                        .then(() => {
                            call.accept();
                        })
                        .catch(error => {
                            let err = createError(error, 'Microphone error');
                            twilioLogger.error('%j', err);
                            PhoneWidget.addLog(error);
                            createNotify('Accept incoming connection', error.message, 'error')
                        });
                }
            };

            device.on("registered", () => {
                PhoneWidget.getDeviceState().twilioRegister();

                if (device.audio.speakerDevices.get().size > 0) {
                    PhoneWidget.getDeviceState().speakerSelected();
                } else {
                    PhoneWidget.getDeviceState().speakerUnselected();
                }

                device.audio.removeListener('deviceChange', updateInputDevice);
                device.audio.addListener('deviceChange', updateInputDevice);
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then((stream) => {
                        updateInputDevice();
                        stream.getTracks().forEach(track => track.stop());
                    }).catch(error => {
                        let err = createError(error, 'Microphone error');
                        twilioLogger.error('%j', err);
                        PhoneWidget.addLog(error);
                        PhoneWidget.getDeviceState().microphoneUnselected();
                    });
                device.removeListener("incoming", incomingCallHandler);
                device.addListener("incoming", incomingCallHandler);
            });

            device.on('unregistered', function () {
                PhoneWidget.getDeviceState().reset();
                PhoneWidget.incomingSoundOff();
            });

            device.on('error', (error, call) => {
                if (error.code === 20104) {
                    twilioLogger.error('%j', error);
                    PhoneWidget.addLogError('Twilio JWT Token Expired');
                    updateToken();
                    return;
                }

                twilioLogger.error('%j', error);
                PhoneWidget.addLog(error);
                PhoneWidget.freeDialButton();
                PhoneWidget.incomingSoundOff();
            });

           device.audio.on("deviceChange", updateAllAudioDevices.bind(device));

            if (device.audio.isOutputSelectionSupported) {
                $('#output-selection').show();
            } else {
                $(document).find('.phone-widget__additional-bar .tabs__nav.tab-nav .wp-tab-device').hide();
                $(document).find('.phone-widget__additional-bar .wp-devices-tab-log').addClass('active-tab');
                $(document).find('.phone-widget__additional-bar #tab-device').hide();
                $(document).find('.phone-widget__additional-bar #tab-logs').show();
            }

            device.register();
        }

        const createError = (error, defaultMessage) => ({
            name: error.name || defaultMessage,
            code: error.code,
            message: error.message || defaultMessage,
            description: error.description || defaultMessage,
            comment: error.comment || defaultMessage,
            explanation: error.explanation || defaultMessage,
            causes: error.causes,
            solutions: error.solutions,
            originalError: error.originalError
        });
    }

    let initiated = false;
    let initDeviceId = null;

    window.phoneWidget.device.initialize = {
        Init: Init
    }
})();