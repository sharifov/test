<?php

use sales\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ClientNotificationPhoneList */

$this->title = $model->cnfl_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Notification Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="client-notification-phone-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cnfl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cnfl_id], [
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
                'cnfl_id',
                'cnfl_status_id:clientNotificationPhoneListStatus',
                'cnfl_from_phone_id',
                'cnfl_to_client_phone_id',
                'cnfl_start:byUserDatetime',
                'cnfl_end:byUserDatetime',
                'cnfl_from_hours',
                'cnfl_to_hours',
                'cnfl_message:ntext',
                'cnfl_file_url:url',
                [
                    'attribute' => 'cnfl_data_json',
                    'value' => static function (ClientNotificationPhoneList $model) {
                        return $model->cnfl_data_json ? Json::encode($model->cnfl_data_json) : null;
                    },
                ],
                'cnfl_call_sid',
                'cnfl_created_dt:byUserDatetime',
                'cnfl_updated_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
