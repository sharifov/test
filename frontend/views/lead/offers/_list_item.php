<?php

/* @var $this yii\web\View */
/* @var $offer \modules\offer\src\entities\offer\Offer */
/* @var $index integer */

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferStatus;
use modules\offer\src\helpers\formatters\OfferFormatter;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\helpers\product\ProductQuoteHelper;
use yii\bootstrap4\Html;

?>

<div class="x_panel">
    <div class="x_title">

            <?= Html::checkbox('offer_checkbox[' . $offer->of_id . ']', false, ['id' => 'off_ch' . $offer->of_id, 'class' => 'offer-checkbox', 'data-id' => $offer->of_id, 'style' => 'width: 16px; height: 16px;'])?>
            <small><span class="badge badge-white">OF<?=($offer->of_id)?></span></small>
            <?= OfferStatus::asFormat($offer->of_status_id) ?>
            (<span title="GID: <?=\yii\helpers\Html::encode($offer->of_gid)?>"><?=\yii\helpers\Html::encode($offer->of_uid)?></span>)
            "<b><?=\yii\helpers\Html::encode($offer->of_name)?></b>"

             <?= OfferFormatter::asSentView($offer) ?>

            <?php if ($offer->of_profit_amount > 0) : ?>
                <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $offer->of_profit_amount ?>
            <?php endif; ?>

        <ul class="nav navbar-right panel_toolbox">
            <!--            <li>-->
            <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
            <!--            </li>-->
            <li>
                <?= Html::a('<i class="fa fa-edit warning"></i> Update offer', null, [
                    'data-url' => \yii\helpers\Url::to(['/offer/offer/update-ajax', 'id' => $offer->of_id]),
                    'class' => 'btn-update-offer'
                ])?>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                <div class="dropdown-menu" role="menu">
                    <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete offer', null, [
                        'class' => 'dropdown-item text-danger btn-delete-offer',
                        'data-offer-id' => $offer->of_id,
                        'data-url' => \yii\helpers\Url::to(['/offer/offer/delete-ajax']),
                    ]) ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-success"></i> Status log', null, [
                        'class' => 'dropdown-item text-success btn-offer-status-log',
                        'data-url' => \yii\helpers\Url::to(['/offer/offer-status-log/show', 'gid' => $offer->of_gid]),
                        'data-gid' => $offer->of_gid,
                    ]) ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-success"></i> Send log', null, [
                        'class' => 'dropdown-item text-success btn-offer-send-log',
                        'data-url' => \yii\helpers\Url::to(['/offer/offer-send-log/show', 'gid' => $offer->of_gid]),
                        'data-gid' => $offer->of_gid,
                    ]) ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-success"></i> View log', null, [
                        'class' => 'dropdown-item text-success btn-offer-view-log',
                        'data-url' => \yii\helpers\Url::to(['/offer/offer-view-log/show', 'gid' => $offer->of_gid]),
                        'data-gid' => $offer->of_gid,
                    ]) ?>

                    <?php  echo Html::a('<i class="fa fa-eye"></i> Checkout Page', $offer->getCheckoutUrlPage(), [
                        'class' => 'dropdown-item',
                        'target'    => '_blank',
                        'title'     => 'View checkout',
                        'data-pjax' => 0
                    ]) ?>

                    <?= Html::a('<i class="fa fa-camera"></i> Copy Checkout Link', null, [
                        'class' => 'btn-offer-copy-checkout-link dropdown-item',
                        'data-url' => $offer->getCheckoutUrlPage(),
                        'title' => 'Copy To Clipboard'
                    ]) ?>

                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <table class="table table-bordered">
        <?php if ($offer->offerProducts) :
            $originTotalPrice = 0;
            $clientTotalPrice = 0;
            $optionTotalPrice = 0;
            $totalFee = 0;

            ?>
            <tr>
