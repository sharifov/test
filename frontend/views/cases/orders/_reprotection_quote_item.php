<?php

use modules\cases\src\abac\CasesAbacObject;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;

/**
 * @var Order $order
 * @var ProductQuote $quote
 * @var int $nr
 * @var int $caseId
 * @var \yii\web\View $this
 * @var bool $isReprotection
 * @var $caseAbacDto \modules\cases\src\abac\dto\CasesAbacDto
 */

$hasReprotection = false;
$reprotectionQuotes = [];

if ($nr && $reprotectionQuotes = ProductQuoteQuery::getReprotectionQuotesByOriginQuote($quote->pq_id)) {
    $hasReprotection = true;
}


$changeTitle = '';
if ($quote->productQuoteLastChange) {
    $titleData = [];
    if ($quote->productQuoteLastChange->pqc_id) {
        $titleData[] = 'PQ Change ID: ' . $quote->productQuoteLastChange->pqc_id;
    }
    if ($quote->productQuoteLastChange->pqc_decision_type_id) {
        $titleData[] = 'Client Decision: ' . ProductQuoteChangeDecisionType::getName($quote->productQuoteLastChange->pqc_decision_type_id);
    }

    if ($quote->productQuoteLastChange->pqc_decision_dt) {
        $titleData[] = 'Client Decision DateTime: ' . Yii::$app->formatter->asDatetime(strtotime($quote->productQuoteLastChange->pqc_decision_dt));
    }
    if ($titleData) {
        $changeTitle = implode(", \r\n", $titleData);
    }
}

