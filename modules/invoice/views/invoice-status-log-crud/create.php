<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\invoice\src\entities\invoiceStatusLog\InvoiceStatusLog */

$this->title = 'Create Invoice Status Log';
$this->params['breadcrumbs'][] = ['label' => 'Invoice Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
