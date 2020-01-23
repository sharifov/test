<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OrderProduct */

$this->title = $model->orp_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id], [
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
            'orp_order_id',
            'orp_product_quote_id',
            'orp_created_user_id',
            //'orp_created_dt',
            [
                'attribute' => 'orp_created_dt',
                'value' => static function(\common\models\OrderProduct $model) {
                    return $model->orp_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->orp_created_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
