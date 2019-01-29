<?php
/* @var $model \common\models\Notifications[] */
/* @var $newCount integer */

\frontend\assets\CallBoxAsset::register($this);

?>
<?php yii\widgets\Pjax::begin(['id' => 'call-box-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => [
        //'tag' => 'li',
        //'class' => 'dropdown',
        //'role' => 'presentation',
]])?>
<div class="fabs">
    <div class="call_box">
        <div class="call_box_header">
            <div class="call_box_option">
                <div class="header_img">
                    <?=\yii\helpers\Html::img('/img/user.png')?>
                </div>
                <span id="call_box_client_name">-</span> <br> <i class="fa fa-phone"></i> <span class="agent" id="call_box_client_phone">-</span> <?/*<span class="online" id="call_box_call_status">(Online)</span>*/?>
                <span id="call_box_fullscreen_loader" class="call_box_fullscreen_loader"><i class="fullscreen fa fa-window-maximize"></i></span>

            </div>

        </div>
        <div class="call_box_body call_box_login">
            <h3 id="call_box_call_status">-</h3>

            <?/*<a id="call_box_first_screen2" class="fab"><i class="fa fa-arrow-right"></i></a>*/ ?>
            <div style="padding: 10px">
                <table class="table table-bordered">
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
                </table>
            </div>

            <?=\yii\helpers\Html::button('<i class="fa fa-search"></i> Show Details', ['class' => 'btn btn-sm btn-info', 'id' => 'call_box_first_screen'])?>

        </div>


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


        <div class="fab_field">
            <?/*<a id="fab_camera" class="fab"><i class="zmdi zmdi-camera"></i></a>
        <a id="fab_send" class="fab"><i class="zmdi zmdi-mail-send"></i></a>
        <textarea id="call_boxSend" name="call_box_message" placeholder="Send a message" class="call_box_field call_box_message"></textarea>*/?>
        </div>
    </div>
    <a id="prime" class="fab"><i class="prime fa fa-phone"></i></a>
</div>

<?php yii\widgets\Pjax::end() ?>


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

            if(obj.last_lead_id > 0) {
                $('#call_last_lead_id').html('<a href="/lead/view/' + obj.last_lead_id + '" target="_blank">' + obj.last_lead_id + '</a>');
            }

            $('#call_count_calls').text(obj.client_count_calls);
            $('#call_count_sms').text(obj.client_count_sms);
            $('#call_created_date').text(obj.client_created_date);
            $('#call_last_activity').text(obj.client_last_activity);
        }

        if(obj.status == 'completed' || obj.status == 'busy') {
            hideCallBox(0);
            if ($('.fab').hasClass('is-visible')) {
                toggleFab();
            }
        }

        $('#call_box_call_status').text(obj.status + ' ...');


    }
</script>

<?php




/*echo  \yii\helpers\Url::home().'<br>';
echo  \yii\helpers\Url::base().'<br>';

echo  \yii\helpers\Url::home(true).'<br>';
echo  \yii\helpers\Url::base(true).'<br>';

exit;*/

//\yii\helpers\VarDumper::dump($_SERVER, 10, true); exit;

//$jsPath = Yii::$app->request->baseUrl.'/js/sounds/';

$userId = Yii::$app->user->id;

$js = <<<JS
    

JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    if (Yii::$app->controller->module->id != 'user-management') {
        $this->registerJs($js, \yii\web\View::POS_READY);
    }
//}


