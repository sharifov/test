<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\PhoneDeviceLog */

$this->title = $model->pdl_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Device Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-device-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'pdl_id' => $model->pdl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'pdl_id' => $model->pdl_id], [
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
                'pdl_id',
                'pdl_user_id',
                'pdl_device_id',
                'pdl_level',
                'pdl_message',
                'pdl_error',
                'pdl_timestamp_ts:datetime',
                'pdl_created_dt',
            ],
        ]) ?>

    </div>

</div>
