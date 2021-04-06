<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\caseOrder\entity\CaseOrder */

$this->title = 'Update Case Order: ' . $model->co_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Case Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->co_order_id, 'url' => ['view', 'co_order_id' => $model->co_order_id, 'co_case_id' => $model->co_case_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="case-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
