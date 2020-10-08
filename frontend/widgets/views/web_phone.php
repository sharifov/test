<?php
/* @var $clientId string */
/* @var $token string */
/* @var $fromAgentPhone string */
/* @var $supportGeneralPhones array */
/* @var $use_browser_call_access bool */

use common\models\Call;
use sales\helpers\setting\SettingHelper;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use yii\helpers\Html;

\frontend\assets\WebPhoneAsset::register($this);

?>
<?php if (SettingHelper::isOriginalPhoneWidgetEnabled()): ?>
<div class="fabs2" style="<?=((isset($_COOKIE['web-phone-widget-close']) && $_COOKIE['web-phone-widget-close']) ? '' : 'display: none')?>">
    <a id="prime2" class="fab2"><i class="fa fa-phone"></i></a>
</div>

<div id="web-phone-widget">
    <?php if($token): ?>
        <div id="web-phone-token" style="display: none"><?=$token?></div>
            <table class="table" style="margin: 0; background-color: rgba(255,255,255,.3);">
                <tr>
                    <?php /*<td style="display: none"><i title="<?=$token?>">Token</i></td>*/?>
                    <td style="width: 100px"><i class="fa fa-user"></i> <span><?=$clientId?></span> <span style="display:none;" id="join-source-type"></span></td>
                    <td>From: <i class="fa fa-phone"></i> <span id="web-call-from-number"></span></td>
                    <td>To: <i class="fa fa-phone"></i> <span id="web-call-to-number"></span></td>
                    <td style="width: 120px">
                        <div class="text-right">
                            <?=Html::button('<i class="fa fa-building-o text-warning"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-send-digit'])?>
                            <?=Html::button('<i class="fa fa-angle-double-up"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-nin-max-webphone'])?>
                            <?=Html::button('<i class="fa fa-close"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-webphone-close'])?>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="table" style="margin: 0">
                <tr>
                    <td style="width: 250px">
                        <table id="volume-indicators" style="display: none">
                            <tr title="Mic Volume">
                                <td><i class="fa fa-microphone"></i> </td>
                                <td><div id="input-volume" style="width: 200px; height: 7px;"></div></td>
                            </tr>
                            <tr title="Speaker Volume">
                                <td><i class="fa fa-volume-down"></i> </td>
                                <td><div id="output-volume" style="width: 200px; height: 7px;"></div></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <div class="btn-group" id="btn-group-id-hangup" style="display:none;">
                            <?=Html::button('<i class="fa fa-close"></i> Hangup', ['class' => 'btn btn-sm btn-danger','id' => 'button-hangup'])?>
                        </div>

