<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProfitSplit */

$this->title = 'Update Profit Split: ' . $model->ps_id;
$this->params['breadcrumbs'][] = ['label' => 'Profit Split', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ps_id, 'url' => ['view', 'id' => $model->ps_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="profit-split-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
