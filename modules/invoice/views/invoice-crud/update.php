<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\invoice\src\entities\invoice\Invoice */

$this->title = 'Update Invoice: ' . $model->inv_id;
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->inv_id, 'url' => ['view', 'id' => $model->inv_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
