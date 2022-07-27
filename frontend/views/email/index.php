<?php

use common\components\grid\UserSelect2Column;
use common\models\Employee;
use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use src\helpers\email\MaskEmailHelper;
use modules\featureFlag\FFlag;
use src\services\email\EmailsNormalizeService;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Emails';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>
<div class="email-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin([
        'id' => 'emails',
        'timeout' => 5000,
        'scrollTo' => 0
    ]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?php if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE)) : ?>
    	<?php $normalizedTotal = EmailsNormalizeService::getTotalConnectedWithOld();
    	   $oldTotal = EmailsNormalizeService::getOldTotal();
    	   $alertClass = ($normalizedTotal < $oldTotal) ? 'warning' : 'info';
    	?>
        <div class="alert alert-<?= $alertClass?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fa fa-info-circle"></i> Normalized emails <?=  $normalizedTotal?> from <?=  $oldTotal?>
        </div>
    <?php endif ?>

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
                'model' => $searchModel,
                'attribute' => 'date_range',
                'useWithAddon' => true,
                'presetDropdown' => true,
                'hideInput' => true,
                'convertFormat' => true,
                'startAttribute' => 'datetime_start',
                'endAttribute' => 'datetime_end',
                'pluginOptions' => [
                    'timePicker' => true,
                    'timePickerIncrement' => 1,
                    'timePicker24Hour' => true,
                    'locale' => [
                        'format' => 'Y-m-d H:i',
                        'separator' => ' - '
                    ],
                    'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
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
        <?php /*= Html::a('Create Email', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'e_id',
            [
                'attribute' => 'normalized',
                'visible' => Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE),
                'value' => static function (\common\models\Email $model) {
                    return $model->normalized ?
                        Html::a('<span class="label label-success">yes</span>', ['email-normalized/view', 'id' => $model->normalized->e_id], ['target' => '_blank', 'data-pjax' => 0]) :
                        Html::button('<i class="fa fa-refresh"></i> Normalize', [
                            'class' => 'btn btn-warning btn-xs take-processing-btn js_email_normalize',
                            'data-url' => \yii\helpers\Url::to(['email-normalized/normalize', 'id' => $model->e_id]),
                        ]);
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'update' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },

                    'delete' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },
                ],
            ],
            //'e_reply_id',

            //'e_project_id',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'e_project_id',
                'relation' => 'eProject',
            ],
//            [
//                'attribute' => 'e_project_id',
//                'value' => static function (\common\models\Email $model) {
//                    return $model->project ? '<span class="badge badge-info">' . Html::encode($model->project->name) . '</span>' : '-';
//                },
//                'format' => 'raw',
//                'filter' => $projectList
//            ],
            //'e_email_from',
            [
                'attribute' => 'e_email_from',
                'value' => static function (\common\models\Email $model) {
                    if ($model->e_type_id == $model::TYPE_INBOX) {
                        return MaskEmailHelper::masking($model->e_email_from);
                    }
                    return $model->e_email_from;
                },
            ],
            //'e_email_to',
            [
                'attribute' => 'e_email_to',
                'value' => static function (\common\models\Email $model) {
                    if ($model->e_type_id == $model::TYPE_OUTBOX) {
                        return MaskEmailHelper::masking($model->e_email_to);
                    }
                    return $model->e_email_to;
                },
            ],
            'e_lead_id',
            'e_case_id',
            //'e_email_cc:email',
            //'e_email_bc:email',
            //'e_email_subject:email',
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
                return $model->templateType ? $model->templateType->etp_name : '-';
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
                'filter' => \common\models\Language::getLanguages(true)
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
            'attribute' => 'e_client_id:client',
            //'e_status_done_dt',
            //'e_read_dt',
            //'e_error_message',
            /*[
                'attribute' => 'e_updated_user_id',
                'value' => static function (\common\models\Email $model) {
                    return ($model->updatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->updatedUser->username) : $model->e_updated_user_id);
                },
                'filter' => $userList,
                'format' => 'raw'
            ],*/

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'e_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => ''
            ],

//            [
//                'attribute' => 'e_created_user_id',
//                'value' => static function (\common\models\Email $model) {
//                    return ($model->createdUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->createdUser->username) : $model->e_created_user_id);
//                },
//                'filter' => $userList,
//                'format' => 'raw'
//            ],
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
                    return  ($model->createdUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->createdUser->username) : $model->e_created_user_id);
                'format' => 'raw'
                },
            ],*/
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'e_created_dt'
            ],
            /*[
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
            ],*/


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

<?php
$jsCode = <<<JS
    $(document).on('click', '.js_email_normalize', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var url = $(this).data('url');
        var elm = $(this);
        elm.html('<i class="fa fa-sync fa-spin"></i>');

        $.post( url, function() {})
          .done(function(data) {
            elm.parent().html(data.html);
          })
          .fail(function(data) {
            elm.html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>');
          });
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
