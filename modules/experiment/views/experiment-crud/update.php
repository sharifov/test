<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\experiment\models\Experiment */

$this->title = 'Update Experiment: ' . $model->ex_id;
$this->params['breadcrumbs'][] = ['label' => 'Experiments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ex_id, 'url' => ['view', 'ex_id' => $model->ex_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="experiment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