<!--                        <div class="btn-group dropup" style="display:none;" id="btn-group-id-forward">-->
<!--                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--                                <i class="fa fa-forward"></i> To Support <span class="caret"></span>-->
<!--                            </button>-->
<!--                            <ul class="dropdown-menu">-->
<!--                                --><?php //if($supportGeneralPhones): ?>
<!--                                    --><?php //foreach ($supportGeneralPhones AS $projectName => $projectPhone): ?>
<!--                                        <li>-->
<!--                                            <a href="#" class="btn-transfer" data-type="number" data-value="--><?php //=Html::encode($projectPhone)?><!--">--><?php //echo Html::encode($projectName) . ' ('.Html::encode($projectPhone).')';?><!--</a>-->
<!--                                        </li>-->
<!--                                    --><?php //endforeach; ?>
<!--                                --><?php //endif;?>
<!--                            </ul>-->
<!--                        </div>-->

                        <div class="btn-group" id="btn-group-id-redirect" style="display: none;">
                            <?=Html::button('<i class="fa fa-forward"></i> Transfer Call', ['id' => 'btn-show-transfer-call', 'class' => 'btn btn-sm btn-info'])?>
                        </div>


                        <?php /*=Html::button('<i class="fa fa-phone"></i> Call', ['class' => 'btn btn-xs btn-success', 'id' => 'button-call'])*/?>
                        <div id="call-controls2" style="display: none;">
                            <div class="btn-group">
                                <?=Html::button('<i class="fa fa-phone"></i> Answer', ['class' => 'btn btn-xs btn-success', 'id' => 'button-answer'])?>
                            </div>
                            <div class="btn-group">
                                <?=Html::button('<i class="fa fa-forward"></i> Reject', ['class' => 'btn btn-xs btn-danger','id' => 'button-reject'])?>
                            </div>
                        </div>

                        <div class="btn-group" id="btn-group-id-mute" style="display: none;">
                            <?= Html::button('<i class="fa fa-microphone"></i> Mute', ['id' => 'btn-mute-microphone', 'class' => 'btn btn-sm btn-success']) ?>
                        </div>

                        <div class="btn-group" id="btn-group-id-hold-call" style="display: none;">
                            <?= Html::button('<i class="fa fa-pause"></i> <span>On Hold</span>', ['class' => 'btn btn-sm btn-success btn-hold-call', 'data-mode' => 'unhold']) ?>
                        </div>
                    </td>
                </tr>
            </table>


            <div class="webphone-controls" id="controls">
                <table class="table">
                    <tr>
                        <td style="width: 250px">
                            <div id="info">
                                <?php /*<div id="client-name"></div>*/ ?>
                                <div id="output-selection">
                                    <label>Ringtone Devices</label>
                                    <select id="ringtone-devices" class="ringtone-devices" multiple></select>
                                    <label>Speaker Devices</label>
                                    <select id="speaker-devices" class="speaker-devices" multiple></select><br/>
                                    <?php /*<a id="get-devices">Seeing unknown devices?</a>*/?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <i>Logs:</i>
                            <div id="call-log"></div>
                        </td>
                    </tr>
                </table>
            </div>
    <?php else: ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Warning!</strong> WebCall token is empty.
        </div>
    <?php endif; ?>
</div>


<?php Modal::begin([
    'id' => 'web-phone-dial-modal',
    'title' => 'Phone Dial',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_LARGE
]); ?>
<?php Modal::end(); ?>


<?php Modal::begin([
    'id' => 'web-phone-send-digit-modal',
    'title' => 'Send digit',
    'size' => 'modal-sm',
]);
?>
    <div class="container container-digit" id="container-digit">
        <div id="output"></div>
        <div class="row">
            <div class="digit" id="one">1</div>
            <div class="digit" id="two">2</div>
            <div class="digit" id="three">3</div>
        </div>
        <div class="row">
            <div class="digit" id="four">4</div>
            <div class="digit" id="five">5</div>
            <div class="digit">6</div>
        </div>
        <div class="row">
            <div class="digit">7</div>
            <div class="digit">8</div>
            <div class="digit">9</div>
        </div>
        <div class="row">
            <div class="digit">*</div>
            <div class="digit">0</div>
            <div class="digit">#</div>
        </div>
        <div class="row">
            <i class="fa fa-eraser dig reset-digit" aria-hidden="true"></i>
        </div>
    </div>
<?php Modal::end(); ?>

