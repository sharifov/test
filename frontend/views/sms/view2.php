<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Sms */

$this->title = 'SMS - ' . $model->s_id;
$this->params['breadcrumbs'][] = ['label' => 'My Sms', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-list"></i> My SMS', ['list'], ['class' => 'btn btn-success']) ?>

        <?= Html::a('<i class="fa fa-trash"></i>  Delete', ['soft-delete', 'id' => $model->s_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-4">
        <pre><?=(Html::encode($model->s_sms_text))?></pre>
    </div>
    <div class="col-md-4">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [

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
                        return $model->sLead ? (Html::a($model->s_lead_id, ['lead/view', 'gid' => $model->sLead->gid], ['target' => '_blank'])) : '';
                    },
                    'format' => 'raw',
                ],

                //'s_reply_id',
                //'s_lead_id',
                //'s_project_id',
                [
                    'attribute' => 's_project_id',
                    'value' => static function (\common\models\Sms $model) {
                        return $model->sProject ? $model->sProject->name : '-';
                    },
                ],

                [
                    'attribute' => 's_phone_from',
                    'value' => static function (\common\models\Sms $model) {
                        return $model->s_phone_from;
                    },
                ],
                [
                    'attribute' => 's_phone_to',
                    'value' => static function (\common\models\Sms $model) {
                        return $model->s_phone_to;
                    },
                ],
            ]

        ]) ?>

    </div>
    <div class="col-md-4">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            's_id',


            //'s_sms_text:ntext',
            //'s_sms_data:ntext',
            //'s_type_id',

            //'s_template_type_id',
            //'s_language_id',
            [
                'attribute' => 's_language_id',
                'value' => static function (\common\models\Sms $model) {
                    return $model->s_language_id;
                },
            ],
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
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
</div>
