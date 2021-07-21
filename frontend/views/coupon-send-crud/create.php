<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponSend\CouponSend */

$this->title = 'Create Coupon Send';
$this->params['breadcrumbs'][] = ['label' => 'Coupon Sends', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-send-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
