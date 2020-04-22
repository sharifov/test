<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\twilio\src\entities\voiceLog\VoiceLog */

$this->title = $model->vl_id;
$this->params['breadcrumbs'][] = ['label' => 'Voice Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="voice-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->vl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->vl_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'vl_id',
            'vl_call_sid',
            'vl_account_sid',
            'vl_from',
            'vl_to',
            'vl_call_status',
            'vl_api_version',
            'vl_direction',
            'vl_forwarded_from',
            'vl_caller_name',
            'vl_parent_call_sid',
            'vl_call_duration',
            'vl_sip_response_code',
            'vl_recording_url:url',
            'vl_recording_sid',
            'vl_recording_duration',
            'vl_timestamp',
            'vl_callback_source',
            'vl_sequence_number',
            'vl_created_dt',
        ],
    ]) ?>

</div>
