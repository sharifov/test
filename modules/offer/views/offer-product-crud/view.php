<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\offer\src\entities\offerProduct\OfferProduct */

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
            'opOffer:offer',
            'opProductQuote:productQuote',
            'opCreatedUser:userName',
            'op_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
