<?php

use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\services\url\UrlGenerator;
use modules\order\src\entities\order\Order;
use modules\order\src\processManager\phoneToBook\OrderProcessManager;
use src\auth\Auth;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var Order $order */
/* @var OrderProcessManager|null $orderProcessManage */
/* @var FileStorage[]|null $orderFiles */
/* @var UrlGenerator $urlGenerator */

$this->title = 'Order ' . $order->or_gid;
$this->params['breadcrumbs'][] = ['label' => 'Order search', 'url' => ['/order/order/search']];
$this->params['breadcrumbs'][] = $this->title;
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

            <?php if (Auth::can('order/view/products')) : ?>
                <?php echo $this->render('_partial/product', [
                    'order' => $order,
                ]) ?>
            <?php endif ?>

            <?php if (Auth::can('order/view/contacts')) : ?>
                <?php echo $this->render('_partial/contacts', [
                    'order' => $order,
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

            <?php if (Auth::can('global/transaction/list/view')) : ?>
                <?php echo $this->render('_partial/transaction', [
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
