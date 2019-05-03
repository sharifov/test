<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadCallExpert */

$this->title = 'Update Lead Call Expert: ' . $model->lce_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Call Experts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lce_id, 'url' => ['view', 'id' => $model->lce_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-call-expert-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
