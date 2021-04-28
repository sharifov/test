<?php

/* @var $clientId string */
/* @var $token string */
/* @var $fromAgentPhone string */
/* @var $supportGeneralPhones array */
/* @var $use_browser_call_access bool */

use common\models\Call;
use yii\helpers\Url;
use yii\bootstrap4\Modal;

\frontend\assets\WebPhoneAsset::register($this);

?>

<?php Modal::begin([
    'id' => 'web-phone-redirect-agents-modal',
    'title' => 'Transfer Call',
    //'size' => 'modal-sm',
]);
?>
<?php Modal::end(); ?>

<?php
    $ajaxSaveCallUrl = Url::to(['phone/ajax-save-call']);
    $ajaxRedirectCallUrl = Url::to(['phone/ajax-call-redirect']);
    $ajaxCallRedirectGetAgents = Url::to(['phone/ajax-call-get-agents']);
    $ajaxCallTransferUrl = Url::to(['phone/ajax-call-transfer']);
    $ajaxCheckUserForCallUrl = Url::to(['phone/ajax-check-user-for-call']);
    $ajaxPhoneDialUrl = Url::to(['phone/ajax-phone-dial']);
    $ajaxBlackList = Url::to(['phone/check-black-phone']);
    $ajaxUnholdConferenceDoubleCall = Url::to(['/phone/ajax-unhold-conference-double-call']);
    $ajaxJoinToConferenceUrl = Url::to(['/phone/ajax-join-to-conference']);
    $ajaxHangupUrl = Url::to(['/phone/ajax-hangup']);
    $ajaxCreateCallUrl = Url::to(['/phone/ajax-create-call']);
    $ajaxGetPhoneListIdUrl = Url::to(['/phone/ajax-get-phone-list-id']);
    $ajaxWarmTransferToUserUrl = Url::to(['/phone/ajax-warm-transfer-to-user']);

    $conferenceBase = 0;
if (isset(Yii::$app->params['settings']['voip_conference_base'])) {
    $conferenceBase = Yii::$app->params['settings']['voip_conference_base'] ? 1 : 0;
}

    $callOutBackendSide = 0;
if (isset(Yii::$app->params['settings']['call_out_backend_side'])) {
    $callOutBackendSide = Yii::$app->params['settings']['call_out_backend_side'] ? 1 : 0;
}

    $csrf_param = Yii::$app->request->csrfParam;
    $csrf_token = Yii::$app->request->csrfToken;

