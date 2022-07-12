<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule */

$this->title = 'Update Lead Business Extra Queue Rule: ' . $model->lbeqr_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queue Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lbeqr_id, 'url' => ['view', 'lbeqr_id' => $model->lbeqr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-business-extra-queue-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
