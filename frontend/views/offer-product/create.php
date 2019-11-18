<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OfferProduct */

$this->title = 'Create Offer Product';
$this->params['breadcrumbs'][] = ['label' => 'Offer Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
