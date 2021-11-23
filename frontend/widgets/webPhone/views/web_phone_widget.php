<?php

use common\models\Call;
use yii\bootstrap4\Modal;
use yii\helpers\Url;

/** @var int $userId */

\frontend\widgets\newWebPhone\NewWebPhoneAsset::register($this);
\frontend\widgets\webPhone\TwilioAsset::register($this);

Modal::begin([
    'id' => 'web-phone-redirect-agents-modal',
    'title' => 'Transfer Call',
    //'size' => 'modal-sm',
]);
Modal::end();


$csrf_param = Yii::$app->request->csrfParam;
$csrf_token = Yii::$app->request->csrfToken;

$ajaxCallRedirectGetAgents = Url::to(['phone/ajax-call-get-agents']);
$ajaxCheckUserForCallUrl = Url::to(['phone/ajax-check-user-for-call']);
$ajaxBlackList = Url::to(['phone/check-black-phone']);
$ajaxUnholdConferenceDoubleCall = Url::to(['/phone/ajax-unhold-conference-double-call']);
$ajaxJoinToConferenceUrl = Url::to(['/phone/ajax-join-to-conference']);
$ajaxHangupUrl = Url::to(['/phone/ajax-hangup']);
$ajaxCreateCallUrl = Url::to(['/phone/ajax-create-call']);
$ajaxGetPhoneListIdUrl = Url::to(['/phone/ajax-get-phone-list-id']);
$redialSourceType = Call::SOURCE_REDIAL_CALL;
$leadViewPageShortUrl = Url::to(['/lead/view'], true);


$callOutBackendSide = 0;
if (isset(Yii::$app->params['settings']['call_out_backend_side'])) {
    $callOutBackendSide = Yii::$app->params['settings']['call_out_backend_side'] ? 1 : 0;
}

