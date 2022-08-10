<?php

use common\components\grid\UserSelect2Column;
use common\components\grid\DateTimeColumn;
use common\models\Employee;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use src\helpers\phone\MaskPhoneHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;
?>
<div class="sms-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php /*if($user->isAdmin()) : */?><!--
    <div class="lead-search">
        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php /*= Html::a('Create Sms', ['create'], ['class' => 'btn btn-success'])*/?>
                </p>
            </div>
            <?php /*<div class="col-md-9">
                <?php
                echo $this->render('_inboxform', [
                    'model' => $inboxModel
                ]);
                */?>
            </div> ?>
        </div>
    </div>
    --><?php /*endif; */?>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1,
                'style' => 'width: 100%;'
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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Sms $model, $index, $widget, $grid) {
            if ($model->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
                if ($model->s_status_id == \common\models\Sms::STATUS_ERROR) {
                    return ['class' => 'danger'];
                } elseif ($model->s_status_id == \common\models\Sms::STATUS_PROCESS) {
                    return ['class' => 'warning'];
                } elseif ($model->s_status_id == \common\models\Sms::STATUS_DONE) {
                    return ['class' => 'success'];
                }
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 's_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_id;
                },
                'options' => ['style' => 'width: 100px']
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

            //'s_is_new:boolean',


            [
                'attribute' => 's_type_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->getTypeName();
                },
                'filter' => \common\models\Sms::TYPE_LIST
            ],

            [
                'attribute' => 's_status_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\Sms::STATUS_LIST
            ],

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 's_project_id',
                'relation' => 'sProject',
            ],


//            [
//                'attribute' => 's_project_id',
//                'value' => static function (\common\models\Sms $model) {
//                    return $model->sProject ? '<span class="badge badge-info">' . Html::encode($model->sProject->name) . '</span>' : '-';
//                },
//                'format' => 'raw',
//                'filter' => $projectList
//            ],


            [
                'attribute' => 's_lead_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_lead_id ? Html::a($model->s_lead_id, ['lead/view', 'gid' => $model->sLead->gid], ['target' => '_blank']) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 100px']
            ],

            [
                'attribute' => 's_case_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_case_id ? Html::a($model->s_case_id, ['cases/view', 'gid' => $model->sCase->cs_gid], ['target' => '_blank']) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 100px']
            ],

            //'s_reply_id',
            //'s_lead_id',
            //'s_project_id',

            //'s_phone_from',
            [
                'attribute' => 's_phone_from',
                'value' => static function (\common\models\Sms $model) {
                    if ($model->s_type_id == $model::TYPE_INBOX) {
                        return MaskPhoneHelper::masking($model->s_phone_from);
                    }
                    return $model->s_phone_from;
                }
            ],
            //'s_phone_to',
            [
                'attribute' => 's_phone_to',
                'value' => static function (\common\models\Sms $model) {
                    if ($model->s_type_id == $model::TYPE_OUTBOX) {
                        return MaskPhoneHelper::masking($model->s_phone_to);
                    }
                    return $model->s_phone_to;
                }
            ],
            's_sms_text:ntext',
            //'s_tw_num_segments',
            [
                'label' => 'Segments',
                'attribute' => 's_tw_num_segments',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_tw_num_segments;
                },
                'options' => ['style' => 'width: 100px']
            ],
            's_client_id:client',
            's_tw_message_sid',
            //'s_sms_data:ntext',
            //'s_type_id',
            //'s_template_type_id',
            //'s_language_id',
            /*[
                'attribute' => 's_language_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_language_id;
                },
                'filter' => \common\models\Language::getLanguages(true)
            ],*/
            //'s_communication_id',
            [
                'label' => 'Comm Id',
                'attribute' => 's_communication_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_communication_id;
                },
                'options' => ['style' => 'width: 100px']
            ],
            //'s_is_deleted',
            //'s_is_new',
            //'s_delay',
            //'s_priority',
            //'s_status_done_dt',
            //'s_read_dt',
            //'s_error_message',
            's_tw_price',
            //'s_tw_sent_dt',
            //'s_tw_account_sid',
            //'s_tw_message_sid',

            //'s_tw_to_country',
            //'s_tw_to_state',
            //'s_tw_to_city',
            //'s_tw_to_zip',
            //'s_tw_from_country',
            //'s_tw_from_state',
            //'s_tw_from_city',
            //'s_tw_from_zip',
            /*'s_created_user_id',
            's_updated_user_id',
            's_created_dt',
            's_updated_dt',*/
            /*[
                'attribute' => 'e_updated_user_id',
                'value' => static function (\common\models\Email $model) {
                    return ($model->updatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->updatedUser->username) : $model->e_updated_user_id);
                },
                'format' => 'raw'
            ],*/
            /*[
                'attribute' => 's_updated_dt',
                'value' => static function (\common\models\Sms $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_updated_dt));
                },
                'format' => 'raw'
            ],*/

            [
                'class' => UserSelect2Column::class,
                'attribute' => 's_created_user_id',
                'relation' => 'sCreatedUser',
                'placeholder' => ''
            ],

//            [
//                'attribute' => 's_created_user_id',
//                'value' => static function (\common\models\Sms $model) {
//                    return  ($model->sCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sCreatedUser->username) : $model->s_created_user_id);
//                },
//                'format' => 'raw',
//                'filter' => $userList
//            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 's_created_dt'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