?>

    <td data-toggle="tooltip" data-original-title="Product Quote ID: <?= Html::encode($quote->pq_id)?>, GID: <?= Html::encode($quote->pq_gid)?>" title="Product Quote ID: <?= Html::encode($quote->pq_id)?>, GID: <?= Html::encode($quote->pq_gid)?>"><?= $nr ?></td>
    <td title="Product ID: <?=Html::encode($quote->pq_product_id)?>">
        <?= $quote->pqProduct->prType->pt_icon_class ? Html::tag('i', '', ['class' => $quote->pqProduct->prType->pt_icon_class]) : '' ?>
        <?=Html::encode($quote->pqProduct->prType->pt_name)?>
        <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
    </td>

    <!--                    <td>--><?php //=\yii\helpers\VarDumper::dumpAsString($quote->attributes, 10, true)?><!--</td>-->

    <td><?=Html::encode($quote->getBookingId())?></td>
    <td><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
    <?php if ($quote->productQuoteLastChange) : ?>
        <td <?= $changeTitle ? 'data-toggle="tooltip" data-original-title="' . $changeTitle . '"' : ''?>>
            <?= $quote->productQuoteLastChange->pqc_status_id ? ProductQuoteChangeStatus::asFormat($quote->productQuoteLastChange->pqc_status_id) : '-' ?>
            <?= $quote->productQuoteLastChange->pqc_decision_type_id ? ' - ' . ProductQuoteChangeDecisionType::asFormat($quote->productQuoteLastChange->pqc_decision_type_id) : '' ?>
        </td>
    <?php else : ?>
        <td>-</td>
    <?php endif; ?>

    <?php if ($quote->productQuoteLastRefund) : ?>
        <td <?= $quote->productQuoteLastRefund->pqr_id ? 'data-toggle="tooltip" data-original-title="PQ Refund ID: ' . $quote->productQuoteLastRefund->pqr_id . '"' : ''?>>
            <?= $quote->productQuoteLastRefund->pqr_status_id ? ProductQuoteRefundStatus::asFormat($quote->productQuoteLastRefund->pqr_status_id) : '-' ?>
        </td>
    <?php else : ?>
        <td>-</td>
    <?php endif; ?>
    <td><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
    <td class="text-right"><?=number_format($quote->pq_client_price, 2)?> <?=Html::encode($quote->pq_client_currency)?></td>
    <td>
      <div class="btn-group">

        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i>
        </button>
        <div class="dropdown-menu">
            <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_VIEW_DETAILS, CasesAbacObject::ACTION_ACCESS, Product quote view details */ ?>
            <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_VIEW_DETAILS, CasesAbacObject::ACTION_ACCESS)) : ?>
                <?= Html::a('<i class="fas fa-info-circle" data-toggle="tooltip" title="Details"></i> View Details', null, [
                    'data-product-quote-gid' => $quote->pq_gid,
                    'class' => 'dropdown-item btn-show-product-quote-details',
                    'data-url' => Url::to([$quote->getQuoteDetailsPageUrl(), 'id' => $quote->pq_id])
                ]) ?>
            <?php endif; ?>

            <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_QUOTE, CasesAbacObject::ACTION_CREATE, Flight Create Reprotection quote from dump*/ ?>
            <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_QUOTE, CasesAbacObject::ACTION_CREATE)) : ?>
                <?php if ($flight = ArrayHelper::getValue($quote, 'flightQuote.fqFlight')) : ?>
                    <?= Html::a('<i class="fas fa-plus-circle" data-toggle="tooltip" title="Details"></i> Add ReProtection Quote', null, [
                        'data-flight-id' => $flight->getId(),
                        'class' => 'dropdown-item btn_create_from_dump',
                        'data-url' => Url::to(['/flight/flight-quote/create-re-protection-quote', 'flight_id' => $flight->getId()]),
                    ]) ?>
                <?php endif ?>
            <?php endif ?>

            <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_REMOVE, CasesAbacObject::ACTION_ACCESS, Remove product from order*/ ?>
            <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_REMOVE, CasesAbacObject::ACTION_ACCESS)) : ?>
                <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i> Remove', null, [
                    'data-order-id' => $order->or_id,
                    'data-product-quote-id' => $quote->pq_id,
                    'class' => 'dropdown-item btn-delete-quote-from-order',
                    'data-url' => \yii\helpers\Url::to(['/order/order-product/delete-ajax'])
                ]) ?>
            <?php endif; ?>
        </div>
      </div>
    </td>

    <?php if ($reprotectionQuotes) : ?>
        <tr>
            <td></td>
            <td colspan="7">
                <p><b>Reprotection Quotes:</b></p>
                <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 30px;">Nr</th>
                  <th>Status</th>
                  <th style="width: 20px">Recommended</th>
                  <th style="width: 180px">Created</th>
                  <th style="width: 10px;"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reprotectionQuotes as $nr => $reprotectionQuote) : ?>
                    <tr>
                        <?php
                        $isRecommended = $reprotectionQuote->isRecommended();
                        /*
                        <td style="padding:5px;" title="Product Quote ID: <?=Html::encode($quote->pq_id)?>, GID: <?=Html::encode($quote->pq_gid)?>">
                            <?= $quote->pqProduct->prType->pt_icon_class ? Html::tag('i', '', ['class' => $quote->pqProduct->prType->pt_icon_class]) : '' ?>
                            <?=Html::encode($quote->pqProduct->prType->pt_name)?>
                            <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
                        </td>
                        <td style="padding:5px;"><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
                        */ ?>
                      <td data-toggle="tooltip" data-original-title="Product QuoteID: <?=Html::encode($reprotectionQuote->pq_id)?>, GID: <?=Html::encode($reprotectionQuote->pq_gid)?>" title="Product QuoteID: <?=Html::encode($reprotectionQuote->pq_id)?>, GID: <?=Html::encode($reprotectionQuote->pq_gid)?>"><?=($nr + 1)?></td>
                      <td><?= ProductQuoteStatus::asFormat($reprotectionQuote->pq_status_id)?></td>
                      <td><?= $isRecommended ? Html::tag('i', null, ['class' => 'fas fa-star yellow']) : '-' ?></td>
                      <td><small><?=$reprotectionQuote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($reprotectionQuote->pq_created_dt)) : '-'?></small></td>
                      <td>
                        <div class="btn-group">

                          <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                          </button>
                          <div class="dropdown-menu">
                              <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_VIEW_DETAILS, CasesAbacObject::ACTION_ACCESS, Reprotection Quote View Details */ ?>
                              <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_VIEW_DETAILS, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fas fa-info-circle" data-toggle="tooltip" title="Details"></i> View Details', null, [
                                      'data-product-quote-gid' => $reprotectionQuote->pq_gid,
                                      'class' => 'dropdown-item btn-show-product-quote-details',
                                      'data-url' => Url::to([$reprotectionQuote->getQuoteDetailsPageUrl(), 'id' => $reprotectionQuote->pq_id])
                                  ]) ?>
                              <?php endif; ?>
                              <?php
                              /** @abac new $caseAbacDto, CasesAbacObject::ACT_REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS, Reprotection Quote send email */
                                if (!$reprotectionQuote->isDeclined() && Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                                    echo Html::a('<i class="fa fa-envelope" title="Send Email"></i> Send Schedule Change Email', null, [
                                      'class' => 'dropdown-item btn-send-reprotection-quote-email',
                                      'data-url' => Url::to(['/product/product-quote/preview-reprotection-quote-email', 'reprotection-quote-id' => $reprotectionQuote->pq_id, 'case-id' => $caseId, 'order-id' => $order->or_id])
                                    ]);
                                }
                                ?>
                              <?php
                              /** @abac new $caseAbacDto, CasesAbacObject::ACT_VIEW_QUOTES_DIFF, CasesAbacObject::ACTION_ACCESS, Reprotection Quote send email */
                                if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_VIEW_QUOTES_DIFF, CasesAbacObject::ACTION_ACCESS)) {
                                    echo Html::a('<i class="fas fa-columns" title="Origin And Reprotection Quotes Diff"></i> Origin And Reprotection Quotes Diff', null, [
                                      'class' => 'dropdown-item btn-origin-reprotection-quote-diff',
                                      'data-url' => Url::to([$quote->getDiffUrlOriginReprotectionQuotes(), 'reprotection-quote-id' => $reprotectionQuote->pq_id, 'origin-quote-id' => $quote->pq_id])
                                    ]);
                                }
                                ?>
                              <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_CONFIRM, CasesAbacObject::ACTION_ACCESS, Flight Reprotection confirm*/ ?>
                              <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_CONFIRM, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fa fa-check text-success" title="Confirm"></i> Confirm', null, [
                                      'class' => 'dropdown-item btn-reprotection-confirm',
                                      'data-url' => Url::to(['/product/product-quote/flight-reprotection-confirm']),
                                      'data-reprotection-quote-id' => $reprotectionQuote->pq_id
                                  ]); ?>
                              <?php endif; ?>
                              <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_REFUND, CasesAbacObject::ACTION_ACCESS, Flight Reprotection refund*/ ?>
                              <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_REFUND, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fa fa-check text-success" title="Refund"></i> Refund', null, [
                                      'class' => 'dropdown-item btn-reprotection-refund',
                                      'data-url' => Url::to(['/product/product-quote/flight-reprotection-refund']),
                                      'data-reprotection-quote-id' => $reprotectionQuote->pq_id,
                                      'data-title' => 'Reprotection Refund'
                                  ]); ?>
                              <?php endif; ?>

                              <?php if (!$isRecommended && Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fas fa-star yellow yellow" title="Set Recommended"></i> Set Recommended', null, [
                                    'class' => 'dropdown-item btn-reprotection-recommended',
                                    'data-url' => Url::to(['/product/product-quote/set-recommended']),
                                    'data-reprotection-quote-id' => $reprotectionQuote->pq_id,
                                    'data-title' => 'Reprotection Set Recommended'
                                ]); ?>
                              <?php endif; ?>

                          </div>
                        </div>
                      </td>
                    </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            </td>
            <td></td>
        </tr>
    <?php endif; ?>

    <?php if ($quote->productQuoteChanges) : ?>
        <tr>
            <td></td>
            <td colspan="7">
                <p><b>Changes:</b></p>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="width: 30px;">Nr</th>
                        <th>Status</th>
                        <th style="width: 180px">Created</th>
                        <th>Decision Type</th>
                        <th>Is Automate</th>
                        <th style="width: 180px">Decision DateTime</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($quote->productQuoteChanges as $nr => $changeItem) : ?>
                        <tr>
                            <td data-toggle="tooltip" data-original-title="ProductQuoteChange ID: <?=Html::encode($changeItem->pqc_id)?>" title="ProductQuoteChangeID: <?=Html::encode($changeItem->pqc_id)?>">
                                <?=($nr + 1)?>
                            </td>
                            <td><?= $changeItem->pqc_status_id ? ProductQuoteChangeStatus::asFormat($changeItem->pqc_status_id) : '-'?></td>
                            <td><small><?=$changeItem->pqc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($changeItem->pqc_created_dt)) : '-'?></small></td>
                            <td><?= $changeItem->pqc_decision_type_id ? ProductQuoteChangeDecisionType::asFormat($changeItem->pqc_decision_type_id) : '-'?></td>
                            <td><?= Yii::$app->formatter->asBoolean($changeItem->pqc_is_automate) ?></td>
                            <td><small><?=$changeItem->pqc_decision_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($changeItem->pqc_decision_dt)) : '-'?></small></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
            <td></td>
        </tr>
    <?php endif; ?>


    <?php if ($quote->productQuoteRefunds) : ?>
        <tr>
            <td></td>
            <td colspan="7">
                <p><b>Refund:</b></p>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="width: 30px;">Nr</th>
                        <th>Status</th>
                        <th>Selling price</th>
                        <th>Refund amount</th>
                        <th>Client currency</th>
                        <th style="width: 180px">Created</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($quote->productQuoteRefunds as $nr => $refundItem) : ?>
                        <tr>
                            <td data-toggle="tooltip" data-original-title="ProductQuoteRefund ID: <?=Html::encode($refundItem->pqr_id)?>" title="ProductQuoteChangeID: <?=Html::encode($refundItem->pqr_id)?>">
                                <?=($nr + 1)?>
                            </td>
                            <td><?= $refundItem->pqr_status_id ? ProductQuoteRefundStatus::asFormat($refundItem->pqr_status_id) : '-'?></td>
                            <td><?= $refundItem->pqr_client_selling_price ? number_format($refundItem->pqr_client_selling_price, 2) : '-'?></td>
                            <td><?= $refundItem->pqr_client_refund_amount ? number_format($refundItem->pqr_client_refund_amount, 2) : '-'?></td>
                            <td><?= $refundItem->pqr_client_currency ? Html::encode($refundItem->pqr_client_currency) : '-'?></td>
                            <td><small><?=$refundItem->pqr_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($refundItem->pqr_created_dt)) : '-'?></small></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
            <td></td>
        </tr>
    <?php endif; ?>