<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserCommissionRules */

$this->title = 'User Commission Rules - Detail';
$this->params['breadcrumbs'][] = ['label' => 'User Commission Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-commission-rules-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ucr_exp_month' => $model->ucr_exp_month, 'ucr_kpi_percent' => $model->ucr_kpi_percent, 'ucr_order_profit' => $model->ucr_order_profit], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ucr_exp_month' => $model->ucr_exp_month, 'ucr_kpi_percent' => $model->ucr_kpi_percent, 'ucr_order_profit' => $model->ucr_order_profit], [
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
            'ucr_exp_month',
            'ucr_kpi_percent:percentInteger',
            'ucr_order_profit',
            'ucr_value:percentInteger',
            'ucr_created_user_id:username',
            'ucr_updated_user_id:username',
            'ucr_created_dt:byUserDateTime',
            'ucr_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
