<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklist */

$this->title = 'Update Lead Checklist: ' . $model->lc_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Checklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lc_type_id, 'url' => ['view', 'lc_type_id' => $model->lc_type_id, 'lc_lead_id' => $model->lc_lead_id, 'lc_user_id' => $model->lc_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-checklist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
