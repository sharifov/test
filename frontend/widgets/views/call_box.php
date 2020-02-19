<?php

use yii\bootstrap4\Modal;

/* @var $model \common\models\Notifications[] */
/* @var $newCount integer */
/* @var $lastCall \common\models\Call */
/* @var $userCallStatus \common\models\UserCallStatus */
/* @var $countMissedCalls integer*/

\frontend\assets\CallBoxAsset::register($this);
\frontend\assets\TimerAsset::register($this);


/*$client = $lastCall->cLead ? $lastCall->cLead->client : ;
if($client) {
    $client_name = $client->full_name;
} else {
    $client_name = '';
}*/
$client_phone = '';

if($lastCall) {
    if ($lastCall->c_call_type_id === \common\models\Call::CALL_TYPE_IN) {
        $client_phone = $lastCall->c_from;
    } else {
        $client_phone = $lastCall->c_to;
    }
}


if($client_phone) {
    $clientPhone = \common\models\ClientPhone::find()->where(['phone' => $client_phone])->limit(1)->orderBy(['client_id' => SORT_DESC])->one();
} else {
    $clientPhone = null;
}
$client = null;
if($clientPhone && $client = $clientPhone->client) {
    $client_name = $client->full_name;
    if($client_name === 'ClientName') {
        $client_name = '- - - - -';
    }
} else {
    $client_name = '';
}


$iconClass = 'fa fa-list';
if ($lastCall && ($lastCall->isStatusRinging() || $lastCall->isStatusInProgress())) {
    $isVisible = true;
} else {
    $isVisible = false;
}
//$iconClass = 'fa fa-refresh fa-spin';


use yii\widgets\Pjax; ?>

