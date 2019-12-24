<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyHistory */

$this->title = 'Update Currency History: ' . $model->ch_code;
$this->params['breadcrumbs'][] = ['label' => 'Currency Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ch_code, 'url' => ['view', 'ch_code' => $model->ch_code, 'ch_created_date' => $model->ch_created_date]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="currency-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
