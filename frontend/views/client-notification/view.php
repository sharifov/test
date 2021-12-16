<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\client\entity\ClientNotification */

$this->title = $model->cn_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-notification-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cn_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cn_id], [
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
                'cn_id',
                'cn_client_id',
                'cn_notification_type_id:clientNotificationType',
                'cn_object_id',
                'cn_communication_type_id:clientNotificationCommunicationType',
                'cn_communication_object_id',
                'cn_created_dt:byUserDatetime',
                'cn_updated_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
