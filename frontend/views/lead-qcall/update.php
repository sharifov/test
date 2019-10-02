<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadQcall */

$this->title = 'Update Lead Qcall: ' . $model->lqc_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Qcalls', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lqc_lead_id, 'url' => ['view', 'id' => $model->lqc_lead_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-qcall-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
