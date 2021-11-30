(function () {
    function Init() {
        if (initiated) {
            console.log('device already initiated');
            return;
        }

        initiated = true;

        // console.log("Requesting Twilio Access Token...");
        PhoneWidget.addLog("Requesting Twilio Access Token...");
        $.getJSON('/phone/get-token')
            .then(function (response) {
                // console.log("Got a Twilio Access token.");
                PhoneWidget.addLog("Got a Twilio Access token.");
                initDevice(response.data.token);
            })
            .catch(function (err) {
                PhoneWidget.addLog("Get Twilio Access token error. Reload page!");
                console.log(err);
                createNotify('Twilio Token error!', 'Could not get a token from server! Please reload page!', 'error');
            });

        function initDevice(token) {
            // console.log("Init Twilio Device...");
            PhoneWidget.addLog("Init Twilio Device...");

            const device = new Twilio.Device(token, {
               //logLevel: 1,
                //edge: 'ashburn',
                closeProtection: true,
                codecPreferences: ["opus", "pcmu"]
            });

            const speakerDevices = document.getElementById("speaker-devices");
            const ringtoneDevices = document.getElementById("ringtone-devices");
            const microphoneDevices = document.getElementById("microphone-devices");

            speakerDevices.addEventListener("change", updateOutputDevice);
            ringtoneDevices.addEventListener("change", updateRingtoneDevice);

            function updateOutputDevice() {
                const selectedDevices = Array.from(speakerDevices.children)
                    .filter((node) => node.selected)
                    .map((node) => node.getAttribute("data-id"));

                device.audio.speakerDevices.set(selectedDevices);
            }

            function updateRingtoneDevice() {
                const selectedDevices = Array.from(ringtoneDevices.children)
                    .filter((node) => node.selected)
                    .map((node) => node.getAttribute("data-id"));

                device.audio.ringtoneDevices.set(selectedDevices);
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
                updateOutputDevices(ringtoneDevices, this.audio.ringtoneDevices.get());
            }

            // todo
//            setInterval(async () => {
//                $.getJSON('/phone/get-token')
//                    .then(function (response) {
//                        console.log("Got a New Twilio token.");
//                        device.updateToken(response.data.token);
//                    })
//                    .catch(function (err) {
//                        console.log(err);
//                        createNotify('Call Token error!', 'Could not get a token from server!', 'error');
//                    });
//            }, ttl - refreshBuffer);

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
                    window.sendCommandUpdatePhoneWidgetCurrentCalls(call.parameters.CallSid, window.userId, window.generalLinePriorityIsEnabled, true);
                });
                call.on('error', error => {
                    createNotify('Call error', 'More info in logs panel', 'error');
                    console.log('An error has occurred: ', error);
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

                if (device.audio.ringtoneDevices.get().size > 0) {
                    PhoneWidget.getDeviceStatus().ringtoneSelected();
                } else {
                    PhoneWidget.getDeviceStatus().ringtoneUnselected();
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
                    PhoneWidget.addLog('Twilio JWT Token Expired');
                    //console.log("Requesting New Twilio Access Token...");
                    PhoneWidget.addLog("Requesting New Twilio Access Token...");
                    $.getJSON('/phone/get-token')
                        .then(function (response) {
                            //console.log("Got a Twilio Access token.");
                            PhoneWidget.addLog("Got a Twilio Access token.");
                            device.updateToken(response.data.token);
                        })
                        .catch(function (err) {
                            PhoneWidget.addLog("Get Twilio Access token error. Reload page!");
                            console.log(err);
                            createNotify('Twilio Token error!', 'Could not get a token from server! Please reload page!', 'error');
                        });
                    return;
                }
                console.log('An error has occurred: ', twilioError);
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

    window.phoneWidget.device.initialize = {
        Init: Init
    }
})();