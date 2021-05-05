<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\leadOrder\entity\LeadOrder */

$this->title = 'Update Lead Order: ' . $model->lo_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lo_order_id, 'url' => ['view', 'lo_order_id' => $model->lo_order_id, 'lo_lead_id' => $model->lo_lead_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
