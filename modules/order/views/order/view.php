<?php

use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\services\url\UrlGenerator;
use modules\order\src\entities\order\Order;
use modules\order\src\processManager\OrderProcessManager;
use sales\auth\Auth;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var Order $order */
/* @var OrderProcessManager|null $orderProcessManage */
/* @var FileOrder[]|null $orderFiles */
/* @var UrlGenerator $urlGenerator */

$this->title = $order->or_name;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <div class="row">
        <div class="col-md-6">
            <?php if (Auth::can('order/view/order')) : ?>
                <?php echo $this->render('_partial/order', [
                    'order' => $order,
                    'orderProcessManage' => $orderProcessManage,
                ]) ?>
            <?php endif ?>

            <?php if (Auth::can('order/view/file')) : ?>
                <?php echo $this->render('_partial/file', [
                    'order' => $order,
                    'orderFiles' => $orderFiles,
                    'urlGenerator' => $urlGenerator,
                ]) ?>
            <?php endif ?>

            <?php if (Auth::can('order/view/additionalInfo')) : ?>
                <?php echo $this->render('_partial/additional', [
                    'order' => $order,
                ]) ?>
            <?php endif ?>
        </div>

        <div class="col-md-6">
            <?php if (Auth::can('order/view/invoice')) : ?>
                <?php echo $this->render('_partial/invoice', [
                    'order' => $order,
                ]) ?>
            <?php endif ?>

            <?php if (Auth::can('order/view/payment')) : ?>
                <?php echo $this->render('_partial/payment', [
                    'order' => $order,
                ]) ?>
            <?php endif ?>

            <?php if (Auth::can('order/view/billingInfo')) : ?>
                <?php echo $this->render('_partial/billing_info', [
                    'order' => $order,
                ]) ?>
            <?php endif ?>
        </div>

    </div>
</div>
