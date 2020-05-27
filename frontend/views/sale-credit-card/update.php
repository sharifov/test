<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SaleCreditCard */

$this->title = 'Update Sale Credit Card: ' . $model->scc_sale_id;
$this->params['breadcrumbs'][] = ['label' => 'Sale Credit Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->scc_sale_id, 'url' => ['view', 'scc_sale_id' => $model->scc_sale_id, 'scc_cc_id' => $model->scc_cc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sale-credit-card-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
