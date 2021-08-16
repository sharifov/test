<?php

use modules\cases\src\abac\CasesAbacObject;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
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

if ($nr && $reprotectionQuotes = $quote->relates) {
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

    <td><?=Html::encode($quote->pq_name)?></td>
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
                    <?= Html::a('<i class="fas fa-plus-circle" data-toggle="tooltip" title="Details"></i> Create from dump', null, [
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
          <p>Reprotection Quotes:</p>
          <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 30px;">Nr</th>
                  <th>Status</th>
                  <th style="width: 180px">Created</th>
                  <th style="width: 10px;"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reprotectionQuotes as $nr => $reprotectionQuote) : ?>
                    <tr>
                        <?= $this->render('_reprotection_quote_item', [
                                'nr' => $nr,
                                'quote' => $reprotectionQuote,
                                'order' => $order,
                                'isReprotection' => true,
                                'caseId' => $caseId,
                                'caseAbacDto' => $caseAbacDto
                        ]) ?>
                    </tr>
                <?php endforeach; ?>
              </tbody>
          </table>
        </td>
          <td></td>
      </tr>
    <?php endif; ?>
