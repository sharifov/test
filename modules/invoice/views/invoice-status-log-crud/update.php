<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\invoice\src\entities\invoiceStatusLog\InvoiceStatusLog */

$this->title = 'Update Invoice Status Log: ' . $model->invsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Invoice Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->invsl_id, 'url' => ['view', 'id' => $model->invsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invoice-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
