<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\saleTicket\entity\SaleTicket */

$this->title = 'Update Sale Ticket: ' . $model->st_case_id;
$this->params['breadcrumbs'][] = ['label' => 'Sale Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->st_case_id, 'url' => ['view', 'st_case_id' => $model->st_case_id, 'st_case_sale_id' => $model->st_case_sale_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sale-ticket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
