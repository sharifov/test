<?php

/* @var $clientId string */
/* @var $this \yii\web\View */

use yii\helpers\Url;

/* @var $token string */
/* @var $fromAgentPhone string */
/* @var $supportGeneralPhones array */
/* @var $use_browser_call_access bool */
/* @var $this \yii\web\View */

\frontend\assets\WebPhoneAsset::register($this);
?>

<?= $this->render('partial/_phone_widget', [
	'token' => $token
]) ?>
<?= $this->render('partial/_phone_widget_icon') ?>

<?php
$ajaxSaveCallUrl = Url::to(['phone/ajax-save-call']);
$ajaxRedirectCallUrl = Url::to(['phone/ajax-call-redirect']);
$ajaxCallRedirectGetAgents = Url::to(['phone/ajax-call-get-agents']);
$ajaxCallTransferUrl = Url::to(['phone/ajax-call-transfer']);
$ajaxCheckUserForCallUrl = Url::to(['phone/ajax-check-user-for-call']);
$ajaxPhoneDialUrl = Url::to(['phone/ajax-phone-dial']);
$ajaxBlackList = Url::to(['phone/check-black-phone']);
$phoneFrom = (Yii::$app->user->identity->getFirstUserProjectParam()->one())->getPhoneList()->one()->pl_phone_number;
$userProjectId = (Yii::$app->user->identity->getFirstUserProjectParam()->one())->upp_project_id;
?>

