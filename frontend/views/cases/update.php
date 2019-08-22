<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\Cases */

$this->title = 'Update Cases: ' . $model->cs_id;
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cs_id, 'url' => ['view', 'id' => $model->cs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cases-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
