<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ApiLog */

$this->title = 'Update Api Log: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Api Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->al_id, 'url' => ['view', 'id' => $model->al_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="api-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
