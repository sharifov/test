<?php

use common\components\grid\UserSelect2Column;
use common\models\Employee;
use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use modules\featureFlag\FFlag;
use src\services\email\EmailsNormalizeService;
use src\entities\email\helpers\EmailType;
use common\models\Language;
use src\entities\email\helpers\EmailStatus;

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
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'e_project_id',
                'relation' => 'project',
            ],
            [
                'attribute' => 'e_email_from',
                'value' => 'emailFrom'
            ],
            [
                'attribute' => 'e_email_to',
                'value' => 'emailTo'
            ],
            [
                'attribute' => 'e_lead_id',
                'value' => 'lead',
                'format' => 'lead',
            ],
            [
                'attribute' => 'e_case_id',
                'value' => 'case',
                'format' => 'case',
            ],
            [
                'attribute' => 'e_type_id',
                'value' => 'typeName',
                'filter' => EmailType::getList()
            ],
            [
                'attribute' => 'e_template_type_name',
                'value' => 'templateTypeName',
                'label' => 'Template Name'
                //'filter' =>
            ],
            [
                'attribute' => 'e_language_id',
                'value' => 'languageId',
                'filter' => Language::getLanguages(true)
            ],
            [
                'attribute' => 'e_communication_id',
                'value' => 'communicationId',
            ],
            [
                'attribute' => 'e_status_id',
                'value' => 'statusName',
                'filter' => EmailStatus::getList()
            ],
            [
                'attribute' => 'e_client_id',
                'value' => 'clientId',
                'format' => 'client'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'e_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => ''
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'e_created_dt'
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
