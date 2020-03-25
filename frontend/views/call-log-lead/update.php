<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogLead\CallLogLead */

$this->title = 'Update Call Log Lead: ' . $model->cll_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cll_cl_id, 'url' => ['view', 'cll_cl_id' => $model->cll_cl_id, 'cll_lead_id' => $model->cll_lead_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-log-lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
