<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReason\entity\LeadStatusReason */

$this->title = 'Create Lead Status Reason';
$this->params['breadcrumbs'][] = ['label' => 'Lead Status Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-status-reason-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
