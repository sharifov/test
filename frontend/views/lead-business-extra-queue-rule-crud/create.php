<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule */

$this->title = 'Create Lead Business Extra Queue Rule';
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queue Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-business-extra-queue-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
