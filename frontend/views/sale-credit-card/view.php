<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SaleCreditCard */

$this->title = $model->scc_sale_id;
$this->params['breadcrumbs'][] = ['label' => 'Sale Credit Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sale-credit-card-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'scc_sale_id' => $model->scc_sale_id, 'scc_cc_id' => $model->scc_cc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'scc_sale_id' => $model->scc_sale_id, 'scc_cc_id' => $model->scc_cc_id], [
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
            'scc_sale_id',
            'scc_cc_id',
            'scc_created_dt',
            'scc_created_user_id',
        ],
    ]) ?>

</div>
