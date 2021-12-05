<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\device\PhoneDevice */

$this->title = $model->pd_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Devices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-device-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->pd_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->pd_id], [
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
                'pd_id',
                'pd_hash',
                'pd_connection_id',
                'pd_user_id:usernameWithId',
                'pd_name',
                'pd_device_identity',
                'pd_status_device:booleanByLabel',
                'pd_status_speaker:booleanByLabel',
                'pd_status_microphone:booleanByLabel',
                'pd_ip_address',
                'pd_created_dt:byUserDatetime',
                'pd_updated_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
