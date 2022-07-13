<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\experiment\models\ExperimentTarget */

$this->title = 'Update Experiment Target: ' . $model->ext_id;
$this->params['breadcrumbs'][] = ['label' => 'Experiment Targets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ext_id, 'url' => ['view', 'ext_id' => $model->ext_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="experiment-target-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
