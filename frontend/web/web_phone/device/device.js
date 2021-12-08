(function () {
    function Init(remoteLogsEnabled, deviceId) {
        if (initiated) {
            if (initDeviceId !== deviceId) {
                createNotify('Phone Device', 'Device Id was changed. Please refresh page!', 'error');
            }
            console.log('device already initiated');
            return;
        }

        initiated = true;
        initDeviceId = deviceId;

        localStorage.setItem(window.phoneDeviceIdStorageKey, initDeviceId);

        // console.log("Requesting Twilio Access Token...");
        PhoneWidget.addLog("Requesting Twilio Access Token...");
        $.getJSON('/phone/get-token?deviceId=' + deviceId)
            .then(function (response) {
                // console.log("Got a Twilio Access token.");
                PhoneWidget.addLog("Got a Twilio Access token.");
                initDevice({"token": response.data.token, "refreshTime": response.data.refreshTime}, remoteLogsEnabled, deviceId);
            })
            .catch(function (err) {
                PhoneWidget.addLog("Get Twilio Access token error. Reload page!");
                console.log(err);
                createNotify('Twilio Token error!', 'Could not get a token from server! Please reload page!', 'error');
            });

        function initDevice(token, remoteLogsEnabled, deviceId) {
            // console.log("Init Twilio Device...");
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
                            deviceId: deviceId,
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
                    createNotify('Phone widget', 'Microphone device not found.', 'error')
                    PhoneWidget.addLog('Not found Microphone device');
                    PhoneWidget.getDeviceStatus().microphoneUnselected();
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
                PhoneWidget.getDeviceStatus().microphoneSelected();
            }

            function updateAllAudioDevices() {
                updateOutputDevices(speakerDevices, this.audio.speakerDevices.get());
            }

            const updateToken = () => {
                PhoneWidget.addLog("Update Twilio Access Token...");
                $.getJSON('/phone/get-token?deviceId=' + deviceId)
                    .then(function (response) {
                        //console.log("Got a Twilio Access token.");
                        PhoneWidget.addLog("Got a Twilio Access token.");
                        device.updateToken(response.data.token);
                        setTimeout(async () => updateToken(), response.data.refreshTime * 1000);
                    })
                    .catch(function (err) {
                        PhoneWidget.addLog("Get Twilio Access token error. Reload page!", '#f41b1b');
                        console.log(err);
                        createNotify('Twilio Token error!', 'Could not get a token from server! Please reload page!', 'error');
                    });
            };

           setTimeout(async () => updateToken(), token.refreshTime * 1000);

            device.on('registering', () => {
                //console.log("Twilio.Device Registering...");
                PhoneWidget.addLog("Twilio.Device Registering...");
            });

            const incomingCallHandler = (call) => {
                PhoneWidget.incomingSoundOff();

                call.on('accept', call => {
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    //console.log('The incoming call was accepted.');
                    PhoneWidget.freeDialButton();
                    PhoneWidget.setActiveCall(call);
                    PhoneWidget.incomingSoundOff();

                    call.on("volume", function (inputVolume, outputVolume) {
                        PhoneWidget.volumeIndicatorsChange(inputVolume, outputVolume)
                    });
                    PhoneWidget.soundConnect();
                });
                call.on('cancel', () => {
                    //console.log('The call has been canceled.');
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
                    //console.log('The call has been disconnected.');
                    PhoneWidget.freeDialButton();
                    PhoneWidget.removeTwilioInternalIncomingConnection();
                    PhoneWidget.soundDisconnect();
                    PhoneWidget.incomingSoundOff();
                    window.sendCommandUpdatePhoneWidgetCurrentCalls(call.parameters.CallSid, window.userId, window.generalLinePriorityIsEnabled, null, false);
                });
                call.on('error', error => {
                    createNotify('Call error', 'More info in logs panel', 'error');
                    // console.log('An error has occurred: ', error);
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
                            //console.log("Accepted incoming call.");
                        })
                        .catch(error => {
                            console.log(error);
                            createNotify('Accept incoming connection', error.message, 'error')
                        });
                }
            };

            device.on("registered", () => {
                //console.log("Twilio.Device Ready!");

                PhoneWidget.getDeviceStatus().deviceRegister();

                if (device.audio.speakerDevices.get().size > 0) {
                    PhoneWidget.getDeviceStatus().speakerSelected();
                } else {
                    PhoneWidget.getDeviceStatus().speakerUnselected();
                }

                device.audio.removeListener('deviceChange', updateInputDevice);
                device.audio.addListener('deviceChange', updateInputDevice);
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then((stream) => {
                        updateInputDevice();
                        stream.getTracks().forEach(track => track.stop());
                    }).catch(error => {
                        console.log(error);
                        error.comment = 'Microphone error';
                        PhoneWidget.addLog(error);
                        PhoneWidget.getDeviceStatus().microphoneUnselected();
                    });

                device.removeListener("incoming", incomingCallHandler);
                device.addListener("incoming", incomingCallHandler);
            });

            device.on('unregistered', function () {
                //console.log("Twilio.Device unregistered!");
                PhoneWidget.getDeviceStatus().reset();
                PhoneWidget.incomingSoundOff();
                PhoneWidget.getDeviceStatus().deviceUnregister();
            });

            device.on('error', (twilioError, call) => {
                if (twilioError.code === 20104) {
                    //console.log('Twilio JWT Token Expired');
                    PhoneWidget.addLog('Twilio JWT Token Expired', '#f41b1b');
                    //console.log("Requesting New Twilio Access Token...");
                    PhoneWidget.addLog("Requesting New Twilio Access Token...");
                    updateToken();
                    return;
                }
               // console.log('An error has occurred: ', twilioError);

                twilioLogger.error('%j', twilioError);
                PhoneWidget.freeDialButton();
                PhoneWidget.addLog(twilioError);
                PhoneWidget.incomingSoundOff();
                createNotify(twilioError.description, twilioError.explanation, 'error');
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
    }

    let initiated = false;
    let initDeviceId = null;

    window.phoneWidget.device.initialize = {
        Init: Init
    }
})();