<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\experiment\models\ExperimentTarget */

$this->title = 'Create Experiment Target';
$this->params['breadcrumbs'][] = ['label' => 'Experiment Targets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-target-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