<?php endif; ?>

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
    const ajaxCallRedirectGetAgents = '<?=$ajaxCallRedirectGetAgents?>';
    const ajaxPhoneDialUrl = '<?=$ajaxPhoneDialUrl?>';
    const ajaxBlackList = '<?=$ajaxBlackList?>';
    const ajaxUnholdConferenceDoubleCall = '<?= $ajaxUnholdConferenceDoubleCall ?>';
    const conferenceBase = parseInt('<?= $conferenceBase ?>');
    const ajaxJoinToConferenceUrl = '<?= $ajaxJoinToConferenceUrl ?>';
    const ajaxHangupUrl = '<?= $ajaxHangupUrl ?>';
    const ajaxCreateCallUrl = '<?= $ajaxCreateCallUrl ?>';
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
    var isJoinCall = false;

   // "use strict";

    var device;
    var joinConnection = null;
    window.connection;

    const speakerDevices = document.getElementsByClassName('speaker-devices');
    const ringtoneDevices = document.getElementsByClassName('ringtone-devices');
    const outputVolumeBar = document.getElementById('output-volume');
    const inputVolumeBar = document.getElementById('input-volume');
    const volumeIndicators = document.getElementById('volume-indicators');

    if (!Array.prototype.inArray) {
        Array.prototype.inArray = function (element) {
            return this.indexOf(element) > -1;
        };
    }

    // Bind button to hangup call
    let btnHangup = document.getElementById('button-hangup');
    if (btnHangup) {
        btnHangup.onclick = function () {
            log('Hanging up...');
            if (device) {
                let callSid = getActiveConnectionCallSid();
                hangup(callSid);
            } else {
                log('Device is null');
            }
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

        let oldActiveCallBtn = $(document).find('#button-hangup');
        if (oldActiveCallBtn.length > 0) {
            if (getActiveConnectionCallSid() !== callSid) {
                oldActiveCallBtn = null;
            } else {
                oldActiveCallBtn.prop('disabled', true);
            }
        } else {
            oldActiveCallBtn = null;
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
                    if (oldActiveCallBtn !== null) {
                        oldActiveCallBtn.prop('disabled', false);
                    }

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
                if (oldActiveCallBtn !== null) {
                    oldActiveCallBtn.prop('disabled', false);
                }

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

    function volumeIndicatorsChange(inputVolume, outputVolume) {
        let inputColor = 'red';
        if (inputVolume < .50) {
            inputColor = 'green';
        } else if (inputVolume < .75) {
            inputColor = 'yellow';
        }

        if (inputVolumeBar) {
            inputVolumeBar.style.width = Math.floor(inputVolume * 300) + 'px';
            inputVolumeBar.style.background = inputColor;
        }

        let outputColor = 'red';
        if (outputVolume < .50) {
            outputColor = 'green';
        } else if (outputVolume < .75) {
            outputColor = 'yellow';
        }

        if (outputVolumeBar) {
            outputVolumeBar.style.width = Math.floor(outputVolume * 300) + 'px';
            outputVolumeBar.style.background = outputColor;
        }
    }


    function bindVolumeIndicators(connection) {
        connection.on('volume', function (inputVolume, outputVolume) {
            volumeIndicatorsChange(inputVolume, outputVolume);
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

    // Activity log
    function log(message) {
        let msg = '<p>&gt;&nbsp;' + message + '</p>';
        let logDiv = document.getElementById('call-log');
        if (logDiv) {
            logDiv.innerHTML += msg;
            logDiv.scrollTop = logDiv.scrollHeight;
        }


        let logDivWidget = $('.logs-block');
        if (logDivWidget) {
            logDivWidget.append(msg);
            logDivWidget.animate({ scrollTop: logDivWidget.prop("scrollHeight")}, 1000);
        }
    }


    function clearLog() {
        let logDiv = document.getElementById('call-log');
        if (logDiv) {
            logDiv.innerHTML = '';
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        let logDivWidget = $('.logs-block');
        if (logDivWidget) {
            logDivWidget.html('');
            logDivWidget.animate({ scrollTop: logDivWidget.prop("scrollHeight")}, 1000);
        }
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

        callControls2Hide();

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
        callControls2Hide();
        incomingConnections.remove(connection.parameters.CallSid);
        connection.reject();
        incomingSoundOff();
        $.get(ajaxSaveCallUrl + '?sid=' + connection.parameters.CallSid, function (r) {
            console.log(r);
        });
    }

    let btnAnswer = document.getElementById('button-answer');
    if (btnAnswer) {
        btnAnswer.onclick = function () {
            let callSid = $('#button-answer').attr('data-call-sid');
            if (!callSid) {
                createNotify('Answer Call', 'CallSid is empty', 'error');
                return;
            }

            let connection = incomingConnections.get(callSid);

            if (connection === null) {
                createNotify('Answer Call', 'Not found CallSid on Collections of IncomingConnections', 'error');
                return;
            }

            console.log("button-answer: " + connection);

            connection.accept();
            callControls2Hide();
        };
    }

    let btnReject = document.getElementById('button-reject');
    if (btnReject) {
        btnReject.onclick = function () {
            let callSid = $('#button-reject').attr('data-call-sid');
            if (!callSid) {
                createNotify('Reject Call', 'CallSid is empty', 'error');
                return;
            }

            let connection = incomingConnections.get(callSid);

            if (connection === null) {
                createNotify('Reject Call', 'Not found CallSid on Collections of IncomingConnections', 'error');
                return;
            }

            incomingConnections.remove(connection.parameters.CallSid);
            console.log("button-reject: " + JSON.stringify(connection.parameters));
            connection.reject();
            incomingSoundOff();
            $.get(ajaxSaveCallUrl + '?sid=' + connection.parameters.CallSid, function (r) {
                console.log(r);
            });
            callControls2Hide();
        };
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

    function callControls2Show(callSid)
    {
        $('#call-controls2 #button-answer').attr('data-call-sid', callSid);
        $('#call-controls2 #button-reject').attr('data-call-sid', callSid);
        $('#call-controls2').show();
    }

    function callControls2Hide()
    {
        $('#call-controls2 #button-answer').attr('data-call-sid', '');
        $('#call-controls2 #button-reject').attr('data-call-sid', '');
        $('#call-controls2').hide();
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
                    $('#btn-group-id-hangup').show();
                    $('#button-hangup').attr('data-call-sid', connection.parameters.CallSid);
                    $('#button-hangup').prop('disabled', false);
                    $('#btn-mute-microphone').html('<i class="fa fa-microphone"></i> Mute').removeClass('btn-warning').addClass('btn-success');

                    let isJoin = false;
                    let sourceTypeId = null;
                    let isInternal = false;
                    let isConferenceCreator = false;
                    conn.customParameters.forEach(function(value, key) {
                        if (key === 'type_id' && value == '<?= Call::CALL_TYPE_JOIN ?>') {
                            isJoin = true;
                        }
                        if (key === 'source_type_id') {
                            sourceTypeId = value;
                        }
                        if (key === 'isInternal') {
                            isInternal = (value === 'true');
                        }
                        if (key === 'isConferenceCreator') {
                            isConferenceCreator = (value === 'true');
                        }
                    });

                    if (isJoin) {

                        $('#btn-group-id-hold-call').hide();
                        joinConnection = conn;
                        isJoinCall = true;
                        if (sourceTypeId == '<?= Call::SOURCE_LISTEN ?>') {
                            $('#join-source-type').html('Join: ' + 'Listen').show();
                        } else if (sourceTypeId == '<?= Call::SOURCE_COACH ?>') {
                            $('#join-source-type').html('Join: ' + 'Coach').show();
                            $('#btn-group-id-mute').show();
                        } else if (sourceTypeId == '<?= Call::SOURCE_BARGE ?>') {
                            $('#join-source-type').html('Join: ' + 'Barge').show();
                            $('#btn-group-id-mute').show();
                        }
                    } else {

                        if (typeof  PhoneWidgetCall === 'object') {
                            if (conferenceBase) {
                                if (!isInternal || (isInternal && isConferenceCreator)) {
                                    let btnHold = $('.btn-hold-call');
                                    btnHold.prop('disabled', false);
                                    btnHold.html('<i class="fa fa-pause"></i> <span>On Hold</span>');
                                    btnHold.data('mode', 'unhold');
                                    btnHold.attr('data-call-sid', connection.parameters.CallSid);
                                    $('#btn-group-id-hold-call').show();
                                }
                            }
                        }

                        joinConnection = null;
                        $('#web-call-from-number').text(conn.parameters.From);
                        $('#web-call-to-number').text(conn.parameters.To);
                        $('#btn-group-id-mute').show();
                        isJoinCall = false;
                        $('#btn-show-transfer-call').attr('data-call-sid', connection.parameters.CallSid);
                        if (conn.parameters.From === undefined) {
                            $('#btn-group-id-redirect').show();
                        } else {
                            $('#btn-group-id-redirect').show();
                        }

                        if (isInternal) {
                            $('#btn-group-id-redirect').hide();
                        }
                    }

                    if (volumeIndicators) {
                        volumeIndicators.style.display = 'block';
                    }
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

                    $('#btn-group-id-hangup').hide();
                    $('#btn-group-id-redirect').hide();
                    $('#btn-group-id-mute').hide();

                    $('#join-source-type').html();
                    $('#join-source-type').hide();
                    $('#btn-group-id-hold-call').hide();

                    if (volumeIndicators) {
                        volumeIndicators.style.display = 'none';
                    }
                    cleanPhones();

                    if (connectCallSid === conn.parameters.CallSid) {
                        soundDisconnect();
                    }

                    incomingSoundOff();

                    window.sendCommandUpdatePhoneWidgetCurrentCalls();
                });

                // device.on('ringing', function (conn) {
                //     // console.log(conn.parameters);
                //     alert('Ringing 123');
                // });

                device.on('incoming', function (conn) {
                    incomingConnections.add(conn);
                    // console.log({"action":"incoming", "cid":conn.parameters.CallSid});
                    connection = conn;
                    callControls2Hide();
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
                        } else {
                            callControls2Show(connection.parameters.CallSid);
                        }
                        // startTimerSoundIncomingCall();
                    } else {
                        if (document.visibilityState === 'visible') {
                            conn.accept();
                        } else {
                            callControls2Show(connection.parameters.CallSid);
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
                    freeDialButton();
                    incomingConnections.remove(conn.parameters.CallSid);

                    //var  access = updateAgentStatus(conn, true, true);
                    let access = updateAgentStatus(connection, false, 1);
                    console.log({"action":"cancel", "cid":conn.parameters.CallSid, "access": access});
                    connection = conn;
                    log('Cancel call');
                    // createNotify('Cancel call', 'Cancel call', 'warning');
                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'canceled');
                    callControls2Hide();
                    $('#btn-group-id-redirect').hide();
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

                window.localStorage.setItem('twilioDevice', JSON.stringify(device, function (key, value) {
                    if (typeof value === 'function') {
                        return value.toString();
                    } else {
                        return value;
                    }
                }));
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

        let params = {
            'To': phone_to,
            'FromAgentPhone': phone_from,
            'c_project_id': project_id,
            'lead_id': lead_id,
            'case_id': case_id,
            'c_type': type,
            'c_user_id': userId,
            'is_conference_call': conferenceBase,
            'c_source_type_id': source_type_id
        };


        // console.log(params);
        webPhoneParams = params;

        if (device) {
            $('#web-call-from-number').text(params.FromAgentPhone);
            $('#web-call-to-number').text(params.To);

            console.log('Calling ' + params.To + '...');
            // createNotify('Calling', 'Calling ' + params.To + '...', 'success');
            connection = device.connect(params);
            updateAgentStatus(connection, false, 0);
            $('#btn-group-id-redirect').hide();
        }
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

        let params = {
            'To': phone_to,
            'FromAgentPhone': phone_from,
            'c_project_id': project_id,
            'lead_id': lead_id,
            'c_type': type,
            'c_user_id': userId,
            'c_source_type_id': c_source_type_id,
            'is_conference_call': conferenceBase
        };
        webPhoneParams = params;

        if (device) {
            $('#web-call-from-number').text(params.FromAgentPhone);
            $('#web-call-to-number').text(params.To);

            console.log('Calling ' + params.To + '...');
            // createNotify('Calling', 'Calling ' + params.To + '...', 'success');
            connection = device.connect(params);
            updateAgentStatus(connection, false, 0);
           // $('#btn-group-id-redirect').hide();
        }

    }

    function cleanPhones() {
        $('#web-call-from-number').text('');
        $('#web-call-to-number').text('');
    }
</script>

<?php


$js = <<<JS

    const webPhoneWidget = $('#web-phone-widget');
    //setInterval('clearAgentStatus(connection)', 1000);

    $(document).on('click', '#btn-show-transfer-call', function(e) {
        e.preventDefault();
        let callSid = $(this).attr('data-call-sid');
        if (callSid) {
            initRedirectToAgent(callSid);   
        } else {
            new PNotify({title: "Transfer call", type: "error", text: "Not found Call SID!", hide: true});
        }
    });
    
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
    
        // if(connection && connection.parameters.CallSid) {
        //     updateAgentStatus(connection, false, 1);
        //    
        //     if(connection.status() !== 'open') {
        //         connection.accept();
        //     }
        //                
        //     if (objValue && objType) {
        //         $.ajax({
        //             type: 'post',
        //             data: {
        //                 'sid': connection.parameters.CallSid,
        //                 'id': objValue,
        //                 'type': objType
        //             },
        //             url: ajaxCallTransferUrl,
        //             success: function (data) {
        //                 if (data.error) {
        //                     alert(data.message);
        //                 }
        //                 modal.modal('hide').find('.modal-body').html('');
        //             },
        //             error: function (error) {
        //                 console.error(error);
        //                 modal.modal('hide').find('.modal-body').html('');
        //             }
        //         });
        //     } else {
        //         new PNotify({title: "Transfer call", type: "error", text: "Please try again after some seconds", hide: true});
        //     }
        //            
        // } else {
        //     alert('Error: Not found active connection or CallSid');
        // }
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
        
//        console.log(connection.parameters);
//        
//        if(connection && connection.parameters.CallSid) {
//            if(objValue.length < 2) {
//                console.error('Error call forward param TO');
//                return false;
//            }
//            
//            let modal = $('#web-phone-redirect-agents-modal');
//            modal.find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
//            // connection.accept();
//            
//            $.ajax({
//                type: 'post',
//                data: {
//                    'sid': connection.parameters.CallSid,
//                    'type': objType,
//                    'from': connection.parameters.To,
//                    'to': objValue,
//                },
//                url: ajaxCallRedirectUrl,
//                success: function (data) {
//                    // updateAgentStatus(connection, false, 1);
//                    //console.log(data);
//                    if (data.error) {
//                        alert(data.message);
//                    }
//                    modal.modal('hide').find('.modal-body').html('');
//                },
//                error: function (error) {
//                    console.error(error);
//                    modal.modal('hide').find('.modal-body').html('');
//                }
//            });
//        } else {
//            alert('Error: Not found active connection or CallSid');
//        }
    });


    $(".digit").on('click', function() {
        let num = ($(this).clone().children().remove().end().text());
        $("#output").append('<span>' + num.trim() + '</span>');
        sendNumberToCall(num.trim());
        //ion.sound.play("button_tiny");        
        soundNotification();
    });

    $('.reset-digit').on('click', function() {
        $('#output').html('');
    });
    
    $('#btn-send-digit').on('click', function() {
        $('#web-phone-send-digit-modal').modal();
    });
    
    $('#web-phone-send-digit-modal').on('hidden.bs.modal', function (e) {
        e.preventDefault();
        $('#output').html('');
    });
     
    $('#btn-nin-max-webphone').on('click', function() {
        let iTag = $(this).find('i');
        if(iTag.hasClass('fa-angle-double-down')) {
            iTag.removeClass('fa-angle-double-down').addClass('fa-angle-double-up');    
            $('.webphone-controls').slideUp();
        } else {
            iTag.removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
            $('.webphone-controls').slideDown();
        }
        //$(this).find('i').addClass('fa-angle-double-up');
    });
    
    $('#btn-webphone-close').on('click', function() {
        webPhoneWidget.slideUp('fast');
        $('.fabs2').show();
        setCookie('web-phone-widget-close', 1, {expires: 3600 * 24, path: "/"});
        //$(this).find('i').addClass('fa-angle-double-up');
    });
    
    $('#prime2').on('click', function() {
        webPhoneWidget.slideDown();
        $('.fabs2').hide();
        //deleteCookie('web-phone-widget-close');
        setCookie('web-phone-widget-close', '', {expires: -1, path: "/"});
    });
    
    $('.call-phone').on('click', function(e) {
        let phone_number = $(this).data('phone');
        let project_id = $(this).data('project-id');
        let lead_id = $(this).data('lead-id');
        let case_id = $(this).data('case-id');
        let source_type_id = $(this).data('source_type_id');
        //alert(phoneNumber);
        e.preventDefault();
        
        $('#web-phone-dial-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#web-phone-dial-modal').modal();
        
        $.post(ajaxPhoneDialUrl, {'phone_number': phone_number, 'project_id': project_id, 'lead_id': lead_id, 'case_id': case_id, 'source_type_id': source_type_id},
            function (data) {
                $('#web-phone-dial-modal .modal-body').html(data);
            }
        );
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
                webPhoneWidget.slideDown();
                $('.fabs2').hide();
                
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
    
    $(document).on('click', '#btn-mute-microphone', function(e) {
        let mute = $(this).html();
        if (mute === '<i class="fa fa-microphone"></i> Mute') {
            if (connection) {
                connection.mute(true);
                if (connection.isMuted()) {
                    if (typeof PhoneWidgetCall === 'object') { 
                        PhoneWidgetCall.panes.active.buttons.mute.mute();
                    }
                    $(this).html('<i class="fa fa-microphone"></i> Unmute').removeClass('btn-success').addClass('btn-warning');
                } else {
                    new PNotify({title: "Mute", type: "error", text: "Error", hide: true});
                }
            }
        } else {
            if (connection) {
                connection.mute(false);
                if (!connection.isMuted()) {
                    if (typeof PhoneWidgetCall === 'object') { 
                        PhoneWidgetCall.panes.active.buttons.mute.unMute();
                    }
                    $(this).html('<i class="fa fa-microphone"></i> Mute').removeClass('btn-warning').addClass('btn-success');
                } else {
                    new PNotify({title: "Unmute", type: "error", text: "Error", hide: true});
                }
            }
        }
    });
    
    webPhoneWidget.css({left:'50%', 'margin-left':'-' + (webPhoneWidget.width() / 2) + 'px'}); //.slideDown();
    initDevice();
    
     $(document).on('click', '.btn-hold-call', function(e) {
        if (!conferenceBase) {
           return;
        }
         
        e.preventDefault();
        let callSid = $(this).attr('data-call-sid');
        if (callSid) {
           let mode = $(this).attr('data-mode');
            if (mode === 'unhold') {
                PhoneWidgetCall.sendHoldRequest(callSid);   
            } else {
                PhoneWidgetCall.sendUnHoldRequest(callSid);
            }
        } else {
            alert('Error: Not found active Connection CallSid');
        }
    });
     
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
     
    //setInterval('renewTwDevice();', 50000);

JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    if (Yii::$app->controller->module->id != 'user-management') {
        $this->registerJs($js, \yii\web\View::POS_READY);

        $cookies = Yii::$app->request->cookies;

        //\yii\helpers\VarDumper::dump($cookies, 10, true);
        /*echo '<h1>+++++++++';
        \yii\helpers\VarDumper::dump($_COOKIE['web-phone-widget-close'], 10, true);
        echo '--------</h1>';*/


        //if (($cookie = $cookies->get('web-phone-widget-close')) !== null) {
            if(!isset($_COOKIE['web-phone-widget-close'])) {
                $this->registerJs("$('#web-phone-widget').slideDown();", \yii\web\View::POS_READY);
            }
        //}

    }
//}


