<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog */

$this->title = 'Create Lead Business Extra Queue Log';
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queue Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-business-extra-queue-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
