<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VisitorLog */

$this->title = 'Update Visitor Log: ' . $model->vl_id;
$this->params['breadcrumbs'][] = ['label' => 'Visitor Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vl_id, 'url' => ['view', 'id' => $model->vl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="visitor-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
