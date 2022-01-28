<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessing\entity\LeadPoorProcessing */

$this->title = 'Update Lead Poor Processing: ' . $model->lpp_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lpp_lead_id, 'url' => ['view', 'lpp_lead_id' => $model->lpp_lead_id, 'lpp_lppd_id' => $model->lpp_lppd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-poor-processing-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
