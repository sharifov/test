<?php
/* @var $this yii\web\View */
/* @var $offer \modules\offer\src\entities\offer\Offer */
/* @var $index integer */

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\bootstrap4\Html;

?>

<div class="x_panel">
    <div class="x_title">

            <?= Html::checkbox('offer_checkbox['.$offer->of_id.']', false, ['id' => 'off_ch' . $offer->of_id, 'class' => 'offer-checkbox', 'data-id' => $offer->of_id, 'style' => 'width: 16px; height: 16px;'])?>
            <small><span class="badge badge-white">OF<?=($offer->of_id)?></span></small>
            "<b><?=\yii\helpers\Html::encode($offer->of_name)?></b>"
            (<span title="UID"><?=\yii\helpers\Html::encode($offer->of_uid)?></span>)
             <?= OfferStatus::asFormat($offer->of_status_id) ?>

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
                    <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete offer', null, [
                        'class' => 'dropdown-item text-danger btn-delete-offer',
                        'data-offer-id' => $offer->of_id,
                        'data-url' => \yii\helpers\Url::to(['/offer/offer/delete-ajax']),
                    ]) ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-success"></i> Status log', null, [
                        'class' => 'dropdown-item text-success btn-offer-status-history',
                        'data-url' => \yii\helpers\Url::to(['/offer/offer-status-log/show', 'gid' => $offer->of_gid]),
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <table class="table table-bordered">
        <?php if ($offer->offerProducts):

            $originTotalPrice = 0;
            $clientTotalPrice = 0;
            $optionTotalPrice = 0;
            $totalFee = 0;

            ?>
            <tr>
                <th>Quote ID</th>
                <th>Type</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created</th>
                <th title="Options, USD">Options, USD</th>
                <th title="Service FEE">FEE</th>
                <th title="Origin Price, USD">Price, USD</th>
                <th>Client Price</th>
                <th></th>
            </tr>
            <?php if ($offer->offerProducts):?>
                <?php foreach ($offer->offerProducts as $product):
                        $quote = $product->opProductQuote;
                        $originTotalPrice += $quote->pq_price;
                        $clientTotalPrice += $quote->pq_client_price;
                        $optionTotalPrice += $quote->optionAmountSum;
                        $totalFee += $quote->pq_service_fee_sum;
                    ?>
                    <tr>
                        <td title="Product Quote ID"><?=Html::encode($quote->pq_id)?></td>
                        <td title="<?=Html::encode($quote->pq_product_id)?>">
                            <?=Html::encode($quote->pqProduct->prType->pt_name)?>
                            <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
                        </td>

    <!--                    <td>--><?//=\yii\helpers\VarDumper::dumpAsString($quote->attributes, 10, true)?><!--</td>-->

                        <td><?=Html::encode($quote->pq_name)?></td>
                        <td><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
                        <td><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
                        <td class="text-right"><?=number_format($quote->optionAmountSum, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_service_fee_sum, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_price, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_client_price, 2)?> <?=Html::encode($quote->pq_client_currency)?></td>
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
                    <th class="text-right"><?=number_format($clientTotalPrice, 2)?> <?=Html::encode($quote->pq_client_currency)?></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-right" colspan="5">Total: </th>
                    <td class="text-right" colspan="2">(price + opt + fee)</td>
                    <th class="text-right"><?=number_format($originTotalPrice + $optionTotalPrice + $totalFee, 2)?></th>
                    <th class="text-right"><?//=number_format($clientTotalPrice, 2)?> <?=Html::encode($quote->pq_client_currency)?></th>
                    <th></th>
                </tr>
            <?php endif; ?>
        <?php endif; ?>
        </table>

        <i class="fa fa-user"></i> <?=$offer->ofCreatedUser ? Html::encode($offer->ofCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($offer->of_created_dt)) ?>,
        <i class="fa fa-money" title="currency"></i> <?=Html::encode($offer->of_client_currency)?> <span title="Rate: <?=$offer->of_client_currency_rate?>">(<?=round($offer->of_client_currency_rate, 3)?>)</span>

        <div class="text-right"><h4>Total: <?=number_format($offer->offerTotalCalcSum, 2)?> USD</h4></div>


    </div>
</div>