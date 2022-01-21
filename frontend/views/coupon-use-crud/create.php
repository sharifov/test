<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponUse\CouponUse */

$this->title = 'Create Coupon Use';
$this->params['breadcrumbs'][] = ['label' => 'Coupon Uses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-use-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
