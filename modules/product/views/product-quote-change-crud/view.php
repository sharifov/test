<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChange\ProductQuoteChange */

$this->title = $model->pqc_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-change-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pqc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pqc_id], [
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
            'pqc_id',
            'pqc_pq_id',
            'pqc_case_id',
            'pqc_decision_user',
            'pqc_status_id',
            'pqc_decision_type_id',
            'pqc_created_dt',
            'pqc_updated_dt',
            'pqc_decision_dt',
        ],
    ]) ?>

</div>