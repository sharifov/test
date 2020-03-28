<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserBonusRules */

$this->title = 'User Bonus Rules - Detail';
$this->params['breadcrumbs'][] = ['label' => 'User Bonus Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-bonus-rules-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ubr_exp_month' => $model->ubr_exp_month, 'ubr_kpi_percent' => $model->ubr_kpi_percent, 'ubr_order_profit' => $model->ubr_order_profit], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ubr_exp_month' => $model->ubr_exp_month, 'ubr_kpi_percent' => $model->ubr_kpi_percent, 'ubr_order_profit' => $model->ubr_order_profit], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ubr_exp_month',
            'ubr_kpi_percent:percentInteger',
            'ubr_order_profit',
            'ubr_value',
            'ubr_created_user_id:username',
            'ubr_updated_user_id:username',
            'ubr_created_dt:byUserDateTime',
            'ubr_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
