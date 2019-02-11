<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ClientPhone */

$this->title = 'Update Client Phone: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Client Phones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-phone-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