<script type="text/javascript">

    const ajaxCheckUserForCallUrl = '<?=$ajaxCheckUserForCallUrl?>';
    const ajaxSaveCallUrl = '<?=$ajaxSaveCallUrl?>';
    const ajaxCallRedirectUrl = '<?=$ajaxRedirectCallUrl?>';
    const ajaxCallTransferUrl = '<?=$ajaxCallTransferUrl?>';
    const ajaxCallRedirectGetAgents = '<?=$ajaxCallRedirectGetAgents?>';
    const ajaxPhoneDialUrl = '<?=$ajaxPhoneDialUrl?>';
    const ajaxBlackList = '<?=$ajaxBlackList?>';

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

    function createNotify(title, message, type) {
        new PNotify({
            title: title,
            type: type,
            text: message,
            icon: true,
            hide: true,
            delay: 3000,
            mouse_reset: false
        });
    }

    var webPhoneParams = {};
    var call_acc_sid = '';

    // "use strict";

    var device;
    var connection;

    const speakerDevices = document.getElementById('speaker-devices');
    const ringtoneDevices = document.getElementById('ringtone-devices');
    const outputVolumeBar = document.getElementById('output-volume');
    const inputVolumeBar = document.getElementById('input-volume');
    const volumeIndicators = document.getElementById('volume-indicators');

    if (!Array.prototype.inArray) {
        Array.prototype.inArray = function (element) {
            return this.indexOf(element) > -1;
        };
    }

    // Bind button to hangup call
    /* document.getElementById('button-hangup').onclick = function () {
        log('Hanging up...');
        if (device) {
            updateAgentStatus(connection, false, 1);
            device.disconnectAll();
        }
    }; */

    /*document.getElementById('get-devices').onclick = function () {
        navigator.mediaDevices.getUserMedia({audio: true})
            .then(updateAllDevices.bind(device));
    }*/

    /*speakerDevices.addEventListener('change', function () {
        let selectedDevices = [].slice.call(speakerDevices.children)
            .filter(function (node) {
                return node.selected;
            })
            .map(function (node) {
                return node.getAttribute('data-id');
            });

        device.audio.speakerDevices.set(selectedDevices);
    });*/

    /*ringtoneDevices.addEventListener('change', function () {
        let selectedDevices = [].slice.call(ringtoneDevices.children)
            .filter(function (node) {
                return node.selected;
            })
            .map(function (node) {
                return node.getAttribute('data-id');
            });

        device.audio.ringtoneDevices.set(selectedDevices);
    });*/


    function volumeIndicatorsChange(inputVolume, outputVolume) {
        let inputColor = 'red';
        if (inputVolume < .50) {
            inputColor = 'green';
        } else if (inputVolume < .75) {
            inputColor = 'yellow';
        }

        inputVolumeBar.style.width = Math.floor(inputVolume * 300) + 'px';
        inputVolumeBar.style.background = inputColor;

        let outputColor = 'red';
        if (outputVolume < .50) {
            outputColor = 'green';
        } else if (outputVolume < .75) {
            outputColor = 'yellow';
        }

        outputVolumeBar.style.width = Math.floor(outputVolume * 300) + 'px';
        outputVolumeBar.style.background = outputColor;
    }


    function bindVolumeIndicators(connection) {
        // connection.on('volume', function (inputVolume, outputVolume) {
        //     volumeIndicatorsChange(inputVolume, outputVolume);
        // });
    }

    function updateAllDevices() {
        //updateDevices(speakerDevices, device.audio.speakerDevices.get());
        //updateDevices(ringtoneDevices, device.audio.ringtoneDevices.get());

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
            option.label = device.label;
            option.setAttribute('data-id', id);
            if (isActive) {
                option.setAttribute('selected', 'selected');
            }
            selectEl.appendChild(option);
        });
    }

    // Activity log
    function log(message) {
        /*let logDiv = document.getElementById('call-log');
        logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
        logDiv.scrollTop = logDiv.scrollHeight;
        */
    }


    function clearLog() {
        /*
        let logDiv = document.getElementById('call-log');
        logDiv.innerHTML = '';
        logDiv.scrollTop = logDiv.scrollHeight;
         */
    }

    // Set the client name in the UI
    /*function setClientNameUI(clientName) {
        var div = document.getElementById('client-name');
        div.innerHTML = 'Your client name: <strong>' + clientName +
            '</strong>';
    }*/

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

    function sendNumberToCall(number)
    {
        if(connection) {
            connection.sendDigits(number);
            // console.log("digit:" + number + ' sent');
        }
        return false;
    }

    /*document.getElementById('button-answer').onclick = function () {
        console.log("button-answer: " + connection);

        if (connection) {
            connection.accept();
            //document.getElementById('call-controls2').style.display = 'none';
            $('#call-controls2').hide();
        }
    };*/

    /*document.getElementById('button-reject').onclick = function () {
        if (connection) {
            console.log("button-reject: " + JSON.stringify(connection.parameters));
            connection.reject();
            $.get(ajaxSaveCallUrl + '?sid=' + connection.parameters.CallSid + '&user_id=' + userId, function (r) {
                console.log(r);
            });
            //document.getElementById('call-controls2').style.display = 'none';
            $('#call-controls2').hide();
        }
    };*/


    function initRedirectToAgent() {

        if (connection && connection.parameters.CallSid) {
            let callSid = connection.parameters.CallSid;
            let modal = $('#web-phone-redirect-agents-modal');
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            $('#web-phone-redirect-agents-modal-label').html('Transfer Call');

            $.post(ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
                .done(function(data) {
                    modal.find('.modal-body').html(data);
                });
        } else {
            alert('Error: Not found Call connection or Call SID!');
        }
        return false;
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

                    /*if (typeof refreshCallBox === "function") {
                        var obj = {'id': data.c_id, 'status': data.c_call_status};
                        console.log(obj);
                        refreshCallBox(obj);
                    }*/
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }
    }


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
                device = new Twilio.Device(data.token, {codecPreferences: ['opus', 'pcmu'], closeProtection: true, enableIceRestart: true, enableRingingState: false, debug: false});

                //console.log([data, device]);
                device.on('ready', function (device) {
                    log('Twilio.Device Ready!');
                });

                device.on('error', function (error) {
                    updateAgentStatus(connection, false, 1);
                    log('Twilio.Device Error: ' + error.message);
                });

                device.on('connect', function (conn) {
                    //console.log("connect call: status: " + connection.status() + "\n" + 'connection: ' + JSON.stringify(connection) + "\n conn:" + JSON.stringify(conn));
                    //updateAgentStatus(connection, true);
                    let access = updateAgentStatus(connection, true, 0);
                    connection = conn;
                    console.log({"action":"connect", "cid":connection.parameters.CallSid, "access": access});
                    //log('Successfully established call!');
                    // console.warn(conn);
                    //console.info(conn.parameters);
                    //alert(clientId + ' - ' + conn.parameters.From);
                    $('#btn-group-id-hangup').show();
                    $('#btn-mute-microphone').html('<i class="fa fa-microphone"></i> Mute').removeClass('btn-warning').addClass('btn-success');
                    $('#btn-group-id-mute').show();

                    if (conn.parameters.From === undefined) {
                        $('#btn-group-id-redirect').show();
                    } else {
                        $('#btn-group-id-redirect').show();
                    }

                    // volumeIndicators.style.display = 'block';
                    bindVolumeIndicators(conn);
                });

                device.on('disconnect', function (conn) {
                    //updateAgentStatus(connection, true);
                    let access = updateAgentStatus(connection, false, 1);
                    console.log({"action":"disconnect", "cid":conn.parameters.CallSid, "access": access});
                    connection = conn;
                    createNotify('Call ended', 'Call ended', 'warning');
                    //console.warn(conn);
                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'completed');

                    // $('#btn-group-id-hangup').hide();
                    // $('#btn-group-id-redirect').hide();
                    // $('#btn-group-id-mute').hide();
                    // volumeIndicators.style.display = 'none';
                    cleanPhones();
                });

                // device.on('ringing', function (conn) {
                //     // console.log(conn.parameters);
                //     alert('Ringing 123');
                // });

                device.on('incoming', function (conn) {
                    connection = conn;
                    $('#call-controls2').hide();
                    if (document.visibilityState === 'visible') {
                        conn.accept();
                    } else {
                        $('#call-controls2').show();
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
                    // document.getElementById('call-controls2').style.display = 'block';
                    $('#call-controls2').show();

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
                    //var  access = updateAgentStatus(conn, true, true);
                    let access = updateAgentStatus(connection, false, 1);
                    console.log({"action":"cancel", "cid":conn.parameters.CallSid, "access": access});
                    connection = conn;
                    log('Cancel call');
                    createNotify('Cancel call', 'Cancel call', 'warning');
                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'canceled');
                    $('#call-controls2').hide();
                    $('#btn-group-id-redirect').hide();
                });

                device.on('offline', function (device) {
                    console.log('Phone device: status Offline');
                    // createNotify('Status Offline', 'Phone device: status Offline', 'error');
                });

                //setClientNameUI(data.client);
                device.audio.on('deviceChange', updateAllDevices.bind(device));

                if (device.audio.isOutputSelectionSupported) {
                    $('#output-selection').show();
                }
            })
            .catch(function (err) {
                updateAgentStatus(connection, false, 1);
                console.log(err);
                log('Could not get a token from server!');
                createNotify('Call Token error!', 'Could not get a token from server!', 'error');
            });
    }

    //$(function () {
    /*console.log(tw_configs);
	initDevice();
	setInterval('renewTwDevice();', 50000);*/
    //});


    function webCall(phone_from, phone_to, project_id, lead_id, case_id, type) {

        /*var access =  updateAgentStatus(connection);
        if(!access) {
            alert('No access to call');
            return false;
        }*/

        let params = {'To': phone_to, 'FromAgentPhone': phone_from, 'project_id': project_id, 'lead_id': lead_id, 'case_id': case_id, 'c_type': type, 'c_user_id': userId};
        webPhoneParams = params;

        if (device) {
            $('#web-call-from-number').text(params.FromAgentPhone);
            $('#web-call-to-number').text(params.To);

            console.log('Calling ' + params.To + '...');
            createNotify('Calling', 'Calling ' + params.To + '...', 'success');
            updateAgentStatus(connection, false, 0);
            connection = device.connect(params);
            $('#btn-group-id-redirect').hide();
        }
    }

    function webCallLeadRedial(phone_from, phone_to, project_id, lead_id, type, c_source_type_id) {

        let params = {
            'To': phone_to,
            'FromAgentPhone': phone_from,
            'project_id': project_id,
            'lead_id': lead_id,
            'c_type': type,
            'c_user_id': userId,
            'c_source_type_id': c_source_type_id
        };
        webPhoneParams = params;

        if (device) {
            $('#web-call-from-number').text(params.FromAgentPhone);
            $('#web-call-to-number').text(params.To);

            console.log('Calling ' + params.To + '...');
            createNotify('Calling', 'Calling ' + params.To + '...', 'success');
            updateAgentStatus(connection, false, 0);
            connection = device.connect(params);
            // $('#btn-group-id-redirect').hide();
        }

    }

    function cleanPhones() {
        // $('#web-call-from-number').text('');
        // $('#web-call-to-number').text('');
    }
</script>

<?php

$js = <<<JS
    $(document).on('click', '#btn-make-call', function(e) {
        e.preventDefault();
        
        $.post(ajaxCheckUserForCallUrl, {user_id: userId}, function(data) {
            
            if(data && data.is_ready) {
                let phone_to = '+'+$('#call-pane__dial-number').val();
                let phone_from = '{$phoneFrom}';
                
                let project_id = '{$userProjectId}';
                // let lead_id = $('#call-lead-id').val();
                // let case_id = $('#call-case-id').val();
                
                console.log(phone_to);
                console.log(phone_from);
                
                // $('#web-phone-dial-modal').modal('hide');
                //alert(phone_from + ' - ' + phone_to);
                //webPhoneWidget.slideDown();
                //$('.fabs2').hide();
                
                $.post(ajaxBlackList, {phone: phone_to}, function(data) {
                    if (data.success) {
                        webCall(phone_from, phone_to, project_id, null, null, 'web-call');        
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
$this->registerJs($js);



