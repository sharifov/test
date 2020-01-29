<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\invoice\src\entities\invoice\Invoice */

$this->title = $model->inv_id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="invoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->inv_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->inv_id], [
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
            'inv_id',
            'inv_gid',
            'inv_uid',
            'invOrder:order',
            'inv_status_id:invoiceStatus',
            'inv_sum',
            'inv_client_sum',
            'inv_client_currency',
            'inv_currency_rate',
            'inv_description:ntext',
            'invCreatedUser:userName',
            'invUpdatedUser:userName',
            'inv_created_dt:byUserDateTime',
            'inv_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
