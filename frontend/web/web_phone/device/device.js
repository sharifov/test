(function () {
    function Init() {
        const speakerDevices = document.getElementById("speaker-devices");
        const ringtoneDevices = document.getElementById("ringtone-devices");

        speakerDevices.addEventListener("change", updateOutputDevice);
        ringtoneDevices.addEventListener("change", updateRingtoneDevice);

        function updateOutputDevice() {
            const selectedDevices = Array.from(speakerDevices.children)
                .filter((node) => node.selected)
                .map((node) => node.getAttribute("data-id"));

            window.TwilioDevice.audio.speakerDevices.set(selectedDevices);
        }

        function updateRingtoneDevice() {
            const selectedDevices = Array.from(ringtoneDevices.children)
                .filter((node) => node.selected)
                .map((node) => node.getAttribute("data-id"));

            window.TwilioDevice.audio.ringtoneDevices.set(selectedDevices);
        }

        function updateDevices(selectEl, selectedDevices) {
            selectEl.innerHTML = '';

            window.TwilioDevice.audio.availableOutputDevices.forEach(function (device, id) {
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

        function updateAllAudioDevices() {
            if (window.TwilioDevice) {
                updateDevices(speakerDevices, window.TwilioDevice.audio.speakerDevices.get());
                updateDevices(ringtoneDevices, window.TwilioDevice.audio.ringtoneDevices.get());
            }
        }

        function bindVolumeIndicators(call) {
            call.on("volume", function (inputVolume, outputVolume) {
                PhoneWidgetCall.volumeIndicatorsChange(inputVolume, outputVolume)
            });
        }

        function log(message) {
            let msg = '<p>&gt;&nbsp;' + message + '</p>';
            let logDivWidget = $('.logs-block');
            if (logDivWidget) {
                logDivWidget.append(msg);
                logDivWidget.animate({scrollTop: logDivWidget.prop("scrollHeight")}, 1000);
            }
        }

        function clearLog() {
            let logDivWidget = $('.logs-block');
            if (logDivWidget) {
                logDivWidget.html('');
                logDivWidget.animate({scrollTop: logDivWidget.prop("scrollHeight")}, 1000);
            }
        }

        function startupTwilioClient() {
            console.log("Requesting Twilio Access Token...");
            log("Requesting Twilio Access Token...");
            $.getJSON('/phone/get-token')
                .then(function (response) {
                    console.log("Got a Twilio Access token.");
                    log("Got a Twilio Access token.");
                    initDevice(response.data.token);
                })
                .catch(function (err) {
                    console.log(err);
                    createNotify('Call Token error!', 'Could not get a token from server!', 'error');
                });
        }

        function initDevice(token) {
            console.log("Init Twilio Device...");
            log("Init Twilio Device...");
            window.TwilioDevice = new Twilio.Device(token, {
                logLevel: 1,
                //edge: 'ashburn',
                closeProtection: true,
                codecPreferences: ["opus", "pcmu"]
            });
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

            window.TwilioDevice.on('registering', function () {
                console.log("Twilio.Device Registering...");
                log("Twilio.Device Registering...");
            });

            window.TwilioDevice.on("registered", function () {
                console.log("Twilio.Device Ready!");
                log("Twilio.Device Ready!");
                PhoneWidgetCall.getDeviceStatus().register();
            });

            window.TwilioDevice.on('unregistered', function () {
                console.log("Twilio.Device unregistered!");
                log("Twilio.Device unregistered!");
                PhoneWidgetCall.incomingSoundOff();
                window.TwilioCall = null;
                PhoneWidgetCall.getDeviceStatus().unregister();
            });

            window.TwilioDevice.on("incoming", call => {
                PhoneWidgetCall.incomingTwilioCalls.add(call);
                window.TwilioCall = call;
                PhoneWidgetCall.incomingSoundOff();

                call.on('accept', call => {
                    PhoneWidgetCall.incomingTwilioCalls.remove(call.parameters.CallSid);
                    window.TwilioCall = call;

                    console.log('The incoming call was accepted.');

                    PhoneWidgetCall.freeDialButton();
                    PhoneWidgetCall.setActiveCall(call);
                    PhoneWidgetCall.setActiveCallSid(call.parameters.CallSid);
                    PhoneWidgetCall.incomingSoundOff();

                    bindVolumeIndicators(call);
                    PhoneWidgetCall.soundConnect();
                });
                call.on('cancel', () => {
                    console.log('The call has been canceled.');
                    PhoneWidgetCall.freeDialButton();
                    if (window.TwilioCall) {
                        PhoneWidgetCall.incomingTwilioCalls.remove(window.TwilioCall.parameters.CallSid);
                    }
                    PhoneWidgetCall.incomingSoundOff();
                });
                call.on('disconnect', call => {
                    console.log('The call has been disconnected.');
                    PhoneWidgetCall.freeDialButton();
                    PhoneWidgetCall.incomingTwilioCalls.remove(call.parameters.CallSid);
                    window.TwilioCall = call;
                    // will remove after move device to one tab
                    if (call.parameters.CallSid === PhoneWidgetCall.getActiveCallSid()) {
                        PhoneWidgetCall.soundDisconnect();
                        PhoneWidgetCall.removeActiveCallSid();
                    }
                    PhoneWidgetCall.incomingSoundOff();
                    window.sendCommandUpdatePhoneWidgetCurrentCalls(call.parameters.CallSid, window.userId, window.generalLinePriorityIsEnabled);
                });
                call.on('error', (error) => {
                    console.log('An error has occurred: ', error);
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
                        PhoneWidgetCall.refreshCallStatus(callObj);
                    }
                } else {
                    // if (document.visibilityState === 'visible') {
                    call.accept();
                    console.log("Accepted incoming call.");
                    // } else {
                    //     PhoneWidgetCall.startTimerSoundIncomingCall();
                    // }
                }
            });

            window.TwilioDevice.on('error', (twilioError, call) => {
                console.log('An error has occurred: ', twilioError);
                if (twilioError.code === 20104) {
                    console.log('Twilio JWT Token Expired');
                    log('Twilio JWT Token Expired');
                    console.log("Requesting New Twilio Access Token...");
                    log("Requesting New Twilio Access Token...");
                    $.getJSON('/phone/get-token')
                        .then(function (response) {
                            console.log("Got a Twilio Access token.");
                            log("Got a Twilio Access token.");
                            window.TwilioDevice.updateToken(response.data.token);
                        })
                        .catch(function (err) {
                            console.log(err);
                            createNotify('Refresh Twilio Token!', 'Could not get a token from server!', 'error');
                        });
                    return;
                }
                PhoneWidgetCall.freeDialButton();
                log('Twilio.Device Error: ' + twilioError.message);
                PhoneWidgetCall.incomingSoundOff();
                createNotify(twilioError.description, twilioError.explanation, 'error');
            });

            window.TwilioDevice.audio.on("deviceChange", updateAllAudioDevices.bind(window.TwilioDevice));

            if (window.TwilioDevice.audio.isOutputSelectionSupported) {
                $('#output-selection').show();
            } else {
                $(document).find('.phone-widget__additional-bar .tabs__nav.tab-nav .wp-tab-device').hide();
                $(document).find('.phone-widget__additional-bar .wp-devices-tab-log').addClass('active-tab');
                $(document).find('.phone-widget__additional-bar #tab-device').hide();
                $(document).find('.phone-widget__additional-bar #tab-logs').show();
            }

            window.TwilioDevice.register();
        }

        startupTwilioClient();
    }

    window.phoneWidget.device.initialize = {
        Init: Init
    }
})();