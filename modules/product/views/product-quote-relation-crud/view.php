<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRelation\ProductQuoteRelation */

$this->title = $model->pqr_parent_pq_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-relation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'pqr_parent_pq_id' => $model->pqr_parent_pq_id, 'pqr_related_pq_id' => $model->pqr_related_pq_id, 'pqr_type_id' => $model->pqr_type_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'pqr_parent_pq_id' => $model->pqr_parent_pq_id, 'pqr_related_pq_id' => $model->pqr_related_pq_id, 'pqr_type_id' => $model->pqr_type_id], [
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
                'pqr_parent_pq_id',
                'pqr_related_pq_id',
                'pqr_type_id',
                'pqr_created_user_id',
                'pqr_created_dt',
            ],
        ]) ?>

    </div>

</div>
