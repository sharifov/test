<?php
/* @var $model \common\models\Notifications[] */
/* @var $newCount integer */
/* @var $lastCall \common\models\Call */
/* @var $lastCalls \common\models\Call[] */
/* @var $userCallStatus \common\models\UserCallStatus */

\frontend\assets\CallBoxAsset::register($this);


/*$client = $lastCall->cLead ? $lastCall->cLead->client : ;
if($client) {
    $client_name = $client->full_name;
} else {
    $client_name = '';
}*/
$client_phone = '';

if($lastCall) {
    if ($lastCall->c_call_type_id == \common\models\Call::CALL_TYPE_IN) {
        $client_phone = $lastCall->c_from;
    } else {
        $client_phone = $lastCall->c_to;
    }
}


$clientPhone = \common\models\ClientPhone::find()->where(['phone' => $client_phone])->limit(1)->one();
$client = null;
if($clientPhone && $client = $clientPhone->client) {
    $client_name = $client->full_name;
} else {
    $client_name = '';
}


?>
<?php /*yii\widgets\Pjax::begin(['id' => 'call-box-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => [
        //'tag' => 'li',
        //'class' => 'dropdown',
        //'role' => 'presentation',
]])*/?>
<div class="fabs">
    <div class="call_box">
        <div class="call_box_header">
            <div class="call_box_option">
                <div class="header_img">
                    <?=\yii\helpers\Html::img('/img/user.png')?>
                </div>
                <span id="call_box_client_name"><?=\yii\helpers\Html::encode($client_name)?></span> <br>  <span class="agent" id="call_box_client_phone"><?=\yii\helpers\Html::encode($client_phone)?></span>
                <span class="online" id="call_box_call_status"><?=$lastCall->c_lead_id ? \yii\helpers\Html::a('LeadId: '.$lastCall->c_lead_id, ['lead/view', 'id' => $lastCall->c_lead_id], ['target' => '_blank', 'data-pjax' => 0]) : ''?></span>
                <?/* <i class="fa fa-phone"></i>*/?>
                <span id="call_box_fullscreen_loader" class="call_box_fullscreen_loader"><i class="fullscreen fa fa-window-maximize"></i></span>

            </div>

        </div>
        <div class="call_box_body call_box_login">
            <h5 id="call_box_call_status"><?=ucfirst($lastCall->c_call_status)?> ... (<?=Yii::$app->formatter->asRelativeTime(strtotime($lastCall->c_created_dt))?>)</h5>

            <?/*<a id="call_box_first_screen2" class="fab"><i class="fa fa-arrow-right"></i></a>*/ ?>
            <div style="padding: 10px">
                <?php if($lastCalls): $n = 1; ?>
                <table class="table table-bordered">
                    <?php foreach ($lastCalls as $call):?>
                    <tr>
                        <td><?=$n++?></td>
                        <th><?=((int) $call->c_call_type_id === \common\models\Call::CALL_TYPE_IN ? \yii\helpers\Html::img('/img/incoming-call.png', ['title' => 'Incoming', 'style' => 'width:14px']) :
                                \yii\helpers\Html::img('/img/outgoing-call2.png', ['title' => 'Outgoing', 'style' => 'width:14px']))?>
                        <?=((int) $call->c_call_type_id === \common\models\Call::CALL_TYPE_IN ? $call->c_from : $call->c_to)?></th>
                        <td><small><?=Yii::$app->formatter->asDatetime(strtotime($call->c_created_dt))?></small></td>
                    </tr>
                   <?php endforeach; ?>
                </table>
                <?php endif; ?>

                <?/* <table class="table table-bordered">
                    <tr>
                        <th>Last Lead</th>
                        <td id="call_last_lead_id">-</td>
                    </tr>
                    <tr>
                        <th>Count of Calls</th>
                        <td id="call_count_calls">-</td>
                    </tr>
                    <tr>
                        <th>Count of SMS</th>
                        <td id="call_count_sms">-</td>
                    </tr>
                    <tr>
                        <th>Created Date</th>
                        <td id="call_created_date">-</td>
                    </tr>
                    <tr>
                        <th>Last Activity</th>
                        <td id="call_last_activity">-</td>
                    </tr>
                </table>*/?>

                <?//php if($client): ?>
                    <?=\yii\helpers\Html::button('<i class="fa fa-user"></i> Client Details', [
                        'class' => 'btn btn-sm btn-info',
                        'id' => 'btn-client-details',
                        'data-client-id' => $client ? $client->id : 0, 'style' => $client ? '' : 'display:none'
                    ])?>
                <?// endif; ?>
            </div>


            <?//=\yii\helpers\Html::button('<i class="fa fa-phone"></i> Is Ready', ['class' => 'btn btn-sm btn-success'])?>

            <?/*<div class="btn-group" role="group" aria-label="...">
                <button type="button" class="btn btn-success"><i class="fa fa-phone"></i> Is Ready</button>
                <button type="button" class="btn btn-danger">Occupied</button>
            </div>*/?>


            <div class="dropup">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span id="span-update-call-status"><?=($userCallStatus ? $userCallStatus->getStatusTypeName() : 'Update my Call Status')?></span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><?=\yii\helpers\Html::a('Is Ready', '#', ['class' => 'user-call-status', 'data-type-id' => \common\models\UserCallStatus::STATUS_TYPE_READY])?></li>
                    <li><?=\yii\helpers\Html::a('Is Occupied', '#', ['class' => 'user-call-status','data-type-id' => \common\models\UserCallStatus::STATUS_TYPE_OCCUPIED])?></li>
                </ul>
            </div>

            <?/*=\yii\helpers\Html::button('<i class="fa fa-search"></i> Show Details', ['class' => 'btn btn-sm btn-info', 'id' => 'call_box_first_screen'])*/?>

        </div>


        <div class="text-right">
            <div id="call_box_body" class="call_box_body">
                <div class="call_box_category">
                    <a id="call_box_third_screen" class="fab"><i class="fa fa-arrow-left"></i></a>
                    <p>What would you like to talk about?</p>
                    <ul>
                        <li>List of Leads<?/*=\yii\helpers\Html::a('List of Leads', '#')*/?></li>
                        <?/*<li class="active">Sales</li>*/?>
                    </ul>
                </div>
            </div>
        </div>


        <div class="fab_field">
            <?/*<a id="fab_camera" class="fab"><i class="zmdi zmdi-camera"></i></a>
        <a id="fab_send" class="fab"><i class="zmdi zmdi-mail-send"></i></a>
        <textarea id="call_boxSend" name="call_box_message" placeholder="Send a message" class="call_box_field call_box_message"></textarea>*/?>
        </div>
    </div>
    <a id="prime" class="fab <?=($userCallStatus && $userCallStatus->us_type_id == \common\models\UserCallStatus::STATUS_TYPE_OCCUPIED ? 'call-status-occupied' : 'call-status-ready')?>"><i class="prime fa fa-phone"></i></a>
