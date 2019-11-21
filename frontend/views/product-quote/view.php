<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProductQuote */

$this->title = $model->pq_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pq_id], [
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
            'pq_id',
            'pq_gid',
            'pr_name',
            'pq_product_id',
            'pq_order_id',
            'pq_description:ntext',
            'pq_status_id',
            'pq_price',
            'pq_origin_price',
            'pq_client_price',
            'pq_service_fee_sum',
            'pq_origin_currency',
            'pq_client_currency',
            'pq_origin_currency_rate',
            'pq_client_currency_rate',
            'pq_owner_user_id',
            'pq_created_user_id',
            'pq_updated_user_id',
            [
                'attribute' => 'pq_created_dt',
                'value' => static function(\common\models\ProductQuote $model) {
                    return $model->pq_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pq_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'pq_updated_dt',
                'value' => static function(\common\models\ProductQuote $model) {
                    return $model->pq_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pq_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
