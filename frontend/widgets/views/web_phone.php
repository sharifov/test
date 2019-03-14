<?php

/* @var $clientId string */
/* @var $token string */
/* @var $fromAgentPhone string */


\frontend\assets\WebPhoneAsset::register($this);
?>

<div class="fabs2" style="display: none">
    <a id="prime2" class="fab2"><i class="fa fa-phone"></i></a>
</div>

<div id="web-phone-widget">

    <?php if($token): ?>


        <div id="web-phone-token" style="display: none"><?=$token?></div>
            <table class="table" style="margin: 0">
                <tr>
                    <?/*<td style="display: none"><i title="<?=$token?>">Token</i></td>*/?>
                    <td width="100"><i class="fa fa-user"></i> <span><?=$clientId?></span></td>
                    <td>From: <i class="fa fa-phone"></i> <span id="web-call-from-number"></span></td>

                    <td>To: <i class="fa fa-phone"></i> <span id="web-call-to-number"></span></td>
                    <?/*<td>
                        <table class="table table-bordered">
                            <tr>
                                <td>From:</td><td> <i class="fa fa-phone"></i> <span id="web-call-from-number"></span></td>
                            </tr>
                            <tr>
                                <td>To:</td><td> <i class="fa fa-phone"></i> <span id="web-call-to-number"></span></td>
                            </tr>
                        </table>
                    </td>*/?>
                    <td width="120px">
                        <div class="text-right">
                            <?//=\yii\helpers\Html::button('<i class="fa fa-tumblr"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-webphone-token', 'onclick' => 'alert($("#web-phone-token").text())'])?>
                            <?=\yii\helpers\Html::button('<i class="fa fa-angle-double-up"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-nin-max-webphone'])?>
                            <?=\yii\helpers\Html::button('<i class="fa fa-close"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-webphone-close'])?>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="table" style="margin: 0">
                <tr>
                    <td width="250">
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
                        <?=\yii\helpers\Html::button('<i class="fa fa-close"></i> Hangup', ['class' => 'btn btn-xs btn-danger','id' => 'button-hangup', 'style' => 'display:none'])?>
                        <?/*=\yii\helpers\Html::button('<i class="fa fa-phone"></i> Call', ['class' => 'btn btn-xs btn-success', 'id' => 'button-call'])*/?>
                        <div id="call-controls2" style="display: none;">
                            <?=\yii\helpers\Html::button('<i class="fa fa-phone"></i> Answer', ['class' => 'btn btn-sm btn-success', 'id' => 'button-answer'])?>
                            <?=\yii\helpers\Html::button('<i class="fa fa-close"></i> Reject', ['class' => 'btn btn-sm btn-danger','id' => 'button-reject'])?>
                            <?=\yii\helpers\Html::button('<i class="fa fa-forward"></i> Forward', ['class' => 'btn btn-sm btn-info','id' => 'button-redirect'])?>
                            <?=\yii\helpers\Html::input('text', 'redirect-to', '',  ['class' => 'form-control','id' => 'redirect-to'])?>
                        </div>
                    </td>
                </tr>
            </table>


            <div class="webphone-controls" id="controls" style="display: none">

                <table class="table">
                    <tr>
                        <td width="250">
                            <div id="info">
                                <?/*<div id="client-name"></div>*/ ?>
                                <div id="output-selection">
                                    <label>Ringtone Devices</label>
                                    <select id="ringtone-devices" multiple></select>
                                    <label>Speaker Devices</label>
                                    <select id="speaker-devices" multiple></select><br/>
                                    <?/*<a id="get-devices">Seeing unknown devices?</a>*/?>
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


