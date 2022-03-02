<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReasonLog\entity\LeadStatusReasonLog */

$this->title = 'Create Lead Status Reason Log';
$this->params['breadcrumbs'][] = ['label' => 'Lead Status Reason Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-status-reason-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
