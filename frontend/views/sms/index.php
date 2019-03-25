<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) : ?>
    <div class="lead-search">
        <div class="row">
            <div class="col-md-3">
                <p>
                    <?/*= Html::a('Create Sms', ['create'], ['class' => 'btn btn-success'])*/ ?>
                </p>
            </div>
            <?php /*<div class="col-md-9">
                <?php
                echo $this->render('_inboxform', [
                    'model' => $inboxModel
                ]);
                ?>
            </div>*/ ?>
        </div>
    </div>
    <?php endif; ?>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Sms $model, $index, $widget, $grid) {
            if($model->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
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
                'value' => function (\common\models\Sms $model) {
                    return $model->s_id;
                },
                'options' => ['style' => 'width: 100px']
            ],

            ['class' => 'yii\grid\ActionColumn'],

            //'s_is_new:boolean',
            's_tw_message_sid',

            [
                'attribute' => 's_type_id',
                'value' => function (\common\models\Sms $model) {
                    return $model->getTypeName();
                },
                'filter' => \common\models\Sms::TYPE_LIST
            ],

            [
                'attribute' => 's_status_id',
                'value' => function (\common\models\Sms $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\Sms::STATUS_LIST
            ],

            [
                'attribute' => 's_project_id',
                'value' => function (\common\models\Sms $model) {
                    return $model->sProject ? $model->sProject->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],




            [
                'attribute' => 's_lead_id',
                'value' => function (\common\models\Sms $model) {
                    return $model->s_lead_id ? Html::a($model->s_lead_id, ['lead/view', 'gid' => $model->sLead->gid], ['target' => '_blank']) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 100px']
            ],


            //'s_reply_id',
            //'s_lead_id',
            //'s_project_id',


            's_phone_from',
            's_phone_to',
            's_sms_text:ntext',
            //'s_tw_num_segments',
            [
                'label' => 'Segments',
                'attribute' => 's_tw_num_segments',
                'value' => function (\common\models\Sms $model) {
                    return $model->s_tw_num_segments;
                },
                'options' => ['style' => 'width: 100px']
            ],
            //'s_sms_data:ntext',
            //'s_type_id',
            //'s_template_type_id',
            //'s_language_id',
            /*[
                'attribute' => 's_language_id',
                'value' => function (\common\models\Sms $model) {
                    return $model->s_language_id;
                },
                'filter' => \lajax\translatemanager\models\Language::getLanguageNames()
            ],*/
            //'s_communication_id',
            [
                    'label' => 'Comm Id',
                'attribute' => 's_communication_id',
                'value' => function (\common\models\Sms $model) {
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
                'value' => function (\common\models\Email $model) {
                    return ($model->eUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->eUpdatedUser->username) : $model->e_updated_user_id);
                },
                'format' => 'raw'
            ],*/
            /*[
                'attribute' => 's_updated_dt',
                'value' => function (\common\models\Sms $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_updated_dt));
                },
                'format' => 'raw'
            ],*/

            [
                'attribute' => 's_created_user_id',
                'value' => function (\common\models\Sms $model) {
                    return  ($model->sCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sCreatedUser->username) : $model->s_created_user_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],
            [
                'attribute' => 's_created_dt',
                'value' => function (\common\models\Sms $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_created_dt));
                },
                'format' => 'raw'
            ],


        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
