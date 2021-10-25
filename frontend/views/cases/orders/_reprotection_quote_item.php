<?php

use modules\cases\src\abac\CasesAbacObject;
use modules\order\src\entities\order\Order;
use modules\product\src\abac\dto\ProductQuoteAbacDto;
use modules\product\src\abac\ProductQuoteAbacObject;
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

if ($nr && $reprotectionQuotes = ProductQuoteQuery::getChangeQuotesByOriginQuote($quote->pq_id)) {
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
                <?= Html::a('<i class="fas fa-info-circle"></i> View Details', null, [
                    'data-product-quote-gid' => $quote->pq_gid,
                    'class' => 'dropdown-item btn-show-product-quote-details',
                    'data-url' => Url::to([$quote->getQuoteDetailsPageUrl(), 'id' => $quote->pq_id]),
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'right',
                    'title' => 'View Details'
                ]) ?>
            <?php endif; ?>

            <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_QUOTE, CasesAbacObject::ACTION_CREATE, Flight Create Reprotection quote from dump*/ ?>
            <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_QUOTE, CasesAbacObject::ACTION_CREATE)) : ?>
                <?php if ($flight = ArrayHelper::getValue($quote, 'flightQuote.fqFlight')) : ?>
                    <?= Html::a('<i class="fas fa-plus-circle"></i> Add Change Quote', null, [
                        'data-flight-id' => $flight->getId(),
                        'class' => 'dropdown-item btn_create_from_dump',
                        'data-url' => Url::to(['/flight/flight-quote/create-re-protection-quote', 'flight_id' => $flight->getId()]),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'right',
                        'title' => 'Add Change Quote'
                    ]) ?>
                <?php endif ?>
            <?php endif ?>

            <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_REMOVE, CasesAbacObject::ACTION_ACCESS, Action Remove product from order */ ?>
            <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_REMOVE, CasesAbacObject::ACTION_ACCESS)) : ?>
                <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i> Remove', null, [
                    'data-order-id' => $order->or_id,
                    'data-product-quote-id' => $quote->pq_id,
                    'class' => 'dropdown-item btn-delete-quote-from-order',
                    'data-url' => \yii\helpers\Url::to(['/order/order-product/delete-ajax']),
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'right',
                    'title' => 'Remove Product Quote'
                ]) ?>
            <?php endif; ?>
        </div>
      </div>
    </td>

    <?php if ($reprotectionQuotes) : ?>
        <tr>
            <td></td>
            <td colspan="7">
                <p><h4>Reprotection Quotes:</h4></p>
                <table class="table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th style="width: 40px;">Nr</th>
                    <th style="width: 50px" title="Recommended">Rec</th>
                  <th>Status</th>
                  <th style="width: 180px">Created</th>
                  <th>Owner</th>
                  <th style="width: 60px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reprotectionQuotes as $nr => $reprotectionQuote) : ?>
                    <?php
                    $isRecommended = $reprotectionQuote->isRecommended();
                    $productQuoteAbacDto = new ProductQuoteAbacDto($reprotectionQuote);
                    /*
                    <td style="padding:5px;" title="Product Quote ID: <?=Html::encode($quote->pq_id)?>, GID: <?=Html::encode($quote->pq_gid)?>">
                        <?= $quote->pqProduct->prType->pt_icon_class ? Html::tag('i', '', ['class' => $quote->pqProduct->prType->pt_icon_class]) : '' ?>
                        <?=Html::encode($quote->pqProduct->prType->pt_name)?>
                        <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
                    </td>
                    <td style="padding:5px;"><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
                    */ ?>
                    <tr>
                      <td data-toggle="tooltip" data-original-title="Product QuoteID: <?=Html::encode($reprotectionQuote->pq_id)?>, GID: <?=Html::encode($reprotectionQuote->pq_gid)?>" title="Product QuoteID: <?=Html::encode($reprotectionQuote->pq_id)?>, GID: <?=Html::encode($reprotectionQuote->pq_gid)?>"><?=($nr + 1)?></td>
                        <td><?= $isRecommended ? Html::tag('i', null, ['class' => 'fas fa-star warning', 'title' => 'Recommended']) : '-' ?></td>
                      <td><?= ProductQuoteStatus::asFormat($reprotectionQuote->pq_status_id)?></td>
                      <td><small><?=$reprotectionQuote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($reprotectionQuote->pq_created_dt)) : '-'?></small></td>
                      <td>
                          <?php if ($reprotectionQuote->pqOwnerUser) : ?>
                            <i class="fa fa-user"></i> <?= $reprotectionQuote->pqOwnerUser->username ?>
                          <?php endif; ?>
                      </td>
                      <td>
                        <div class="btn-group">

                          <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                          </button>
                          <div class="dropdown-menu">
                              <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_VIEW_DETAILS, CasesAbacObject::ACTION_ACCESS, Action Reprotection Quote View Details */ ?>
                              <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_VIEW_DETAILS, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fas fa-info-circle" title=""></i> view Details', null, [
                                      'data-product-quote-gid' => $reprotectionQuote->pq_gid,
                                      'class' => 'dropdown-item btn-show-product-quote-details',
                                      'data-url' => Url::to([$reprotectionQuote->getQuoteDetailsPageUrl(), 'id' => $reprotectionQuote->pq_id]),
                                      'data-toggle' => 'tooltip',
                                      'data-placement' => 'right',
                                      'title' => 'View Details'
                                  ]) ?>
                              <?php endif; ?>
                              <?php
                                $caseAbacDto->pqc_status = $quote->productQuoteLastChange->pqc_status_id ?? null;
                                /** @abac new $caseAbacDto, CasesAbacObject::ACT_REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS, Action Reprotection Quote send email */
                                if (!$reprotectionQuote->isDeclined() && Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                                    echo Html::a('<i class="fa fa-envelope"></i> send SC Email', null, [
                                        'class' => 'dropdown-item btn-send-reprotection-quote-email',
                                        'data-url' => Url::to(['/product/product-quote/preview-reprotection-quote-email', 'reprotection-quote-id' => $reprotectionQuote->pq_id, 'case-id' => $caseId, 'order-id' => $order->or_id]),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'right',
                                        'title' => 'Send Schedule Change Email'
                                    ]);
                                }
                                ?>
                              <?php
                              /** @abac new $caseAbacDto, CasesAbacObject::ACT_VIEW_QUOTES_DIFF, CasesAbacObject::ACTION_ACCESS, Action Reprotection Quote Difference */
                                if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_VIEW_QUOTES_DIFF, CasesAbacObject::ACTION_ACCESS)) {
                                    echo Html::a('<i class="fas fa-columns"></i> view Difference', null, [
                                        'class' => 'dropdown-item btn-origin-reprotection-quote-diff',
                                        'data-url' => Url::to([$quote->getDiffUrlOriginReprotectionQuotes(), 'reprotection-quote-id' => $reprotectionQuote->pq_id, 'origin-quote-id' => $quote->pq_id]),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'right',
                                        'title' => 'View Origin and Reprotection quotes Difference'
                                    ]);
                                }
                                ?>
                              <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_CONFIRM, CasesAbacObject::ACTION_ACCESS, Action Flight Reprotection quote confirm */ ?>
                              <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_CONFIRM, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fa fa-check-circle-o"></i> set Confirmed', null, [
                                      'class' => 'dropdown-item btn-reprotection-confirm',
                                      'data-url' => Url::to(['/product/product-quote/flight-reprotection-confirm']),
                                      'data-reprotection-quote-id' => $reprotectionQuote->pq_id,
                                      'data-toggle' => 'tooltip',
                                      'data-placement' => 'right',
                                      'title' => 'Set Confirm status Reprotection quote'
                                  ]); ?>
                              <?php endif; ?>
                              <?php /** @abac new $caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_REFUND, CasesAbacObject::ACTION_ACCESS, Action Flight Reprotection quote refund*/ ?>
                              <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_FLIGHT_REPROTECTION_REFUND, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fa fa-reply"></i> set Refunded', null, [
                                      'class' => 'dropdown-item btn-reprotection-refund',
                                      'data-url' => Url::to(['/product/product-quote/flight-reprotection-refund']),
                                      'data-reprotection-quote-id' => $reprotectionQuote->pq_id,
                                      'data-title' => 'Reprotection Refund',
                                      'data-toggle' => 'tooltip',
                                      'data-placement' => 'right',
                                      'title' => 'Set Refund status Reprotection quote'
                                  ]); ?>
                              <?php endif; ?>

                              <?php /** @abac $caseAbacDto, CasesAbacObject::ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE, CasesAbacObject::ACTION_ACCESS, Action Flight Reprotection quote recommended */ ?>
                              <?php if (!$isRecommended && Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_VIEW_SET_RECOMMENDED_REPROTECTION_QUOTE, CasesAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fas fa-star"></i> set Recommended', null, [
                                        'class' => 'dropdown-item btn-reprotection-recommended',
                                        'data-url' => Url::to(['/product/product-quote/set-recommended']),
                                        'data-reprotection-quote-id' => $reprotectionQuote->pq_id,
                                        'data-title' => 'Reprotection Set Recommended',
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'right',
                                        'title' => 'Set Recommended for Reprotection quote'
                                ]); ?>
                              <?php endif; ?>

                              <?php /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::ACT_DECLINE_REPROTECTION_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS, Action Flight Reprotection quote decline */ ?>
                              <?php if (Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::ACT_DECLINE_REPROTECTION_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS)) : ?>
                                    <?= Html::a('<i class="fas fa-times text-danger"></i> set Decline', null, [
                                      'class' => 'dropdown-item btn-reprotection-decline',
                                      'data-url' => Url::to(['/product/product-quote/ajax-decline-reprotection-quote']),
                                      'data-reprotection-quote-id' => $reprotectionQuote->pq_id,
                                      'data-title' => 'Decline Reprotection',
                                      'data-toggle' => 'tooltip',
                                      'data-placement' => 'right',
                                      'title' => 'Decline Reprotection quote'
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
                <p><h4>Change List:</h4></p>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th style="width: 30px;">Nr</th>
                        <th style="width: 60px;">Type</th>
                        <th>Status</th>
                        <th style="width: 140px">Created</th>

                        <th style="width: 60px" title="is Automate">Auto</th>
                        <th>Decision Type</th>
                        <th style="width: 150px">Decision DateTime</th>
                        <th style="width: 60px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($quote->productQuoteChanges as $nr => $changeItem) : ?>
                        <tr>
                            <td data-toggle="tooltip" data-original-title="ProductQuoteChange ID: <?=Html::encode($changeItem->pqc_id)?>" title="ProductQuoteChangeID: <?=Html::encode($changeItem->pqc_id)?>">
                                <?=($nr + 1)?>
                            </td>
                            <td>
                                <?= Html::tag('span', $changeItem->getShortTypeName(), ['class' => 'badge badge-light', 'title' => $changeItem->getTypeName()]); ?>
                            </td>
                            <td><?= $changeItem->getStatusLabel()?></td>
                            <td><small><?=$changeItem->pqc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($changeItem->pqc_created_dt)) : '-'?></small></td>

                            <td><?= $changeItem->pqc_is_automate ? '<i class="fa fa-check" title="Automate"></i>' : '-' ?></td>
                            <td><?= $changeItem->getDecisionTypeLabel()?></td>
                            <td><small><?=$changeItem->pqc_decision_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($changeItem->pqc_decision_dt)) : '-'?></small></td>
                            <td>-</td>
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
                <p><b>Refund List:</b></p>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="width: 30px;">Nr</th>
                        <th style="width: 80px;">Type</th>
                        <th>Status</th>
                        <th>Selling price</th>
                        <th>Refund amount</th>
                        <th>Client currency</th>
                        <th style="width: 140px">Created</th>
                        <th style="width: 60px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($quote->productQuoteRefunds as $nr => $refundItem) : ?>
                        <tr>
                            <td data-toggle="tooltip" data-original-title="ProductQuoteRefund ID: <?=Html::encode($refundItem->pqr_id)?>" title="ProductQuoteChangeID: <?=Html::encode($refundItem->pqr_id)?>">
                                <?=($nr + 1)?>
                            </td>
                            <td>
                                <?= Html::tag('span', $refundItem->getShortTypeName(), ['class' => 'badge badge-light', 'title' => $refundItem->getTypeName()]); ?>
                            </td>
                            <td><?= $refundItem->getStatusLabel()?></td>
                            <td><?= $refundItem->pqr_client_selling_price ? number_format($refundItem->pqr_client_selling_price, 2) : '-'?></td>
                            <td><?= $refundItem->pqr_client_refund_amount ? number_format($refundItem->pqr_client_refund_amount, 2) : '-'?></td>
                            <td><?= $refundItem->pqr_client_currency ? Html::encode($refundItem->pqr_client_currency) : '-'?></td>
                            <td><small><?=$refundItem->pqr_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($refundItem->pqr_created_dt)) : '-'?></small></td>
                            <td>
                              <div class="btn-group">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu">
                                  <?php /** @abac ProductQuoteAbacObject::ACT_VIEW_DETAILS_REFUND_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS, Product quote refund view details */ ?>
                                  <?php if (Yii::$app->abac->can(null, ProductQuoteAbacObject::ACT_VIEW_DETAILS_REFUND_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS)) : ?>
                                        <?= Html::a('<i class="fas fa-info-circle"></i> View Details', null, [
                                        'data-refund-quote-id' => $refundItem->pqr_id,
                                        'class' => 'dropdown-item btn-show-refund-quote-details',
                                        'data-url' => Url::to(['/product/product-quote-refund/ajax-view-details', 'id' => $refundItem->pqr_id]),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'right',
                                        'title' => 'View Details'
                                    ]) ?>
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
