<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\offer\src\entities\offerProduct\OfferProduct */

$this->title = 'Update Offer Product: ' . $model->op_offer_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->op_offer_id, 'url' => ['view', 'op_offer_id' => $model->op_offer_id, 'op_product_quote_id' => $model->op_product_quote_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="offer-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