</div>

<?php //yii\widgets\Pjax::end() ?>

<?php \yii\bootstrap\Modal::begin([
    'id' => 'client-details-modal',
    'header' => '<h4 class="modal-title">Client Details</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]); ?>
<?php \yii\bootstrap\Modal::end(); ?>


<script>
    function incomingCall(obj) {

        //alert(123);
        //$('.fab').toggleClass('is-visible');

        console.log(obj);


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
        } else if(obj.type_id == 2) {
            $('#prime').removeClass('call-status-ready');
            $('#prime').addClass('call-status-occupied');
            $('#span-update-call-status').text('Is Occupied');
            if ($('.fab').hasClass('is-visible')) {
                toggleFab();
            }
        }
    }

</script>

<?php

$callStatusUrl = \yii\helpers\Url::to(['user-call-status/update-status']);
$clientInfoUrl = \yii\helpers\Url::to(['client/ajax-get-info']);


/*echo  \yii\helpers\Url::home().'<br>';
echo  \yii\helpers\Url::base().'<br>';

echo  \yii\helpers\Url::home(true).'<br>';
echo  \yii\helpers\Url::base(true).'<br>';

exit;*/

//\yii\helpers\VarDumper::dump($_SERVER, 10, true); exit;

//$jsPath = Yii::$app->request->baseUrl.'/js/sounds/';

$userId = Yii::$app->user->id;

$js = <<<JS

    var callStatusUrl = '$callStatusUrl';
    var clientInfoUrl = '$clientInfoUrl';

    $(document).on('click', '#btn-client-details', function(e) {
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


    $(document).on('click', '.user-call-status', function(e) {
        e.preventDefault();
        var type_id = $(this).data('type-id');
        
        $.ajax({
            type: 'post',
            data: {'type_id': type_id},
            url: callStatusUrl,
            success: function (data) {
                console.log(data);
                /*$('#preloader').addClass('hidden');
                modal.find('.modal-body').html(data);
                modal.modal('show');*/
            },
            error: function (error) {
                console.error('Error: ' + error);
            }
        });

    });


JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    if (Yii::$app->controller->module->id != 'user-management') {
        $this->registerJs($js, \yii\web\View::POS_READY);
    }
//}