$js = <<<JS
    const ajaxCheckUserForCallUrl = '{$ajaxCheckUserForCallUrl}';
    const ajaxCallRedirectGetAgents = '{$ajaxCallRedirectGetAgents}';
    const ajaxBlackList = '{$ajaxBlackList}';
    const ajaxUnholdConferenceDoubleCall = '{$ajaxUnholdConferenceDoubleCall}';
    const ajaxJoinToConferenceUrl = '{$ajaxJoinToConferenceUrl}';
    const ajaxHangupUrl = '{$ajaxHangupUrl}';
    const ajaxCreateCallUrl = '{$ajaxCreateCallUrl}';
    const ajaxGetPhoneListIdUrl = '{$ajaxGetPhoneListIdUrl}';
    const callOutBackendSide = parseInt('{$callOutBackendSide}');
    const redialSourceType = parseInt('{$redialSourceType}');
    const leadViewPageShortUrl = '{$leadViewPageShortUrl}';
    
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

    // if (!Array.prototype.inArray) {
    //     Array.prototype.inArray = function (element) {
    //         return this.indexOf(element) > -1;
    //     };
    // }

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
            logDivWidget.animate({ scrollTop: logDivWidget.prop("scrollHeight")}, 1000);
        }
    }

    function clearLog() {
        let logDivWidget = $('.logs-block');
        if (logDivWidget) {
            logDivWidget.html('');
            logDivWidget.animate({ scrollTop: logDivWidget.prop("scrollHeight")}, 1000);
        }
    }

    function IncomingTwilioCalls() {
        this.calls = [];

        this.add = function (call) {
            if (typeof call.parameters === 'undefined') {
                console.error('Not found Parameters. Call: ' + JSON.stringify(call));
                return;
            }
            if (typeof call.parameters.CallSid === 'undefined') {
                console.error('Not found CallSid. Call: ' + JSON.stringify(call));
                return;
            }
            this.calls.push(call);
        };

        this.get = function (callSid) {
            let index = this.getIndex(callSid);
            if (index !== null) {
                return this.calls[index];
            }
            console.error('Not found Call. CallSid: ' + callSid);
            return null;
        };

        this.getIndex = function (callSid) {
            let index = null;
            this.calls.forEach(function (call, i) {
                if (call.parameters.CallSid === callSid) {
                    index = i;
                    return false;
                }
            });
            return index;
        };

        this.remove = function (callSid) {
            let index = this.getIndex(callSid);
            if (index !== null) {
                this.calls.splice(index, 1);
            }
        };

        this.all = function () {
            console.log(JSON.stringify(this.calls));
        };
    }

    window.incomingTwilioCalls = new IncomingTwilioCalls();
    
    function startupTwilioClient() {
        console.log("Requesting Twilio Access Token...");
        $.getJSON('/phone/get-token')
            .then(function (response) {
                console.log("Got a Twilio Access token.");
                initDevice(response.data.token);
            })
            .catch(function (err) {
                console.log(err);
                createNotify('Call Token error!', 'Could not get a token from server!', 'error');
            });
    }

    startupTwilioClient();

    function initDevice(token) {
            console.log("Init Twilio Device...");
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
            });
        
            window.TwilioDevice.on("registered", function () {
                console.log("Twilio.Device Ready!");
            });
            
            window.TwilioDevice.on('unregistered', function () {
                console.log("Twilio.Device unregistered!");
                PhoneWidgetCall.incomingSoundOff();
                window.TwilioCall = null;
            });
        
            window.TwilioDevice.on("incoming", call => {     
                window.incomingTwilioCalls.add(call);
                window.TwilioCall = call;
                PhoneWidgetCall.incomingSoundOff();
                
                call.on('accept', call => {
                    window.incomingTwilioCalls.remove(call.parameters.CallSid);
                    window.TwilioCall = call;
                    
                    console.log('The incoming call was accepted.');
                    freeDialButton();
                    
                    PhoneWidgetCall.setActiveCall(call);
                    PhoneWidgetCall.setActiveCallSid(call.parameters.CallSid);
                    PhoneWidgetCall.incomingSoundOff();
                    
                    bindVolumeIndicators(call);
                    soundConnect();
                });
                call.on('cancel', () => {
                    console.log('The call has been canceled.');
                    freeDialButton();
                    if (window.TwilioCall) {
                        window.incomingTwilioCalls.remove(window.TwilioCall.parameters.CallSid);
                    }
                    PhoneWidgetCall.incomingSoundOff();
                });
                call.on('disconnect', call => {
                    console.log('The call has been disconnected.');
                    freeDialButton();
                    window.incomingTwilioCalls.remove(call.parameters.CallSid);
                    window.TwilioCall = call;
                    // will remove after move device to one tab
                    if (call.parameters.CallSid === PhoneWidgetCall.getActiveCallSid()) {
                        soundDisconnect();
                        PhoneWidgetCall.removeActiveCallSid();
                    }
                    PhoneWidgetCall.incomingSoundOff();
                    window.sendCommandUpdatePhoneWidgetCurrentCalls(call.parameters.CallSid, userId, window.generalLinePriorityIsEnabled);
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
                    if (document.visibilityState === 'visible') {
                        call.accept();
                        console.log("Accepted incoming call.");
                    } else {
                        PhoneWidgetCall.startTimerSoundIncomingCall();
                    }
                }
            });
        
            window.TwilioDevice.on('error', (twilioError, call) => {
                 console.log('An error has occurred: ', twilioError);
                 if (twilioError.code === 20104) {
                    console.log('Twilio JWT Token Expired');
                    console.log("Requesting New Twilio Access Token...");
                     $.getJSON('/phone/get-token')
                        .then(function (response) {
                            console.log("Got a Twilio Access token.");
                            window.TwilioDevice.updateToken(response.data.token);
                        })
                        .catch(function (err) {
                            console.log(err);
                            createNotify('Refresh Twilio Token!', 'Could not get a token from server!', 'error');
                        });
                    return;
                }
                freeDialButton();
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
        
    function webCall(phone_from, phone_to, project_id, lead_id, case_id, type, source_type_id) {

        $.post(ajaxGetPhoneListIdUrl, {'phone': phone_from}, function(data) {
            if (data.error) {
                var text = 'Error. Try again later';
                if (data.message) {
                    text = data.message;
                }
                new PNotify({title: "Make call", type: "error", text: text, hide: true});
            } else {
                let params = {
                    params: {
                        'To': phone_to,
                        'FromAgentPhone': phone_from,
                        'c_project_id': project_id,
                        'lead_id': lead_id,
                        'case_id': case_id,
                        'c_type': type,
                        'c_user_id': userId,
                        'is_conference_call': 1,
                        'c_source_type_id': source_type_id,
                        'phone_list_id': data.phone_list_id
                    }
                };
                
                if (window.TwilioDevice) {
                    console.log('Calling ' + params.params.To + '...');
                    connection = window.TwilioDevice.connect(params);
                }
            }
        }, 'json');
    }

    function joinListen(call_sid) {
        joinConference('Listen', '<?= Call::SOURCE_LISTEN ?>', call_sid);
    }

    function joinCoach(call_sid) {
        joinConference('Coach', '<?= Call::SOURCE_COACH ?>', call_sid);
    }

    function joinBarge(call_sid) {
        joinConference('Barge', '<?= Call::SOURCE_BARGE ?>', call_sid);
    }

    function joinConference(source_type, source_type_id, call_sid) {
        // new PNotify({title: source_type, type: "success", text: 'Request', hide: true});
        $.ajax({
            type: 'post',
            data: {
                '<?= $csrf_param ?>' : '<?= $csrf_token ?>',
                'call_sid': call_sid,
                'source_type_id': source_type_id
            },
            url: ajaxJoinToConferenceUrl
        })
        .done(function (data) {
            if (data.error) {
                new PNotify({title: source_type, type: "error", text: data.message, hide: true});
            } else {
                // new PNotify({title: source_type, type: "success", text: 'Success', hide: true});
            }
        })
        .fail(function (error) {
            new PNotify({title: source_type, type: "error", text: "Server error", hide: true});
            console.error(error);
        })
        .always(function () {

        });
    }

    function webCallLeadRedial(phone_from, phone_to, project_id, lead_id, type, c_source_type_id) {
        $.post(ajaxGetPhoneListIdUrl, {'phone': phone_from}, function(data) {
            if (data.error) {
                var text = 'Error. Try again later';
                if (data.message) {
                    text = data.message;
                }
                new PNotify({title: "Make call", type: "error", text: text, hide: true});
            } else {
                let params = {
                    params: {
                        'To': phone_to,
                        'FromAgentPhone': phone_from,
                        'c_project_id': project_id,
                        'lead_id': lead_id,
                        'c_type': type,
                        'c_user_id': userId,
                        'c_source_type_id': c_source_type_id,
                        'is_conference_call': 1,
                        'user_identity': window.userIdentity,
                        'phone_list_id': data.phone_list_id
                    }
                };

                if (window.TwilioDevice) {
                    console.log('Calling ' + params.params.To + '...');
                    connection = window.TwilioDevice.connect(params);
                }
            }
        }, 'json');

    }

    function webCallLeadRedialPriority(redialCallInfo) {
        let params = {
            params: {
                'To': redialCallInfo.phoneTo,
                'FromAgentPhone': redialCallInfo.phoneFrom,
                'c_project_id': redialCallInfo.projectId,
                'lead_id': redialCallInfo.leadId,
                'c_type': 'web-call',
                'c_user_id': userId,
                'c_source_type_id': redialSourceType,
                'is_conference_call': 1,
                'user_identity': window.userIdentity,
                'phone_list_id': redialCallInfo.phoneListId,
                'is_redial_call': true
            }
        };

        console.log(params);
        if (window.TwilioDevice) {
            console.log('Calling ' + params.params.To + '...');
            connection = window.TwilioDevice.connect(params);
        }
    }
JS;

if (Yii::$app->controller->module->id != 'user-management') {
    $this->registerJs($js, \yii\web\View::POS_READY);
}
