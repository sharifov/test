<?php

use sales\model\client\notifications\sms\entity\ClientNotificationSmsList;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ClientNotificationSmsList */

$this->title = $model->cnsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Notification Sms Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="client-notification-sms-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cnsl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cnsl_id], [
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
                'cnsl_id',
                'cnsl_status_id:clientNotificationSmsListStatus',
                'cnsl_from_phone_id',
                'cnsl_name_from',
                'cnsl_to_client_phone_id',
                'cnsl_start:byUserDatetime',
                'cnsl_end:byUserDatetime',
                [
                    'attribute' => 'cnsl_data_json',
                    'value' => static function (ClientNotificationSmsList $model) {
                        return $model->cnsl_data_json ? Json::encode($model->cnsl_data_json) : null;
                    },
                ],
                'cnsl_sms_id',
                'cnsl_created_dt:byUserDatetime',
                'cnsl_updated_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