<?php \yii\bootstrap\Modal::begin([
    'id' => 'web-phone-dial-modal',
    'header' => '<h4 class="modal-title">Phone Dial</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]); ?>
<?php \yii\bootstrap\Modal::end(); ?>


<?php
    $ajaxSaveCallUrl = \yii\helpers\Url::to(['phone/ajax-save-call']);
    $ajaxRedirectCallUrl = \yii\helpers\Url::to(['phone/ajax-call-redirect']);
?>


<script type="text/javascript">

    const ajaxSaveCallUrl = '<?=$ajaxSaveCallUrl?>';
    const ajaxCallRedirectUrl = '<?=$ajaxRedirectCallUrl?>';

    function createNotify(title, message, type) {
        new PNotify({
            title: title,
            type: type,
            text: message,
            hide: true
        });
    }

    var tw_configs = {"client":"<?= $clientId;?>"};
    var webPhoneParams = {};
    var call_acc_sid = '';

   // "use strict";

    var device;
    var connection;

    var speakerDevices = document.getElementById('speaker-devices');
    var ringtoneDevices = document.getElementById('ringtone-devices');
    var outputVolumeBar = document.getElementById('output-volume');
    var inputVolumeBar = document.getElementById('input-volume');
    var volumeIndicators = document.getElementById('volume-indicators');

    if (!Array.prototype.inArray) {
        Array.prototype.inArray = function (element) {
            return this.indexOf(element) > -1;
        };
    }

    // Bind button to make call
    /*document.getElementById('button-call').onclick = function () {
        // get the phone number to connect the call to
        var params = {
            To: document.getElementById('phone-number').value,
            FromAgentPhone: tw_configs.FromAgentPhone
        };

        console.log('Calling ' + params.To + '...');
        if (device) {
            device.connect(params);
        }
    };*/

    // Bind button to hangup call
    document.getElementById('button-hangup').onclick = function () {
        log('Hanging up...');
        if (device) {
            device.disconnectAll();
        }
    };

    /*document.getElementById('get-devices').onclick = function () {
        navigator.mediaDevices.getUserMedia({audio: true})
            .then(updateAllDevices.bind(device));
    }*/

    speakerDevices.addEventListener('change', function () {
        var selectedDevices = [].slice.call(speakerDevices.children)
            .filter(function (node) {
                return node.selected;
            })
            .map(function (node) {
                return node.getAttribute('data-id');
            });

        device.audio.speakerDevices.set(selectedDevices);
    });

    ringtoneDevices.addEventListener('change', function () {
        var selectedDevices = [].slice.call(ringtoneDevices.children)
            .filter(function (node) {
                return node.selected;
            })
            .map(function (node) {
                return node.getAttribute('data-id');
            });

        device.audio.ringtoneDevices.set(selectedDevices);
    });


    function volumeIndicatorsChange(inputVolume, outputVolume) {
        var inputColor = 'red';
        if (inputVolume < .50) {
            inputColor = 'green';
        } else if (inputVolume < .75) {
            inputColor = 'yellow';
        }

        inputVolumeBar.style.width = Math.floor(inputVolume * 300) + 'px';
        inputVolumeBar.style.background = inputColor;

        var outputColor = 'red';
        if (outputVolume < .50) {
            outputColor = 'green';
        } else if (outputVolume < .75) {
            outputColor = 'yellow';
        }

        outputVolumeBar.style.width = Math.floor(outputVolume * 300) + 'px';
        outputVolumeBar.style.background = outputColor;
    }


    function bindVolumeIndicators(connection) {
        connection.on('volume', function (inputVolume, outputVolume) {
            volumeIndicatorsChange(inputVolume, outputVolume);
        });
    }

    function updateAllDevices() {
        updateDevices(speakerDevices, device.audio.speakerDevices.get());
        updateDevices(ringtoneDevices, device.audio.ringtoneDevices.get());

        // updateDevices(speakerDevices, );
        // updateDevices(ringtoneDevices, device);
    }

    // Update the available ringtone and speaker devices
    function updateDevices(selectEl, selectedDevices) {
        selectEl.innerHTML = '';

        device.audio.availableOutputDevices.forEach(function (device, id) {
            var isActive = (selectedDevices.size === 0 && id === 'default');
            selectedDevices.forEach(function (device) {
                if (device.deviceId === id) {
                    isActive = true;
                }
            });

            var option = document.createElement('option');
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
        var logDiv = document.getElementById('call-log');
        logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
        logDiv.scrollTop = logDiv.scrollHeight;
    }


    function clearLog() {
        var logDiv = document.getElementById('call-log');
        logDiv.innerHTML = '';
        logDiv.scrollTop = logDiv.scrollHeight;
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
            var status = device.status();
            var refrash_device_statuses = ['pending', 'closed', 'offline', 'error'];
            if (refrash_device_statuses.inArray(status)) {
                initDevice();
            }
        }
    }

    document.getElementById('button-answer').onclick = function () {
        console.log("button-answer: " + connection);
        if (connection) {
            connection.accept();
            document.getElementById('call-controls2').style.display = 'none';
        }
    };

    document.getElementById('button-reject').onclick = function () {
        console.log("button-reject: " + connection);
        if (connection) {
            connection.reject();
            document.getElementById('call-controls2').style.display = 'none';
        }
    };

    // TODO redirect call
    document.getElementById('button-redirect').onclick = function () {
        console.log("button-redirect: " + connection);
        if (connection && connection.parameters.CallSid) {
            connection.accept();
            redirectToClient = document.getElementById('redirect-to').value;
            if(redirectTo.length < 2) {
                return false;
            }
            $.ajax({
                type: 'post',
                data: {
                    'sid': connection.parameters.CallSid,
                    'type': 'client',
                    'from': connection.parameters.To,
                    'to':redirectToClient,
                },
                url: ajaxCallRedirectUrl,
                success: function (data) {
                    console.log(data);

                },
                error: function (error) {
                    console.error(error);
                }
            });
        }
    };


    function saveDbCall(call_sid, call_from, call_to, call_status) {

        var project_id = webPhoneParams.project_id;
        var lead_id = webPhoneParams.lead_id;
        //var call_acc_sid =

        console.info('sid: ' + call_sid + ' : ' + call_from + ' : ' + call_to + ' : ' + call_status + ' : ' + project_id + ' : ' + lead_id);

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
                    'project_id': project_id
                },
                url: ajaxSaveCallUrl,
                success: function (data) {
                    console.log(data);
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


    function initDevice() {

        clearLog();
        log('Requesting Capability Token...');
        $.getJSON('/phone/get-token')
            .then(function (data_res) {
                var data = data_res.data;
                log('Got a token.');
                console.log('app_sid: ' + data.app_sid);
                console.log('account_sid: ' + data.account_sid);

                call_acc_sid = data.account_sid;

                //console.log('Token: ' + data.token);
                // Setup Twilio.Device
                device = new Twilio.Device(data.token, {debug: true});

                //console.log([data, device]);
                device.on('ready', function (device) {
                    log('Twilio.Device Ready!');
                    //document.getElementById('call-controls').style.display = 'block';
                });

                device.on('error', function (error) {
                    log('Twilio.Device Error: ' + error.message);
                });

                device.on('connect', function (conn) {
                    log('Successfully established call!');
                    console.warn(conn);
                    //console.info(conn.parameters);
                    //console.log(conn.parameters.CallSid);

                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'queued');

                    //document.getElementById('button-call').style.display = 'none';
                    document.getElementById('button-hangup').style.display = 'inline';
                    volumeIndicators.style.display = 'block';
                    bindVolumeIndicators(conn);
                });

                device.on('disconnect', function (conn) {
                    log('Call ended');
                    createNotify('Call ended', 'Call ended', 'warning');
                    console.warn(conn);

                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'completed');

                    //document.getElementById('button-call').style.display = 'inline';
                    document.getElementById('button-hangup').style.display = 'none';
                    volumeIndicators.style.display = 'none';

                    cleanPhones();

                });

                device.on('incoming', function (conn) {
                    connection = conn;
                    log('Incoming connection from ' + conn.parameters.From);
                    createNotify('Incoming connection', 'Incoming connection from ' + conn.parameters.From, 'success');

                    var archEnemyPhoneNumber = tw_configs.client;
                    //document.getElementById('call-controls').style.display = 'none';
                    document.getElementById('call-controls2').style.display = 'block';
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
                    connection = conn;
                    log('Cancel call');
                    createNotify('Cancel call', 'Cancel call', 'warning');
                    saveDbCall(conn.parameters.CallSid, conn.message.FromAgentPhone, conn.message.To, 'canceled');

                    //document.getElementById('call-controls').style.display = 'block';
                    document.getElementById('call-controls2').style.display = 'none';
                });



                //setClientNameUI(data.client);

                device.audio.on('deviceChange', updateAllDevices.bind(device));
                // Show audio selection UI if it is supported by the browser.
                if (device.audio.isOutputSelectionSupported) {
                    document.getElementById('output-selection').style.display = 'block';
                }
            })
            .catch(function (err) {
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


    function webCall(phone_from, phone_to, project_id, lead_id) {
        var params = {'To': phone_to, 'FromAgentPhone': phone_from, 'project_id': project_id, 'lead_id': lead_id};
        webPhoneParams = params;

        //console.log(params); return false;

        if (device) {

            $('#web-call-from-number').text(params.FromAgentPhone);
            $('#web-call-to-number').text(params.To);

            console.log('Calling ' + params.To + '...');
            createNotify('Calling', 'Calling ' + params.To + '...', 'success');
            device.connect(params);
        }
    }

    function cleanPhones()
    {
        $('#web-call-from-number').text('');
        $('#web-call-to-number').text('');
    }


</script>

<?php

//$callStatusUrl = \yii\helpers\Url::to(['user-call-status/update-status']);
$ajaxPhoneDialUrl = \yii\helpers\Url::to(['phone/ajax-phone-dial']);

$userId = Yii::$app->user->id;

$js = <<<JS

    const ajaxPhoneDialUrl = '$ajaxPhoneDialUrl';

    /*$(document).on('click', '#btn-client-details', function(e) {
        e.preventDefault();
        var client_id = $(this).data('client-id');
        $('#client-details-modal .modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        $('#client-details-modal').modal();
        $.post(clientInfoUrl, {client_id: client_id},
            function (data) {
                $('#client-details-modal .modal-body').html(data);
            }
        );
    });
   
    $(document).on('change', '#user-call-status-select', function(e) {
        e.preventDefault();
        var type_id = $(this).val();
                
        $.ajax({
            type: 'post',
            data: {'type_id': type_id},
            url: callStatusUrl,
            success: function (data) {
                //console.log(data);
                //$('#preloader').addClass('hidden');
                //modal.find('.modal-body').html(data);
                //modal.modal('show');
            },
            error: function (error) {
                console.error('Error: ' + error);
            }
        });

    });*/
    
    
    $('#btn-nin-max-webphone').on('click', function() {
        var iTag = $(this).find('i');
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
        
        $('#web-phone-widget').slideUp('fast');
        $('.fabs2').show();
        //$(this).find('i').addClass('fa-angle-double-up');
    });
    
    
    $('#prime2').on('click', function() {
        $('#web-phone-widget').slideDown();
        $('.fabs2').hide();
    });
    
    $('.call-phone').on('click', function(e) {
        var phone_number = $(this).data('phone');
        var project_id = $(this).data('project-id');
        var lead_id = $(this).data('lead-id');
        
        //alert(phoneNumber);
        
        e.preventDefault();
        
        $('#web-phone-dial-modal .modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        $('#web-phone-dial-modal').modal();
        
        $.post(ajaxPhoneDialUrl, {'phone_number': phone_number, 'project_id': project_id, 'lead_id': lead_id},
            function (data) {
                $('#web-phone-dial-modal .modal-body').html(data);
            }
        );
    });
    
    $(document).on('click', '#btn-make-call', function(e) {
        e.preventDefault();
        var phone_to = $('#call-to-number').val();
        var phone_from = $('#call-from-number').val();
        
        var project_id = $('#call-project-id').val();
        var lead_id = $('#call-lead-id').val();
        
        
        $('#web-phone-dial-modal').modal('hide');
        //alert(phone_from + ' - ' + phone_to);
        
        
        $('#web-phone-widget').slideDown();
        $('.fabs2').hide();
        
        webCall(phone_from, phone_to, project_id, lead_id);
    });
    
    
    
    
    $('#web-phone-widget').css({left:'50%', 'margin-left':'-'+($('#web-phone-widget').width() / 2)+'px'}).slideDown();
    
    //console.log(tw_configs);
    initDevice();
    setInterval('renewTwDevice();', 50000);


JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    if (Yii::$app->controller->module->id != 'user-management') {
        $this->registerJs($js, \yii\web\View::POS_READY);
    }
//}