<!--                <th>Quote ID</th>-->
                <th>Nr</th>
                <th>Product</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created</th>
                <th title="Options, USD">Options, USD</th>
                <th title="Service FEE">FEE</th>
                <th title="Origin Price, USD">Price, USD</th>
                <th>Client Price</th>
                <th></th>
            </tr>
            <?php if ($offer->offerProducts) :
                $nr = 1;
                ?>
                <?php foreach ($offer->offerProducts as $product) :
                        $quote = $product->opProductQuote;
                        $originTotalPrice += $quote->pq_price;
                        $clientTotalPrice += $quote->pq_price * $offer->of_client_currency_rate;
                        //$clientTotalPrice += ProductQuoteHelper::calcClientPrice($quote->pq_client_price, $quote->pqProduct);
                        $optionTotalPrice += $quote->optionAmountSum;
                        $totalFee += $quote->pq_service_fee_sum;
                    ?>
                    <tr>
                        <td title="Product Quote ID: <?=Html::encode($quote->pq_id)?>"><?= $nr++ ?></td>

                        <td title="<?=Html::encode($quote->pq_product_id)?>">
                            <?= $quote->pqProduct->prType->pt_icon_class ? Html::tag('i', '', ['class' => $quote->pqProduct->prType->pt_icon_class]) : '' ?>
                            <?=Html::encode($quote->pqProduct->prType->pt_name)?>
                            <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
                        </td>

    <!--                    <td>--><?php //=\yii\helpers\VarDumper::dumpAsString($quote->attributes, 10, true)?><!--</td>-->

                        <td><?=Html::encode($quote->pq_name)?></td>
                        <td><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
                        <td><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
                        <td class="text-right"><?=number_format($quote->optionAmountSum, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_service_fee_sum, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_price, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_price * $offer->of_client_currency_rate, 2)?> <?=Html::encode($offer->of_client_currency)?></td>
                        <td>
                            <?php
                              echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i>', null, [
                                    'data-offer-id' => $offer->of_id,
                                    'data-product-quote-id' => $quote->pq_id,
                                    'class' => 'btn-delete-quote-from-offer',
                                    'data-url' => \yii\helpers\Url::to(['/offer/offer-product/delete-ajax'])
                                ]);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th class="text-right" colspan="5">Sub Total: </th>
                    <th class="text-right"><?=number_format($optionTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($totalFee, 2)?></th>
                    <th class="text-right"><?=number_format($originTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($clientTotalPrice, 2)?> <?=Html::encode($offer->of_client_currency)?></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="5">Total: </th>
                    <td class="text-right" colspan="2">(price + opt)</td>
                    <th class="text-right"><?=number_format($originTotalPrice + $optionTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($clientTotalPrice + $optionTotalPrice, 2)?> <?=Html::encode($offer->of_client_currency)?></th>
                    <th></th>
                </tr>
            <?php endif; ?>
        <?php endif; ?>
        </table>

        <i class="fa fa-user"></i> <?=$offer->ofCreatedUser ? Html::encode($offer->ofCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($offer->of_created_dt)) ?>,
        <i class="fa fa-money" title="currency"></i> <?=Html::encode($offer->of_client_currency)?> <span title="Rate: <?=$offer->of_client_currency_rate?>">(<?=round($offer->of_client_currency_rate, 3)?>)</span>

        <div class="text-right">
            <?php $clientTotal = '' ?>
            <?php if (isset($clientTotalPrice) && !empty($offer->of_client_currency)) : ?>
                <?php $clientCurrency = $offer->of_client_currency ?: 'USD' ?>
                <?php $clientTotal = ', Client Total: <b>' . number_format($clientTotalPrice + ($optionTotalPrice ?? 0), 2) . ' ' . Html::encode($clientCurrency) . '</b>' ?>
            <?php endif ?>
            <h4>Total: <b><?=number_format($offer->offerTotalCalcSum, 2)?> USD</b><?php echo $clientTotal ?></h4>
        </div>


    </div>
</div>