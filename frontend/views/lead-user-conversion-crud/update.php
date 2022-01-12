<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserConversion\entity\LeadUserConversion */

$this->title = 'Update Lead User Conversion: ' . $model->luc_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead User Conversions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->luc_lead_id, 'url' => ['view', 'luc_lead_id' => $model->luc_lead_id, 'luc_user_id' => $model->luc_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-user-conversion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
