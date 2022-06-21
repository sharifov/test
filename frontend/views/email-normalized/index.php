<?php

use common\components\grid\UserSelect2Column;
use common\models\Employee;
use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use src\helpers\email\MaskEmailHelper;
use kartik\daterange\DateRangePicker;
use src\entities\email\Email;
use common\models\Language;
use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailStatus;

/* @var $this yii\web\View */
/* @var $searchModel src\entities\email\EmailSearch */
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
            <?= DateRangePicker::widget([
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
            ]);?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'e_id',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
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
                'attribute' => 'email_from',
                'value' => static function (Email $model) {
                    return $model->emailFrom->getEmail(EmailType::isInbox($model->e_type_id));
                },
            ],
            [
                'attribute' => 'email_to',
                'value' => static function (Email $model) {
                    return $model->emailTo->getEmail(EmailType::isOutbox($model->e_type_id));
                },
            ],
            'e_lead_id',
            'e_case_id',
            [
                'attribute' => 'e_type_id',
                'value' => static function (Email $model) {
                    return EmailType::getName($model->e_type_id);
                },
                'filter' => EmailType::getList()
            ],
            [
                'attribute' => 'e_template_type_name',
                'value' => static function (Email $model) {
                    return $model->templateType ? $model->templateType->etp_name : '-';
                },
                'label' => 'Template Name'
            ],
            [
                'attribute' => 'e_language_id',
                'value' => static function (Email $model) {
                    return $model->language;
                },
                'filter' => Language::getLanguages(true)
            ],
            'e_communication_id',
            [
                'attribute' => 'e_status_id',
                'value' => static function (Email $model) {
                    return EmailStatus::getName($model->e_status_id);
                },
                'filter' => EmailStatus::getList()
            ],
            'attribute' => 'e_client_id:client',
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

    </script>
</div>
