<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserCommissionRules */

$this->title = 'Update User Commission Rules: ' . $model->ucr_exp_month;
$this->params['breadcrumbs'][] = ['label' => 'User Commission Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ucr_exp_month, 'url' => ['view', 'ucr_exp_month' => $model->ucr_exp_month, 'ucr_kpi_percent' => $model->ucr_kpi_percent, 'ucr_order_profit' => $model->ucr_order_profit]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-commission-rules-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
