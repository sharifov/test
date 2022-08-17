<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskScenario */

$this->title = 'Update Object Task Scenario: ' . $model->ots_id;
$this->params['breadcrumbs'][] = ['label' => 'Object Task Scenarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ots_id, 'url' => ['view', 'ots_id' => $model->ots_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="object-task-scenario-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
