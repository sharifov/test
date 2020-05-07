<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponCase\CouponCase */

$this->title = 'Create Coupon Case';
$this->params['breadcrumbs'][] = ['label' => 'Coupon Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-case-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
