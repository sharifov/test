<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation */

$this->title = $model->pqcr_pqc_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Change Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-change-relation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'pqcr_pqc_id' => $model->pqcr_pqc_id, 'pqcr_pq_id' => $model->pqcr_pq_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'pqcr_pqc_id' => $model->pqcr_pqc_id, 'pqcr_pq_id' => $model->pqcr_pq_id], [
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
                'pqcr_pqc_id',
                'pqcr_pq_id',
            ],
        ]) ?>

    </div>

</div>
