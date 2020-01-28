<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderStatusLog\OrderStatusLog */

$this->title = $model->orsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->orsl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->orsl_id], [
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
            'orsl_id',
            'order:order',
            'orsl_start_status_id:orderStatus',
            'orsl_end_status_id:orderStatus',
            'orsl_start_dt:byUserDateTime',
            'orsl_end_dt:byUserDateTime',
            'orsl_duration',
            'orsl_description',
            'orsl_action_id:orderStatusAction',
            'orsl_owner_user_id:userName',
            'orsl_created_user_id:userName',
        ],
    ]) ?>

</div>
