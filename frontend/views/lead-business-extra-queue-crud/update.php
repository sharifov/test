<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue */

$this->title = 'Update Lead Business Extra Queue: ' . $model->lbeq_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lbeq_lead_id, 'url' => ['view', 'lbeq_lead_id' => $model->lbeq_lead_id, 'lbeq_lbeqr_id' => $model->lbeq_lbeqr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-business-extra-queue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
