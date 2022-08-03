<?php

use common\models\Employee;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use src\helpers\phone\MaskPhoneHelper;
use common\models\Sms;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $phoneList [] */
/* @var $projectList [] */


$this->title = 'My Sms';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;
?>
<div class="sms-index">

    <?php /*if($user->isAdmin()) : ?>
    <div class="lead-search">
        <div class="row">
            <div class="col-md-3">
                <p>
                    <?= Html::a('Create Sms', ['create'], ['class' => 'btn btn-success']) ?>
                </p>
            </div>
            <div class="col-md-9">
                <?php
                echo $this->render('_inboxform', [
                    'model' => $inboxModel
                ]);
                ?>
            </div>
        </div>
    </div>
    <?php endif;*/ ?>

    <h1><i class="fa fa-comments-o"></i> <?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>

        <?php //= Html::a('<i class="fa fa-plus"></i> Create Sms', ['create'], ['class' => 'btn btn-success'])?>

        <?= Html::a('<i class="fa fa-check"></i> Make Read All', ['all-read'], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => 'Are you sure you want to mark read all SMS?',
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('<i class="fa fa-times"></i> Delete All', ['all-delete'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all SMS?',
                'method' => 'post',
            ],
        ]) ?>

    </p>


    <div class="row top_tiles">

        <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
            <h5>My Phone List (<?=count($phoneList)?>):</h5>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Nr</th>
                    <th>Phone</th>
                </tr>
                <?php
                $nr = 1;
                foreach ($phoneList as $phone) :?>
                    <tr>
                        <td style="width:100px"><?=($nr++)?></td>
                        <td><?=Html::encode($phone)?></td>
                    </tr>
                <?php endforeach; ?>

            </table>

            <?php /*<div class="tile-stats">
                <div class="icon"><i class="fa fa-comments"></i></div>
                <div class="count">

                    <?=\common\models\Sms::find()->where(['or', ['s_phone_to' => $phoneList], ['s_phone_from' => $phoneList]])
                        ->andWhere(['s_type_id' => \common\models\Sms::FILTER_TYPE_DRAFT, 's_is_deleted' => false])->count()?>
                </div>
                <h3>Draft</h3>
                <p>Draft count of SMS messages</p>
            </div>*/ ?>
        </div>

        <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-comments"></i></div>
                <div class="count">
                    <?= Sms::find()->where(['or', ['s_phone_to' => $phoneList], ['s_phone_from' => $phoneList]])
                        ->andWhere(['s_is_new' => true, 's_is_deleted' => false])->cache(30 * 60)->count()?>
                </div>
                <h3>New SMS (unread)</h3>
                <p>Total new (unread) SMS messages</p>
            </div>
        </div>

        <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-comments-o"></i></div>
                <div class="count">
                    <?= Sms::find()->where(['or', ['s_phone_to' => $phoneList], ['s_phone_from' => $phoneList]])
                        ->andWhere(['s_type_id' => Sms::TYPE_INBOX, 'DATE(s_created_dt)' => new \yii\db\Expression('DATE(NOW())'), 's_is_deleted' => false])->cache(30 * 60)->count()?>
                </div>
                <h3>Today Inbox</h3>
                <p>Today inbox count of SMS messages</p>
            </div>
        </div>

        <div class="animated flipInY col-md-2 col-sm-2 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-comments"></i></div>
                <div class="count">
                    <?= Sms::find()->where(['or', ['s_phone_to' => $phoneList], ['s_phone_from' => $phoneList]])
                        ->andWhere(['s_type_id' => Sms::TYPE_OUTBOX, 'DATE(s_created_dt)' => new \yii\db\Expression('DATE(NOW())'), 's_is_deleted' => false])->cache(30 * 60)->count()?>
                </div>
                <h3>Today Outbox</h3>
                <p>Today outbox count of SMS messages</p>
            </div>
        </div>

<!--        <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-trash"></i></div>
                <div class="count">
                    <?php /*echo Sms::find()->where(['or', ['s_phone_to' => $phoneList], ['s_phone_from' => $phoneList]])
                        ->andWhere(['s_type_id' => Sms::FILTER_TYPE_TRASH, 's_is_deleted' => false])->cache(30 * 60)->count()*/?>
                </div>
                <h3>Trash</h3>
                <p>Trash count of SMS messages</p>
            </div>
        </div>-->

        <?php /*
            <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count"><?=\frontend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()?></div>
                    <h3>System Logs</h3>
                    <p>Today count of System Logs</p>
                </div>
            </div>
            */ ?>
    </div>





    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Sms $model, $index, $widget, $grid) {
            if ($model->s_status_id == \common\models\Sms::STATUS_ERROR) {
                return ['class' => 'danger'];
            } elseif ($model->s_status_id == \common\models\Sms::STATUS_PROCESS) {
                return ['class' => 'warning'];
            } elseif ($model->s_status_id == \common\models\Sms::STATUS_DONE) {
                return ['class' => 'success'];
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'s_is_deleted',

            [
                'attribute' => 's_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_id;
                },
                'options' => ['style' => 'width: 100px']
            ],

            's_is_new:boolean',

            [
                'attribute' => 's_type_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->getTypeName();
                },
                'filter' => \common\models\Sms::FILTER_TYPE_LIST
            ],

            [
                'attribute' => 's_lead_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_lead_id ? Html::a($model->s_lead_id, ['lead/view', 'gid' => $model->sLead->gid], ['target' => '_blank']) : '';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 100px']
            ],


            //'s_reply_id',
            //'s_lead_id',
            //'s_project_id',
            [
                'attribute' => 's_project_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->sProject ? $model->sProject->name : '-';
                },
                'filter' => $projectList
            ],

            [
                'attribute' => 's_phone_from',
                'value' => static function (\common\models\Sms $model) {
                    if ($model->s_type_id == $model::TYPE_INBOX) {
                        return MaskPhoneHelper::masking($model->s_phone_from);
                    }
                    return $model->s_phone_from;
                },
                'filter' => $phoneList
            ],

            /*[
                'attribute' => 's_phone_from',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_phone_from;
                },
                'filter' => $phoneList
            ],*/

            [
                'attribute' => 's_phone_to',
                'value' => static function (\common\models\Sms $model) {
                    if ($model->s_type_id == $model::TYPE_OUTBOX) {
                        return MaskPhoneHelper::masking($model->s_phone_to);
                    }
                    return $model->s_phone_to;
                },
                'filter' => $phoneList
            ],

            /*[
                'attribute' => 's_phone_to',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_phone_to;
                },
                'filter' => $phoneList
            ],*/

            's_sms_text:ntext',
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
            //'s_is_deleted',
            //'s_is_new',
            //'s_delay',
            //'s_priority',
            [
                'attribute' => 's_status_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\Sms::STATUS_LIST
            ],
            //'s_status_done_dt',
            //'s_read_dt',
            //'s_error_message',
            //'s_tw_price',
            //'s_tw_sent_dt',
            //'s_tw_account_sid',
            //'s_tw_message_sid',
            //'s_tw_num_segments',
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
                'attribute' => 's_created_user_id',
                'value' => static function (\common\models\Sms $model) {
                    return  ($model->sCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->sCreatedUser->username) : $model->s_created_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 's_created_dt',
                'value' => static function (\common\models\Sms $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 's_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                //'controller' => 'order-shipping',
                'template' => '{view2} {soft-delete}',

                'buttons' => [
                    'view2' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-search"></i>', $url, [
                            'title' => 'View',
                        ]);
                    },
                    'soft-delete' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', $url, [
                            'title' => 'Delete',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this SMS?',
                                //'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
