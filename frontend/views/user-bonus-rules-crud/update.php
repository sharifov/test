<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserBonusRules */

$this->title = 'Update User Bonus Rules: ' . $model->ubr_exp_month;
$this->params['breadcrumbs'][] = ['label' => 'User Bonus Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ubr_exp_month, 'url' => ['view', 'ubr_exp_month' => $model->ubr_exp_month, 'ubr_kpi_percent' => $model->ubr_kpi_percent, 'ubr_order_profit' => $model->ubr_order_profit]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-bonus-rules-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
