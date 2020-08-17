<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Airports */

$this->title = 'Update Airports: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Airports', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->iata]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="airports-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
