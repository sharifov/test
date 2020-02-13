<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\invoice\src\entities\invoiceStatusLog\InvoiceStatusLog */

$this->title = $model->invsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Invoice Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="invoice-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->invsl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->invsl_id], [
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
            'invsl_id',
            'invoice:invoice',
            'invsl_start_status_id:invoiceStatus',
            'invsl_end_status_id:invoiceStatus',
            'invsl_start_dt:byUserDateTime',
            'invsl_end_dt:byUserDateTime',
            'invsl_duration:duration',
            'invsl_description',
            'invsl_action_id:invoiceStatusAction',
            'invsl_created_user_id:userName',
        ],
    ]) ?>

</div>