?>
<script type="text/javascript">

    const ajaxCheckUserForCallUrl = '<?=$ajaxCheckUserForCallUrl?>';
    const ajaxSaveCallUrl = '<?=$ajaxSaveCallUrl?>';
    const ajaxCallRedirectUrl = '<?=$ajaxRedirectCallUrl?>';
    const ajaxCallTransferUrl = '<?=$ajaxCallTransferUrl?>';
    const ajaxWarmTransferToUserUrl = '<?=$ajaxWarmTransferToUserUrl?>';
    const ajaxCallRedirectGetAgents = '<?=$ajaxCallRedirectGetAgents?>';
    const ajaxPhoneDialUrl = '<?=$ajaxPhoneDialUrl?>';
    const ajaxBlackList = '<?=$ajaxBlackList?>';
    const ajaxUnholdConferenceDoubleCall = '<?= $ajaxUnholdConferenceDoubleCall ?>';
    const conferenceBase = parseInt('<?= $conferenceBase ?>');
    const ajaxJoinToConferenceUrl = '<?= $ajaxJoinToConferenceUrl ?>';
    const ajaxHangupUrl = '<?= $ajaxHangupUrl ?>';
    const ajaxCreateCallUrl = '<?= $ajaxCreateCallUrl ?>';
    const ajaxGetPhoneListIdUrl = '<?= $ajaxGetPhoneListIdUrl ?>';
    const callOutBackendSide = parseInt('<?= $callOutBackendSide ?>');

    const clientId = '<?=$clientId?>';

    const userId = '<?=Yii::$app->user->id?>';
    use_browser_call_access =  <?= $use_browser_call_access ? 'true' : 'false' ?>;
    call_access_log = [];

    if(window.localStorage.agent_tab_conn_state === undefined) {
        var agent_tab_conn_state = [{"user": userId, "items": []}];
        window.localStorage.setItem('agent_tab_conn_state', JSON.stringify(agent_tab_conn_state));
    }

    if(window.localStorage.agent_tab_conn_access === undefined) {
        var agent_tab_conn_access = [{"user": userId, "access": 1}];
        window.localStorage.setItem('agent_tab_conn_access', JSON.stringify(agent_tab_conn_access));
    }

    if(window.localStorage.lock === undefined) {
        window.localStorage.setItem('lock', 'false');
    }

    window.addEventListener('storage', function (event) {
        console.log(" localStorage_EVENT:" + JSON.stringify(event) + " ");
    });

    
    function clearAgentStatus(cn) {
        //if(window.localStorage.lock !== undefined && window.localStorage.lock !== 'true') {
            if (cn && cn.parameters && cn.status() && cn.status() !== 'closed') {
                //updateAgentStatus(cn, true);
            }
        //}
    }


    function updateAgentStatus(conn, check, status) {
        try {
        var agent_tab_conn_access = [];
        if (!use_browser_call_access) {
            if (window.localStorage.agent_tab_conn_access !== undefined) {
                agent_tab_conn_access = [{"user": userId, "access": 1}];
                window.localStorage.setItem('agent_tab_conn_access', JSON.stringify(agent_tab_conn_access));
            }
            return true;
        }

        var agent_access = 1;
        try {
            agent_tab_conn_access = JSON.parse(window.localStorage.agent_tab_conn_access);
        } catch (e) {
            agent_tab_conn_access = [{"user": userId, "access": 1}];
        }

        var key_index = 0;
        if (agent_tab_conn_access.length > 0) {
            for (var i = 0; i < agent_tab_conn_access.length; i++) {
                var agentData = agent_tab_conn_access[i];
                if (agentData && agentData.user && agentData.user === userId) {
                    agent_access = agentData.access;
                    key_index = i;
                }
            }
        }

        if (check) {
            if (agent_access > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            agent_tab_conn_access[key_index] = {"user": userId, "access": status}
        }
        window.localStorage.setItem('agent_tab_conn_access', JSON.stringify(agent_tab_conn_access));
        return (agent_access > 0);
        } catch (e) {
            return true;
        }
    }

    var webPhoneParams = {};
    var call_acc_sid = '';

   // "use strict";

    var device;
    window.connection;

    const speakerDevices = document.getElementsByClassName('speaker-devices');
    const ringtoneDevices = document.getElementsByClassName('ringtone-devices');

    if (!Array.prototype.inArray) {
        Array.prototype.inArray = function (element) {
            return this.indexOf(element) > -1;
        };
    }

    function hangup(callSid) {

        if (!callSid) {
            createNotify('Hangup', 'Not found Call Sid', 'error');
            return false;
        }

        let call = null;
        if (typeof PhoneWidgetCall === 'object') {
            call = PhoneWidgetCall.queues.active.one(callSid);
            if (call === null) {
                call = PhoneWidgetCall.queues.outgoing.one(callSid);
                if (call === null) {
                    createNotify('Hangup', 'Not found Call on Active or Outgoing Queue', 'error');
                    return false;
                }
            }
            if (!call.setHangupRequestState()) {
                return false;
            }
        }

        $.ajax({
            type: 'post',
            data: {
                'sid': callSid,
            },
            url: ajaxHangupUrl
        })
            .done(function(data) {
                if (data.error) {
                    createNotify('Hangup', data.message, 'error');

                    if (call !== null) {
                        call.unSetHangupRequestState();
                    }
                    return;
                }
                if (typeof data.result !== 'undefined' && typeof data.result.status !== 'undefined' && data.result.status === 'completed') {
                    PhoneWidgetCall.completeCall(callSid);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                createNotify('Hangup', 'Server error', 'error');
                if (call !== null) {
                    call.unSetHangupRequestState();
                }
            })
    }

    /*document.getElementById('get-devices').onclick = function () {
        navigator.mediaDevices.getUserMedia({audio: true})
            .then(updateAllDevices.bind(device));
    }*/

    for (var i = 0; i < speakerDevices.length; i++) {
        speakerDevices[i].addEventListener('change', function () {
            let selectedDevices = [].slice.call(speakerDevices[i].children)
                .filter(function (node) {
                    return node.selected;
                })
                .map(function (node) {
                    return node.getAttribute('data-id');
                });

            device.audio.speakerDevices.set(selectedDevices);
        });
    }

    if (ringtoneDevices) {
        for (var i = 0; i < ringtoneDevices.length; i++) {
            ringtoneDevices[i].addEventListener('change', function () {
                let selectedDevices = [].slice.call(ringtoneDevices[i].children)
                    .filter(function (node) {
                        return node.selected;
                    })
                    .map(function (node) {
                        return node.getAttribute('data-id');
                    });

                device.audio.ringtoneDevices.set(selectedDevices);
            });
        }
    }

    function bindVolumeIndicators(connection) {
        connection.on('volume', function (inputVolume, outputVolume) {
            if (typeof  PhoneWidgetCall === 'object') {
                PhoneWidgetCall.volumeIndicatorsChange(inputVolume, outputVolume)
            }
        });
    }

    function updateAllDevices() {
        for (var i = 0; i < speakerDevices.length; i++) {
            updateDevices(speakerDevices[i], device.audio.speakerDevices.get());
        }
        for (var i = 0; i < speakerDevices.length; i++) {
            updateDevices(ringtoneDevices[i], device.audio.ringtoneDevices.get());
        }

        // updateDevices(speakerDevices, );
        // updateDevices(ringtoneDevices, device);
    }

    // Update the available ringtone and speaker devices
    function updateDevices(selectEl, selectedDevices) {
        selectEl.innerHTML = '';

        device.audio.availableOutputDevices.forEach(function (device, id) {
            let isActive = (selectedDevices.size === 0 && id === 'default');
            selectedDevices.forEach(function (device) {
                if (device.deviceId === id) {
                    isActive = true;
                }
            });

            let option = document.createElement('option');
            option.label = device.label;0
            option.setAttribute('data-id', id);
            if (isActive) {
                option.setAttribute('selected', 'selected');
            }
            selectEl.appendChild(option);
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

    function renewTwDevice() {
        console.log(device.status());
        if (!device) {
            initDevice();
        } else {
            let status = device.status();
            let device_statuses = ['pending', 'closed', 'offline', 'error'];
            if (device_statuses.inArray(status)) {
                initDevice();
            }
        }
    }

    function acceptInternalCall(call)
    {
        if (call.isSentAcceptCallRequestState()) {
            return;
        }

        let callSid = call.data.callSid;
        let connection = incomingConnections.get(callSid);

        if (connection === null) {
            createNotify('Accept internal Call', 'Not found CallSid on Collections of IncomingConnections', 'error');
            return;
        }

        call.setAcceptCallRequestState();
        PhoneWidgetCall.callRequester.acceptInternalCall(call, connection);
    }

    function rejectInternalCall(call)
    {
        let callSid = call.data.callSid;
        let connection = incomingConnections.get(callSid);

        if (connection === null) {
            createNotify('Reject Internal Call', 'Not found CallSid on Collections of IncomingConnections', 'error');
            return;
        }

        call.setRejectInternalRequest();
        incomingConnections.remove(connection.parameters.CallSid);
        connection.reject();
        incomingSoundOff();
        $.get(ajaxSaveCallUrl + '?sid=' + connection.parameters.CallSid, function (r) {
            console.log(r);
        });
    }

    function initRedirectToAgent(callSid) {
        let modal = $('#web-phone-redirect-agents-modal');
        modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"> </i> Loading ...</div>');
        $('#web-phone-redirect-agents-modal-label').html('Transfer Call');
        $.post(ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
            .done(function(data) {
                modal.find('.modal-body').html(data);
            });
    }


    function saveDbCall(call_sid, call_from, call_to, call_status) {

        let project_id = webPhoneParams.project_id;
        let lead_id = webPhoneParams.lead_id;
        let case_id = webPhoneParams.case_id;

        console.info('saveDbCall - sid: ' + call_sid + ' : ' + call_from + ' : ' + call_to + ' : ' + call_status + ' : ' + project_id + ' : ' + lead_id + ' : ' + case_id);

        //console.warn(webPhoneParams); return false;

        if(call_sid) {
            $.ajax({
                type: 'post',
                data: {
                    '<?= $csrf_param ?>' : '<?= $csrf_token ?>',
                    'call_acc_sid': call_acc_sid,
                    'call_sid': call_sid,
                    'call_from': call_from,
                    'call_to': call_to,
                    'call_status': call_status,
                    'lead_id': lead_id,
                    'case_id': case_id,
                    'project_id': project_id
                },
                url: ajaxSaveCallUrl,
                success: function (data) {
                    console.info(data);
                    //$('#preloader').addClass('hidden');
                    //modal.find('.modal-body').html(data);
                    //modal.modal('show');
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }
    }

    // let currentConnection;

    var connectCallSid = null;

    function IncomingConnections() {
        this.connections = [];

        this.add = function (connection) {
            if (typeof connection.parameters === 'undefined') {
                console.error('Not found Parameters. Connection: ' + JSON.stringify(connection));
                return;
            }
            if (typeof connection.parameters.CallSid === 'undefined') {
                console.error('Not found CallSid. Connection: ' + JSON.stringify(connection));
                return;
            }
            this.connections.push(connection);
        };

        this.get = function (callSid) {
            let index = this.getIndex(callSid);
            if (index !== null) {
                return this.connections[index];
            }
            console.error('Not found Connection. CallSid: ' + callSid);
            return null;
        };

        this.getIndex = function (callSid) {
            let index = null;
            this.connections.forEach(function (connection, i) {
                if (connection.parameters.CallSid === callSid) {
                    index = i;
                    return false;
                }
            });
            return index;
        };

        this.remove = function (callSid) {
            let index = this.getIndex(callSid);
            if (index !== null) {
                this.connections.splice(index, 1);
            }
        };

        this.all = function () {
            console.log(JSON.stringify(this.connections));
        };
    }

    let incomingConnections = new IncomingConnections();

    function initDevice() {
        clearLog();
        log('Requesting Capability Token...');
        $.getJSON('/phone/get-token')
            .then(function (response) {
                let data = response.data;
                log('Got a token');
                // console.log('app_sid: ' + data.app_sid + 'account_sid: ' + data.account_sid);
                call_acc_sid = data.account_sid;
                //console.log('Token: ' + data.token);
                device = new Twilio.Device(data.token, {codecPreferences: ['opus', 'pcmu'], closeProtection: true, enableIceRestart: true, enableRingingState: false, debug: false, allowIncomingWhileBusy: false});

                device.audio.incoming(false);
                device.audio.outgoing(false);
                device.audio.disconnect(false);

                //console.log([data, device]);
                device.on('ready', function (device) {
                    log('Twilio.Device Ready!');
                    console.log('Twilio.Device Ready!');
                });

                device.on('error', function (error) {
                    freeDialButton();
                    updateAgentStatus(connection, false, 1);
                    log('Twilio.Device Error: ' + error.message);
                    incomingSoundOff();
                    if (typeof  error.twilioError !== 'undefined') {
                        createNotify(error.twilioError.description, error.twilioError.explanation, 'error');
                    } else {
                        createNotify('Twilio error', error.message, 'error');
                    }
                });

                device.on('connect', function (conn) {
                    freeDialButton();
                    incomingConnections.remove(conn.parameters.CallSid);

                    // currentConnection = conn;
                    //console.log("connect call: status: " + connection.status() + "\n" + 'connection: ' + JSON.stringify(connection) + "\n conn:" + JSON.stringify(conn));
                    //updateAgentStatus(connection, true);
                    let access = updateAgentStatus(connection, true, 0);
                    connection = conn;

                    console.log({"action":"connect", "cid":connection.parameters.CallSid, "access": access});
                    //log('Successfully established call!');
                    // console.warn(conn);
                    //console.info(conn.parameters);
                    //alert(clientId + ' - ' + conn.parameters.From);

                    bindVolumeIndicators(conn);
                    if (typeof PhoneWidgetCall === 'object') {
                        PhoneWidgetCall.updateConnection(conn);
                    }

                    connectCallSid = connection.parameters.CallSid;
                    setActiveConnection(conn);
                    incomingSoundOff();
                    soundConnect();
                });

                device.on('disconnect', function (conn) {
                    freeDialButton();
                    incomingConnections.remove(conn.parameters.CallSid);

                    //updateAgentStatus(connection, true);
                    let access = updateAgentStatus(connection, false, 1);
                    console.log({"action":"disconnect", "cid":conn.parameters.CallSid, "access": access});
                    connection = conn;
                    // createNotify('Call ended', 'Call ended', 'warning');
                    //console.warn(conn);
                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'completed');

                    if (connectCallSid === conn.parameters.CallSid) {
                        soundDisconnect();
                    }

                    incomingSoundOff();

                    window.sendCommandUpdatePhoneWidgetCurrentCalls(conn.parameters.CallSid, userId, window.generalLinePriorityIsEnabled);
                });

                // device.on('ringing', function (conn) {
                //     // console.log(conn.parameters);
                //     alert('Ringing 123');
                // });

                device.on('incoming', function (conn) {
                    incomingConnections.add(conn);
                    // console.log({"action":"incoming", "cid":conn.parameters.CallSid});
                    connection = conn;
                    incomingSoundOff();
                    if ("autoAccept" in connection.message && connection.message.autoAccept === 'false') {
                        if ("isInternal" in connection.message && connection.message.isInternal === 'true') {
                            let call = JSON.parse(atob(connection.message.requestCall), function (k, v) {
                                if (v === 'false') {
                                    return false;
                                } else if (v === 'true') {
                                    return true;
                                }
                                return v;
                            });
                            call.callSid = connection.parameters.CallSid;
                            PhoneWidgetCall.refreshCallStatus(call);
                        }
                        // startTimerSoundIncomingCall();
                    } else {
                        if (document.visibilityState === 'visible') {
                            conn.accept();
                        } else {
                            startTimerSoundIncomingCall();
                        }
                    }

                    /*var access =  updateAgentStatus(connection, true, 0);
                    console.log({"action":"incoming", "cid":conn.parameters.CallSid, "access": access});

                    if(!access) {
                        conn.reject();
                        return false;
                    }

                    if(connection && Object.prototype.hasOwnProperty.call(connection, "status")) {
                        //console.log("incoming call: status: " + connection.status() + "\n" + 'connection: ' + JSON.stringify(connection) + "\n conn:" + JSON.stringify(conn));
                        if (connection && ['open', 'ringing'].inArray(connection.status())) {
                            conn.reject();
                            return false;
                        }
                    }
                    connection = conn;
                    //updateAgentStatus(connection, true);
                    // log('Incoming connection from ' + conn.parameters.From);
                    createNotify('Incoming connection', 'Incoming connection from ' + conn.parameters.From, 'success');

                    var archEnemyPhoneNumber = tw_configs.client;
                    //document.getElementById('call-controls').style.display = 'none';

                    connection.accept();*/
                    /*
                    if (conn.parameters.From === archEnemyPhoneNumber || conn.parameters.From === 'client:' + archEnemyPhoneNumber) {
                        conn.reject();
                        log('It\'s your nemesis. Rejected call.');
                    } else {
                        // accept the incoming connection and start two-way audio
                        if(!confirm('Incoming call... Answer?')) {
                            conn.reject();
                        } else {
                            conn.accept();
                        }
                    }*/
                });


                device.on('cancel', function (conn) {
                    freeDialButton();
                    incomingConnections.remove(conn.parameters.CallSid);

                    //var  access = updateAgentStatus(conn, true, true);
                    let access = updateAgentStatus(connection, false, 1);
                    console.log({"action":"cancel", "cid":conn.parameters.CallSid, "access": access});
                    connection = conn;
                    log('Cancel call');
                    // createNotify('Cancel call', 'Cancel call', 'warning');
                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'canceled');
                    incomingSoundOff();
                });

                device.on('offline', function (device) {
                    console.log('Phone device: status Offline');
                    // createNotify('Status Offline', 'Phone device: status Offline', 'error');
                    incomingSoundOff();
                });

                //setClientNameUI(data.client);
                device.audio.on('deviceChange', updateAllDevices.bind(device));

                if (device.audio.isOutputSelectionSupported) {
                    $('#output-selection').show();
                } else {
                    $(document).find('.phone-widget__additional-bar .tabs__nav.tab-nav .wp-tab-device').hide();
                    $(document).find('.phone-widget__additional-bar .wp-devices-tab-log').addClass('active-tab');
                    $(document).find('.phone-widget__additional-bar #tab-device').hide();
                    $(document).find('.phone-widget__additional-bar #tab-logs').show();
                }
            })
            .catch(function (err) {
                updateAgentStatus(connection, false, 1);
                console.log(err);
                log('Could not get a token from server!');
                createNotify('Call Token error!', 'Could not get a token from server!', 'error');
            });
    }

    var incomingSoundInterval = null;

    function startTimerSoundIncomingCall() {
        incomingSoundInterval = setInterval(function () {
            incomingAudio.play();
            clearInterval(incomingSoundInterval);
        }, 2500);
    }

    function incomingSoundOff() {
        clearInterval(incomingSoundInterval);
        incomingAudio.pause();
    }

    //$(function () {
        /*console.log(tw_configs);
        initDevice();
        setInterval('renewTwDevice();', 50000);*/
    //});

    function getActiveConnection() {
        let activeConnection = window.localStorage.getItem('activeConnection');
        if (activeConnection) {
            return JSON.parse(activeConnection);
        }
        return null;
    }

    function setActiveConnection(conn) {
        window.localStorage.setItem('activeConnection', JSON.stringify({
            'CallSid': conn.parameters.CallSid,
            'To': connection.parameters.To
        }));
    }

    function getActiveConnectionCallSid() {
        let callSid = null;
        let activeConnection = getActiveConnection();
        if (activeConnection) {
            callSid = activeConnection.CallSid;
        }
        return callSid;
    }

    function webCall(phone_from, phone_to, project_id, lead_id, case_id, type, source_type_id) {

        /*var access =  updateAgentStatus(connection);
        if(!access) {
            alert('No access to call');
            return false;
        }*/

        if (conferenceBase && callOutBackendSide) {

            let createCallParams = {
                '<?= $csrf_param ?>' : '<?= $csrf_token ?>',
                'called': phone_to,
                'from': phone_from,
                'project_id': project_id,
                'lead_id': lead_id,
                'case_id': case_id,
                'source_type_id': source_type_id
            };

            $.post(ajaxCreateCallUrl, createCallParams, function(data) {
                if (data.error) {
                    var text = 'Error. Try again later';
                    if (data.message) {
                        text = data.message;
                    }
                    new PNotify({title: "Make call", type: "error", text: text, hide: true});
                } else {
                    console.log('webCall success');
                }
            }, 'json');

            return;
        }

        $.post(ajaxGetPhoneListIdUrl, {'phone': phone_from}, function(data) {
            if (data.error) {
                var text = 'Error. Try again later';
                if (data.message) {
                    text = data.message;
                }
                new PNotify({title: "Make call", type: "error", text: text, hide: true});
            } else {
                let params = {
                    'To': phone_to,
                    'FromAgentPhone': phone_from,
                    'c_project_id': project_id,
                    'lead_id': lead_id,
                    'case_id': case_id,
                    'c_type': type,
                    'c_user_id': userId,
                    'is_conference_call': conferenceBase,
                    'c_source_type_id': source_type_id,
                    'phone_list_id': data.phone_list_id
                };

                // console.log(params);
                webPhoneParams = params;

                if (device) {
                    console.log('Calling ' + params.To + '...');
                    // createNotify('Calling', 'Calling ' + params.To + '...', 'success');
                    connection = device.connect(params);
                    updateAgentStatus(connection, false, 0);
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

        if (conferenceBase && callOutBackendSide) {

            let createCallParams = {
                '<?= $csrf_param ?>' : '<?= $csrf_token ?>',
                'called': phone_to,
                'from': phone_from,
                'project_id': project_id,
                'lead_id': lead_id,
                'source_type_id': c_source_type_id,
            };

            $.post(ajaxCreateCallUrl, createCallParams, function(data) {
                if (data.error) {
                    var text = 'Error. Try again later';
                    if (data.message) {
                        text = data.message;
                    }
                    new PNotify({title: "Make call", type: "error", text: text, hide: true});
                } else {
                    console.log('webCall success');
                }
            }, 'json');

            return;
        }
        $.post(ajaxGetPhoneListIdUrl, {'phone': phone_from}, function(data) {
            if (data.error) {
                var text = 'Error. Try again later';
                if (data.message) {
                    text = data.message;
                }
                new PNotify({title: "Make call", type: "error", text: text, hide: true});
            } else {
                let params = {
                    'To': phone_to,
                    'FromAgentPhone': phone_from,
                    'c_project_id': project_id,
                    'lead_id': lead_id,
                    'c_type': type,
                    'c_user_id': userId,
                    'c_source_type_id': c_source_type_id,
                    'is_conference_call': conferenceBase,
                    'user_identity': window.userIdentity,
                    'phone_list_id': data.phone_list_id
                };
                webPhoneParams = params;

                if (device) {
                    console.log('Calling ' + params.To + '...');
                    // createNotify('Calling', 'Calling ' + params.To + '...', 'success');
                    connection = device.connect(params);
                    updateAgentStatus(connection, false, 0);
                }
            }
        }, 'json');

    }

</script>

<?php


$js = <<<JS
    
    $(document).on('click', '.btn-transfer', function(e) {
        e.preventDefault();
        
        let obj = $(e.target);
        let objType  = obj.data('type');
        let objValue = obj.data('value');
        
        obj.attr('disabled', true);
        
        let modal = $('#web-phone-redirect-agents-modal');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        
        let callSid = $(this).attr('data-call-sid');
        
        if (!callSid) {
            new PNotify({title: "Transfer call", type: "error", text: "Not found Call SID!", hide: true});
            return false;
        }
        
        if (!(objValue && objType)) {
            new PNotify({title: "Transfer call", type: "error", text: "Please try again after some seconds", hide: true});
            return false;
        }
        
        $.ajax({
            type: 'post',
            data: {
                'sid': callSid,
                'id': objValue,
                'type': objType
            },
            url: ajaxCallTransferUrl,
            success: function (data) {
                if (data.error) {
                    alert(data.message);
                }
                modal.modal('hide').find('.modal-body').html('');
            },
            error: function (error) {
                console.error(error);
                modal.modal('hide').find('.modal-body').html('');
            }
        });
    });

    $(document).on('click', '.btn-warm-transfer-to-user', function(e) {
        e.preventDefault();
        
        let obj = $(e.target);
        let userId  = obj.data('user-id');
        let callSid = obj.data('call-sid');
        
        obj.attr('disabled', true);
        
        let modal = $('#web-phone-redirect-agents-modal');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        if (!callSid) {
            new PNotify({title: "Transfer call", type: "error", text: "Not found Call SID!", hide: true});
            return false;
        }

        $.ajax({
            type: 'post',
            data: {
                'callSid': callSid,
                'userId': userId
            },
            url: ajaxWarmTransferToUserUrl,
            success: function (data) {
                if (data.error) {
                    alert(data.message);
                }
                modal.modal('hide').find('.modal-body').html('');
            },
            error: function (error) {
                console.error(error);
                modal.modal('hide').find('.modal-body').html('');
            }
        });
    });

    $(document).on('click',  '.btn-transfer-number',  function (e) {
        e.preventDefault();
        let obj = $(e.target);
        let objType  = obj.data('type');
        let objValue = obj.data('value');
        
        obj.attr('disabled', true);
        
        let callSid = $(this).attr('data-call-sid');
        let to = null;
        let activeConnection = getActiveConnection();
        if (activeConnection) {
            // callSid = activeConnection.CallSid;
            to = activeConnection.To;
        }
        
        if (!callSid) {
            new PNotify({title: "Transfer call", type: "error", text: "Not found active Connection CallSid", hide: true});
            return false;
        }
        
        if (objValue.length < 2) {
            console.error('Error call forward param TO');
            return false;
        }
        
        let modal = $('#web-phone-redirect-agents-modal');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        // connection.accept();
        
        $.ajax({
            type: 'post',
            data: {
                'sid': callSid,
                'type': objType,
                'from': to,
                'to': objValue,
            },
            url: ajaxCallRedirectUrl,
            success: function (data) {
                // updateAgentStatus(connection, false, 1);
                //console.log(data);
                if (data.error) {
                    alert(data.message);
                }
                modal.modal('hide').find('.modal-body').html('');
            },
            error: function (error) {
                console.error(error);
                modal.modal('hide').find('.modal-body').html('');
            }
        });
        
    });
        
    $(document).on('click', '.btn-make-call', function(e) {
        e.preventDefault();
        
        $.post(ajaxCheckUserForCallUrl, {user_id: userId}, function(data) {
            
            if(data && data.is_ready) {
                let phone_to = $('#call-to-number').val();
                let phone_from = $('#call-from-number').val();
                
                let project_id = $('#call-project-id').val();
                let lead_id = $('#call-lead-id').val();
                let case_id = $('#call-case-id').val();
                let source_type_id = $('#call-source-type-id').val();
                
                $('#web-phone-dial-modal').modal('hide');
                //alert(phone_from + ' - ' + phone_to);

                $.post(ajaxBlackList, {phone: phone_to}, function(data) {
                    if (data.success) {
                        webCall(phone_from, phone_to, project_id, lead_id, case_id, 'web-call', source_type_id);        
                    } else {
                        var text = 'Error. Try again later';
                        if (data.message) {
                            text = data.message;
                        }
                        new PNotify({title: "Make call", type: "error", text: text, hide: true});
                    }
                }, 'json');
                
            } else {
                alert('You have active call');
                return false;
            }
        }, 'json');
        
    });
    
    initDevice();

JS;
$muteJs = <<<JS
 function muteEvent(data)
     {
         if (typeof PhoneWidgetCall !== 'object') {
            return;
         }
                  
        let call = PhoneWidgetCall.queues.active.one(data.call.sid);
        if (call === null) {
            return;
        }
        if (data.command === 'mute') {
            call.mute();
        } else if (data.command === 'unmute') {
            call.unMute();
        }
     }
JS;

if (Yii::$app->controller->module->id != 'user-management') {
    $this->registerJs($js, \yii\web\View::POS_READY);
    $this->registerJs($muteJs, \yii\web\View::POS_END);
}
