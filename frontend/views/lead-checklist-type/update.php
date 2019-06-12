<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklistType */

$this->title = 'Update Lead Checklist Type: ' . $model->lct_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Checklist Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lct_id, 'url' => ['view', 'id' => $model->lct_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-checklist-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
