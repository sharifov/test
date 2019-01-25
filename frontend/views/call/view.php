<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Call */

$this->title = $model->c_id;
$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->c_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->c_id], [
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
            'c_id',
            'c_call_sid',
            'c_account_sid',
            'c_call_type_id',
            'c_from',
            'c_to',
            'c_sip',
            'c_call_status',
            'c_api_version',
            'c_direction',
            'c_forwarded_from',
            'c_caller_name',
            'c_parent_call_sid',
            'c_call_duration',
            'c_sip_response_code',
            'c_recording_url:url',
            'c_recording_sid',
            'c_recording_duration',
            'c_timestamp',
            'c_uri',
            'c_sequence_number',
            'c_lead_id',
            'c_created_user_id',
            'c_created_dt',
            'c_com_call_id',
            'c_updated_dt',
            'c_project_id',
            'c_error_message',
            'c_is_new',
            'c_is_deleted',
        ],
    ]) ?>

</div>
