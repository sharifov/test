<?php

use sales\helpers\call\CallHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Call */

$this->title = 'Call Id: ' . $model->c_id . ' (' . $model->c_from . ' ... ' . $model->c_to . ')';
$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-view">

    <h1><i class="fa fa-phone-square"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?php /*= Html::a('Update', ['update', 'id' => $model->c_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->c_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <div class="col-md-6">
        <?php if ($model->recordingUrl) :?>
            <?= CallHelper::displayAudioTag($model->recordingUrl, ['style' => 'width: 100%']) ?>
        <?php endif;?>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'c_id',
                'c_call_sid',
                'c_parent_call_sid',
                //'c_call_type_id',
                [
                    'attribute' => 'c_call_type_id',
                    'value' => static function (\common\models\Call $model) {
                        return $model->getCallTypeName();
                    },
                ],
                'c_from',
                'c_to',
                'c_call_status',
                [
                    'attribute' => 'c_client_id',
                    'value' => static function (\common\models\Call $model) {
                        return  $model->c_client_id ?: '-';
                    },
                ],
                'c_language_id',

                //'format' => 'raw'
                //'c_forwarded_from',

                //'c_parent_call_sid',

            ],
        ]) ?>
    </div>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'c_caller_name',
            'c_call_duration',
            //'c_recording_url:url',
            'c_recording_duration',
            //'c_sequence_number',
            'c_lead_id',
            //'c_created_user_id',
            [
                'attribute' => 'c_created_user_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'format' => 'raw'
            ],
            //'c_created_dt',
            [
                'attribute' => 'c_created_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'c_updated_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
            //'c_com_call_id',
            //'c_updated_dt',
            //'c_project_id',
            [
                'attribute' => 'c_project_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->cProject ? $model->cProject->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],
            //'c_error_message',
            'c_is_new:boolean',
        ],
    ]) ?>
    </div>

</div>
