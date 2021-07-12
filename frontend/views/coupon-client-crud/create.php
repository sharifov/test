<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponClient\CouponClient */

$this->title = 'Create Coupon Client';
$this->params['breadcrumbs'][] = ['label' => 'Coupon Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-client-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
