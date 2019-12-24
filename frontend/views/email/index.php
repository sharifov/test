<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Emails';
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('qa', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\Project::getListByUser(Yii::$app->user->id);
}
?>
<div class="email-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php Pjax::begin([
		'id' => 'emails',
		'timeout' => 5000
	]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1,
                'style' => 'width: 100%'
            ],
        ]); ?>

        <div class="col-md-3">
            <?php
            echo  \kartik\daterange\DateRangePicker::widget([
                'model'=> $searchModel,
                'attribute' => 'date_range',
                'useWithAddon'=>true,
                'presetDropdown'=>true,
                'hideInput'=>true,
                'convertFormat'=>true,
                'startAttribute' => 'datetime_start',
                'endAttribute' => 'datetime_end',
                'pluginOptions'=>[
                    'timePicker'=> true,
                    'timePickerIncrement'=>1,
                    'timePicker24Hour' => true,
                    'locale'=>[
                        'format'=>'Y-m-d H:i',
                        'separator' => ' - '
                    ]
                ]
            ]);
            ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <p>
        <?/*= Html::a('Create Email', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'e_id',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'update' => function ($model, $key, $index) {
                        return Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);
                    },

                    'delete' => function ($model, $key, $index) {
                        return Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);
                    },
                ],
            ],
            //'e_reply_id',
            'e_lead_id',
            'e_case_id',
            //'e_project_id',
            [
                'attribute' => 'e_project_id',
                'value' => static function (\common\models\Email $model) {
                    return $model->eProject ? $model->eProject->name : '-';
                },
                'filter' => $projectList
            ],
            'e_email_from:email',
            'e_email_to:email',
            //'e_email_cc:email',
            //'e_email_bc:email',
            //'e_email_subject:email',
            //'e_email_body_html:ntext',
            //'e_email_body_text:ntext',
            //'e_attach',
            //'e_email_data:ntext',
            //'e_type_id',
            [
                'attribute' => 'e_type_id',
                'value' => static function (\common\models\Email $model) {
                    return $model->getTypeName();
                },
                'filter' => \common\models\Email::TYPE_LIST
            ],
            //'e_template_type_id',
            [
                'attribute' => 'e_template_type_name',
                'value' => static function (\common\models\Email $model) {
                    return $model->eTemplateType ? $model->eTemplateType->etp_name : '-';
                },
                'label' => 'Template Name'
                //'filter' =>
            ],
            //'e_language_id',
            [
                'attribute' => 'e_language_id',
                'value' => static function (\common\models\Email $model) {
                    return $model->e_language_id;
                },
                'filter' => \lajax\translatemanager\models\Language::getLanguageNames()
            ],
            'e_communication_id',
            //'e_is_deleted',
            //'e_is_new:boolean',
            //'e_delay',
            //'e_priority',
            //'e_status_id',
            [
                'attribute' => 'e_status_id',
                'value' => static function (\common\models\Email $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\Email::STATUS_LIST
            ],
            //'e_status_done_dt',
            //'e_read_dt',
            //'e_error_message',
            /*[
                'attribute' => 'e_updated_user_id',
                'value' => static function (\common\models\Email $model) {
                    return ($model->eUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->eUpdatedUser->username) : $model->e_updated_user_id);
                },
                'filter' => $userList,
                'format' => 'raw'
            ],*/

            [
                'attribute' => 'e_created_user_id',
                'value' => static function (\common\models\Email $model) {
                    return ($model->eCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->eCreatedUser->username) : $model->e_created_user_id);
                },
                'filter' => $userList,
                'format' => 'raw'
            ],
            /*[
                'attribute' => 'e_updated_dt',
                'value' => static function (\common\models\Email $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->e_updated_dt));
                },
                'format' => 'raw'
            ],*/

            /*[
                'attribute' => 'e_created_user_id',
                'value' => static function (\common\models\Email $model) {
                    return  ($model->eCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->eCreatedUser->username) : $model->e_created_user_id);
                'format' => 'raw'
                },
            ],*/
            [
                'attribute' => 'e_created_dt',
                'value' => static function (\common\models\Email $model) {
                    return $model->e_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt), 'php: Y-m-d [H:i:s]')  : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'e_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],


        ],
    ]); ?>
    <?php Pjax::end(); ?>

    <script>
        var socket   = null;

        /**
         * Send a message to the WebSocket server
         */
        function onSendClick() {
            if (socket.readyState != socket.OPEN) {
                console.error("Socket is not open: " + socket.readyState);
                return;
            }
            var msg = document.getElementById("message").value;
            socket.send(msg);
        }
        var user_id = '<?=Yii::$app->user->id?>';

        /*try {

            socket = new WebSocket('ws://localhost:8080/?user_id=' + user_id);
            //socket = new WebSocket('ws://localhost:8080/?lead_id=12345');

            socket.onopen = function (e) {
                //socket.send('{"user2_id":' + user_id + '}');
                //alert(1234);
                console.log('Socket Status: ' + socket.readyState + ' (Open)');
                //console.log(e);
            };
            socket.onmessage = function (e) {
                //alert(e.data);
                //var customWindow = window.open('', '_self', ''); customWindow.close();
                //location.href = '/';
                alert(e.data);
                console.log(e.data);
            };

            socket.onclose = function (e) {
                console.log('Socket Status: ' + socket.readyState + ' (Closed)');
            };

            socket.onerror = function(evt) {
                //if (socket.readyState == 1) {
                    console.log('Socket error: ' + evt.type);
                //}
            };


        } catch (e) {
            console.error(e);
        }*/

        //open(location, '_self').close();

        //window.top.close();

       /* function closeWin() {
            window.top.close();
        }
        setTimeout(function(){
            closeWin()
        }, 3000);*/

    </script>
</div>
