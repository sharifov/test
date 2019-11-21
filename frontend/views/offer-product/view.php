<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OfferProduct */

$this->title = $model->op_offer_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="offer-product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'op_offer_id' => $model->op_offer_id, 'op_product_quote_id' => $model->op_product_quote_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'op_offer_id' => $model->op_offer_id, 'op_product_quote_id' => $model->op_product_quote_id], [
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
            'op_offer_id',
            'op_product_quote_id',
            'op_created_user_id',
            [
                'attribute' => 'op_created_dt',
                'value' => static function(\common\models\OfferProduct $model) {
                    return $model->op_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->op_created_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