<?php yii\widgets\Pjax::begin(['id' => 'call-box-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => []])?>
<div class="fabs">
    <div class="call_box <?=$isVisible ? 'is-visible' : ''?>">
        <div class="call_box_header" style="<?=($userCallStatus && $userCallStatus->us_type_id === \common\models\UserCallStatus::STATUS_TYPE_OCCUPIED) ? 'background: #f55f42' : ''?>">
            <div class="call_box_option">
                <div class="header_img">
                    <?=\yii\helpers\Html::img('/img/user.png')?>
                </div>
                <span id="call_box_client_name"><?=\yii\helpers\Html::encode($client_name)?></span> <br>  <span class="agent" id="call_box_client_phone"><?=\yii\helpers\Html::encode($client_phone)?></span>
                <?php /*<span class="online">
                    <?php if($lastCall):?>
                        <?=$lastCall->c_lead_id ? \yii\helpers\Html::a('LeadId: '.$lastCall->c_lead_id, ['lead/view', 'id' => $lastCall->c_lead_id], ['target' => '_blank', 'data-pjax' => 0]) : ''?>
                    <?php endif; ?>
                </span>*/?>
                <?php /* <i class="fa fa-phone"></i>*/?>

                <?php /*
                <span id="call_box_fullscreen_loader" class="call_box_fullscreen_loader"><i class="fullscreen fa fa-window-maximize"></i></span>
                */?>

            </div>

        </div>
        <div class="call_box_body call_box_login">
            <?php if($lastCall):?>
                <h4 title="<?=$lastCall->c_updated_dt ? Yii::$app->formatter->asDatetime(strtotime($lastCall->c_updated_dt)) : '-'?>">
                    <?=$lastCall->getStatusLabel()?>
                    <?php /*Id: <?=$lastCall->c_id?> [<?=date('H:i:s')?>]*/ ?>
                </h4>
                <h4 id="call_box_call_status">
                    <span class="badge"><?=$lastCall->cProject ? \yii\helpers\Html::encode($lastCall->cProject->name) : '-'?></span>,
                    <span class="label label-default"><?=$lastCall->cDep ? \yii\helpers\Html::encode($lastCall->cDep->dep_name) : '-'?></span><br>
                    <?=$lastCall->getCallTypeName()?>
                </h4>
                <?php if ($lastCall->isStatusRinging() || $lastCall->isStatusInProgress()): ?>
                    <?php
                        if($lastCall->c_updated_dt) {
                            $timerSeconds = time() - strtotime($lastCall->c_updated_dt);
                            if(!$timerSeconds) {
                                $timerSeconds = 0;
                            }
                            if( $timerSeconds >= 0 ) {
                                echo '<div  style="font-size: 16px" class="badge badge-warning">
<i class="fa fa-clock-o fa-spin" title="updated: '.date('H:i:s', strtotime($lastCall->c_updated_dt)).'"></i> <span id="call-box-timer" class="timer">' .gmdate('i:s', $timerSeconds). '</span></div>';
                                $js = "$('#call-box-timer').timer({format: '%M:%S', seconds: " . $timerSeconds . "}).timer('start');";
                                $this->registerJs($js, \yii\web\View::POS_READY);
                            }
                        }
                    ?>
                <?php else:?>

                    <?php if($lastCall->c_updated_dt):?>
                        <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($lastCall->c_updated_dt))?>
                        (<?=Yii::$app->formatter->asRelativeTime(strtotime($lastCall->c_updated_dt))?>)
                    <?php endif; ?>

                <?php endif; ?>
            <?php endif; ?>


            <?php /*<a id="call_box_first_screen2" class="fab"><i class="fa fa-arrow-right"></i></a>*/ ?>
            <div style="padding: 0 10px 0 10px">
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        My Call Status:
                    </div>
                    <div class="col-md-6">
                        <?php
                        echo \yii\helpers\Html::dropDownList('user-call-status', $userCallStatus ? $userCallStatus->us_type_id : \common\models\UserCallStatus::STATUS_TYPE_READY, \common\models\UserCallStatus::STATUS_TYPE_LIST,
                            ['class' => 'form-control', 'id' => 'user-call-status-select']);
                        ?>

                        <?php /*=\yii\helpers\Html::button('<i class="fa fa-search"></i> Show Details', ['class' => 'btn btn-sm btn-info', 'id' => 'call_box_first_screen'])*/?>
                    </div>
                    <br><br>
                </div>


                <?php /*php if($lastCalls): $n = 1; ?>
                    <h5>Last Calls:</h5>
                <table class="table table-bordered">
                    <?php foreach ($lastCalls as $call):?>

                    <tr>
                        <td><small><?=$n++?></small></td>
                        <th><small><?=((int) $call->c_call_type_id === \common\models\Call::CALL_TYPE_IN ? \yii\helpers\Html::img('/img/incoming-call.png', ['title' => 'Incoming', 'style' => 'width:14px']) :
                                \yii\helpers\Html::img('/img/outgoing-call2.png', ['title' => 'Outgoing', 'style' => 'width:14px']))?>

                            <?=((int) $call->c_call_type_id === \common\models\Call::CALL_TYPE_IN ? $call->c_from : $call->c_to)?>
                                </small>
                        </th>
                        <td><small title="<?=$call->c_call_duration ? Yii::$app->formatter->asDuration($call->c_call_duration) : ''?>"><?=$call->c_call_duration > 0 ? $call->c_call_duration : 0?> s<br><?=\yii\helpers\Html::encode($call->c_call_status)?></small></td>
                        <td><small><?=Yii::$app->formatter->asDatetime(strtotime($call->c_created_dt), 'dd-MMM [HH:mm]')?></small></td>
                    </tr>
                   <?php endforeach; ?>
                </table>
                <?php endif;*/ ?>

                <?php if($client): ?>
                    <?=\yii\helpers\Html::button('<i class="fa fa-user"></i> Client Info', [
                        'class' => 'btn btn-xs btn-info',
                        'id' => 'btn-client-details',
                        'data-client-id' => $client ? $client->id : 0, 'style' => $client ? '' : 'display:none'
                    ])?>
                <?php endif; ?>

                <?php if($lastCall): ?>
                    <?=\yii\helpers\Html::button('<i class="fa fa-phone"></i> Call Info', [
                        'class' => 'btn btn-xs btn-default',
                        'title' => 'Call Info Id: '.$lastCall->c_id,
                        'data-call-id' => $lastCall->c_id,
                        'id' => 'btn-call-info'
                    ])?>


                    <?php //php if($countMissedCalls): ?>
                        <?=\yii\helpers\Html::button('<i class="fa fa-phone"></i> Missed' . ($countMissedCalls ? ' (' . $countMissedCalls .')' : '') , [
                            'class' => 'btn btn-xs btn-danger',
                            'title' => 'Missed Calls' . ($countMissedCalls ? ' (' . $countMissedCalls .')' : ''),
                            'id' => 'btn-missed-calls'
                        ])?>
                    <?php //php endif; ?>

                <?php endif; ?>



            </div>


            <?php //=\yii\helpers\Html::button('<i class="fa fa-phone"></i> Is Ready', ['class' => 'btn btn-sm btn-success'])?>

            <?php /*<div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-success"><i class="fa fa-phone"></i> Is Ready</button>
                <button type="button" class="btn btn-danger">Occupied</button>
            </div>*/?>

            <?php /*
            <div class="dropup">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span id="span-update-call-status"><?=($userCallStatus ? $userCallStatus->getStatusTypeName() : 'Update my Call Status')?></span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><?=\yii\helpers\Html::a('Is Ready', '#', ['class' => 'user-call-status', 'data-type-id' => \common\models\UserCallStatus::STATUS_TYPE_READY])?></li>
                    <li><?=\yii\helpers\Html::a('Is Occupied', '#', ['class' => 'user-call-status','data-type-id' => \common\models\UserCallStatus::STATUS_TYPE_OCCUPIED])?></li>
                </ul>
            </div>*/ ?>




        </div>


        <?php /*
        <div class="text-right">
            <div id="call_box_body" class="call_box_body">
                <div class="call_box_category">
                    <a id="call_box_third_screen" class="fab"><i class="fa fa-arrow-left"></i></a>
                    <p>What would you like to talk about?</p>
                    <ul>
                        <li><?=\yii\helpers\Html::a('List of Leads', '#')?></li>
                    </ul>
                </div>
            </div>
        </div>*/?>


        <div class="fab_field">
            <?php /*<a id="fab_camera" class="fab"><i class="zmdi zmdi-camera"></i></a>
        <a id="fab_send" class="fab"><i class="zmdi zmdi-mail-send"></i></a>
        <textarea id="call_boxSend" name="call_box_message" placeholder="Send a message" class="call_box_field call_box_message"></textarea>*/?>
        </div>
    </div>
    <a id="prime" class="fab <?=($userCallStatus && $userCallStatus->us_type_id === \common\models\UserCallStatus::STATUS_TYPE_OCCUPIED ? 'call-status-occupied' : 'call-status-ready')?>">
        <?php /*<i class="prime fa fa-list"></i>*/?>
        <?php
            $iconClass = 'fa fa-list';

            if($lastCall) {
                if ($lastCall->isStatusRinging()) {
                    $iconClass = 'fa fa-spinner fa-spin';
                } elseif ($lastCall->isStatusInProgress()) {
                    $iconClass = 'fa fa-refresh fa-spin';
                }
            }
        //$iconClass = 'fa fa-refresh fa-spin';
        ?>
            <i class="prime <?=$iconClass?>"></i>
    </a>
</div>
<?php yii\widgets\Pjax::end() ?>

<?= frontend\widgets\IncomingCallWidget::widget() ?>

<?php Modal::begin([
    'id' => 'call-box-modal',
    'title' => 'Missed Calls',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_LARGE
]); ?>
<?php Modal::end(); ?>


<?php
    $callBoxUrl = \yii\helpers\Url::to(['/call/call-box']);
?>

<script>
    const callBoxUrl = '<?=$callBoxUrl?>';

    function incomingCall(obj) {

        //alert(123);
        //$('.fab').toggleClass('is-visible');

        //console.log(obj);


        if(obj.status == 'initiated' || obj.status == 'ringing') {
            hideCallBox(0);
            if (!$('.fab').hasClass('is-visible')) {
                toggleFab();
            }

            $('#call_box_client_name').text(obj.client_name);
            $('#call_box_client_phone').text(obj.client_phone);

            /*if(obj.last_lead_id > 0) {
                $('#call_last_lead_id').html('<a href="/lead/view/' + obj.last_lead_id + '" target="_blank" data-pjax="0">' + obj.last_lead_id + '</a>');
            }*/

            if(obj.client_id > 0) {
                $('#btn-client-details').data('client-id', obj.client_id).show();
            }


            /*
            $('#call_count_calls').text(obj.client_count_calls);
            $('#call_count_sms').text(obj.client_count_sms);
            $('#call_created_date').text(obj.client_created_date);
            $('#call_last_activity').text(obj.client_last_activity);*/
        }

        if(obj.status == 'completed' || obj.status == 'busy') {
            hideCallBox(0);
            if ($('.fab').hasClass('is-visible')) {
                toggleFab();
            }


        }

        $('#call_box_call_status').text(obj.status + ' ...');
    }


    function updateUserCallStatus(obj) {
        console.log(obj);
        if(obj.type_id == 1) {
            $('#prime').addClass('call-status-ready');
            $('#prime').removeClass('call-status-occupied');
            $('#span-update-call-status').text('Is Ready');
            $('#user-call-status-select').val(obj.type_id);
        } else if(obj.type_id == 2) {
            $('#prime').removeClass('call-status-ready');
            $('#prime').addClass('call-status-occupied');
            $('#span-update-call-status').text('Is Occupied');
            $('#user-call-status-select').val(obj.type_id);
            if ($('.fab').hasClass('is-visible')) {
                toggleFab();
            }
        }
    }

    function refreshCallBox(obj) {
        // console.log(obj);
        $.pjax.reload({url: callBoxUrl, container: '#call-box-pjax', push: false, replace: false, 'scrollTo': false, timeout: 7000, async: false, data: {id: obj.id, status: obj.status}});
    }

</script>

<?php
$callStatusUrl = \yii\helpers\Url::to(['/user-call-status/update-status']);
$clientInfoUrl = \yii\helpers\Url::to(['/client/ajax-get-info']);
$missedCallsUrl = \yii\helpers\Url::to(['/call/ajax-missed-calls']);
$callInfoUrl = \yii\helpers\Url::to(['/call/ajax-call-info']);

$userId = Yii::$app->user->id;

$js = <<<JS

    var callStatusUrl = '$callStatusUrl';
    var clientInfoUrl = '$clientInfoUrl';
    var missedCallsUrl = '$missedCallsUrl';
    var callInfoUrl = '$callInfoUrl';
    

    $(document).on('click', '#btn-client-details', function(e) {
        e.preventDefault();
        var client_id = $(this).data('client-id');
        $('#call-box-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#call-box-modal-label').html('Client Details (' + client_id + ')');
        $('#call-box-modal').modal();
        $.post(clientInfoUrl, {client_id: client_id},
            function (data) {
                $('#call-box-modal .modal-body').html(data);
            }
        );
    });
    
    $(document).on('click', '#btn-missed-calls', function(e) {
        e.preventDefault();
        $('#call-box-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#call-box-modal-label').html('Missed Calls');
        $('#call-box-modal').modal();
        $.post(missedCallsUrl, 
            function (data) {
                $('#call-box-modal .modal-body').html(data);
            }
        );
    });
    
    $(document).on('click', '#btn-call-info', function(e) {
        e.preventDefault();
        var callId = $(this).data('call-id');
        $('#call-box-modal .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#call-box-modal-label').html('Call Info');
        $('#call-box-modal').modal();
        $.post(callInfoUrl, {id: callId}, 
            function (data) {
                $('#call-box-modal .modal-body').html(data);
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
                /*$('#preloader').addClass('hidden');
                modal.find('.modal-body').html(data);
                modal.modal('show');*/
            },
            error: function (error) {
                console.error('Error: ' + error);
            }
        });

    });
   

    /*$("#call-box-pjax").on("pjax:start", function() {
        $('.prime').addClass('fa-recycle fa-spin');
    });
    
    $("#call-box-pjax").on("pjax:end", function() {
        $('.prime').removeClass('fa-recycle fa-spin');
    });*/

JS;

$this->registerJs($js, \yii\web\View::POS_READY);

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['/orders/create'])) {

} else {*/

//if (Yii::$app->controller->module->id != 'user-management') {

//}
//}