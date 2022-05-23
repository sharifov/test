<?php

use common\models\Currency;
use modules\cases\src\abac\CasesAbacObject;
use modules\flight\src\useCases\flightQuote\createManually\helpers\FlightQuotePaxPriceHelper;
use modules\order\src\entities\order\Order;
use modules\product\src\abac\dto\ProductQuoteAbacDto;
use modules\product\src\abac\ProductQuoteAbacObject;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use src\helpers\product\ProductQuoteHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\abac\ProductQuoteChangeAbacObject;
use modules\product\src\abac\dto\ProductQuoteChangeAbacDto;
use modules\product\src\abac\ProductQuoteRefundAbacObject;
use modules\product\src\abac\dto\ProductQuoteRefundAbacDto;
use modules\product\src\abac\dto\RelatedProductQuoteAbacDto;
use modules\product\src\abac\RelatedProductQuoteAbacObject;
use yii\web\ForbiddenHttpException;
use src\access\EmployeeGroupAccess;
use src\auth\Auth;
use src\helpers\setting\SettingHelper;
use src\helpers\product\ProductQuoteRefundHelper;

/**
 * @var Order $order
 * @var ProductQuote $quote
 * @var int $nr
 * @var int $projectId
 * @var  $case \src\entities\cases\Cases
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

$productQuoteAbacDto = new ProductQuoteAbacDto($quote);
$productQuoteAbacDto->mapCaseAttributes($case);
$productQuoteAbacDto->mapOrderAttributes($order);
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
    <td><?= ($quote->getProductQuoteOptionsCount() ?: '-') ?></td>
    <td><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
    <td class="text-right"><?=number_format($quote->pq_client_price, 2)?> <?=Html::encode($quote->pq_client_currency)?></td>
    <td>
      <div class="btn-group">

        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i>
        </button>
        <div class="dropdown-menu">
            <?php /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS_DETAILS, Product quote view details */ ?>
            <?php if (Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS_DETAILS)) : ?>
                <?= Html::a('<i class="fas fa-info-circle"></i> View Details', null, [
                    'data-product-quote-gid' => $quote->pq_gid,
                    'class' => 'dropdown-item btn-show-product-quote-details',
                    'data-url' => Url::to([$quote->getQuoteDetailsPageUrl(), 'id' => $quote->pq_id, 'case_id' => $case->cs_id, 'order_id' => $order->or_id]),
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'right',
                    'title' => 'View Details'
                ]) ?>
            <?php endif; ?>

            <?php /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, CasesAbacObject::ACTION_CREATE_CHANGE, Product quote add change */ ?>
            <?php if (Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_CREATE_CHANGE)) :  ?>
                <?php if ($flight = ArrayHelper::getValue($quote, 'flightQuote.fqFlight')) : ?>
                    <?php echo Html::a('<i class="fas fa-plus-circle"></i> Add Change', null, [
                        'class' => 'dropdown-item btn_create_change',
                        'data-url' => Url::to([
                            '/flight/flight-quote/add-change',
                            'case_id' => $case->cs_id,
                            'origin_quote_id' => $quote->pq_id,
                            'order_id' => $order->or_id
                        ]),
                        'title' => 'Add Change'
                    ]) ?>
                <?php endif ?>
            <?php endif ?>

            <?php /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_CREATE_VOL_REFUND, Product quote add voluntary refund */ ?>
            <?php if (Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_CREATE_VOL_REFUND)) : ?>
                <?php if ($flight = ArrayHelper::getValue($quote, 'flightQuote.fqFlight')) : ?>
                    <?php echo Html::a('<i class="fas fa-plus-circle"></i> Add Refund', null, [
                        'data-flight-id' => $flight->getId(),
                        'class' => 'dropdown-item btn_create_voluntary_refund',
                        'data-url' => Url::to([
                            '/flight/flight-quote/create-voluntary-quote-refund',
                            'flight_quote_id' => $quote->flightQuote->fq_id,
                            'project_id' => $projectId,
                            'origin_product_quote_id' => $quote->pq_id,
                            'order_id' => $order->or_id,
                            'case_id' => $case->cs_id
                        ]),
                        'title' => 'Add Voluntary Refund Quote'
                    ]) ?>
                <?php endif ?>
            <?php endif; ?>

            <?php /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_SHOW_STATUS_LOG, Action Show status logs */ ?>
            <?php if (Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_SHOW_STATUS_LOG)) : ?>
                <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                    'class' => 'dropdown-item btn-product-quote-status-log',
                    'data-url' => \yii\helpers\Url::to(['/product/product-quote-status-log/show', 'gid' => $quote->pq_gid]),
                    'data-gid' => $quote->pq_gid,
                    'title' => 'View status log'
                ]) ?>
            <?php endif; ?>

            <?php /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_DELETE, Action Remove product from order */ ?>
            <?php if (Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_DELETE)) : ?>
                <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i> Remove', null, [
                    'data-order-id' => $order->or_id,
                    'data-product-quote-id' => $quote->pq_id,
                    'data-case-id' => $case->cs_id,
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

    <?php if ($quote->productQuoteChanges) : ?>
        <tr>
            <td></td>
            <td colspan="6">
                <p><b>Change List:</b></p>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th style="width: 55px;">Nr</th>
                        <th style="width: 60px;">Type</th>
                        <th>Status</th>
                        <th title="Client Status mapping from SiteSettings for OTA" data-toggle="tooltip">Client Status</th>
                        <th style="width: 140px">Created</th>
                        <th style="width: 84px">Info</th>
                        <th>Decision</th>
                        <th style="width: 60px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($quote->productQuoteChanges as $nr => $changeItem) : ?>
                        <?php
                        $quoteChangeRelations = $changeItem->productQuoteChangeRelations;
                        $pqcAbacDto = new ProductQuoteChangeAbacDto($changeItem);

                        if ($quoteChangeRelations) {
                            $quotesCnt = 0;
                            foreach ($quoteChangeRelations as $quoteRelation) {
                                if (in_array($quoteRelation->pqcrPq->pq_status_id, SettingHelper::getExchangeQuoteConfirmStatusList())) {
                                    $quotesCnt++;
                                }
                            }
                            $pqcAbacDto->maxConfirmableQuotesCnt = $quotesCnt;
                        }
                        ?>
                        <tr>
                            <td data-toggle="tooltip" data-html="true" title="Change ID: <?=Html::encode($changeItem->pqc_id)?> <br> Change GID: <?=Html::encode($changeItem->pqc_gid)?>">
                                <small>Ch. <?=($nr + 1)?></small>
                            </td>
                            <td>
                                <?= Html::tag('span', $changeItem->getShortTypeName(), ['class' => 'badge badge-light', 'title' => $changeItem->getTypeName()]); ?>
                            </td>
                            <td><?= $changeItem->getStatusLabel()?></td>
                            <td><?= Html::encode($changeItem->getClientStatusName()) ?></td>
                            <td><small><?=$changeItem->pqc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($changeItem->pqc_created_dt)) : '-'?></small></td>
                            <td>
                                <?php if ($changeItem->pqc_is_automate) : ?>
                                    <?php echo Html::tag('span', 'A', ['class' => 'badge badge-pill badge-success', 'title' => 'Automatic']) ?>
                                <?php endif ?>
                                <?php if ($changeItem->isTypeReProtection() && !$changeItem->pqc_refund_allowed) : ?>
                                    <?php echo Html::tag('span', 'R', ['class' => 'badge badge-pill badge-danger', 'title' => 'Not Refundable']) ?>
                                <?php endif ?>
                            </td>
                            <td>
                                <?= $changeItem->getDecisionTypeLabel()?><br />
                                <small><?=$changeItem->pqc_decision_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($changeItem->pqc_decision_dt)) : '-'?></small>
                            </td>
                            <td>

                        <?php if ($changeItem->isTypeVoluntary()) : ?>
                            <?php /** @abac new $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_VOLUNTARY_QUOTE, Flight Create Voluntary quote from dump*/ ?>
                            <?php if (
                                    Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_VOLUNTARY_QUOTE) ||
                                    Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_SEND_OFFER_EXCHANGE_EMAIL)
) : ?>
                                <div class="btn-group">

                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu">

                                        <?php /** @abac new $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_VOLUNTARY_QUOTE, Flight Create Voluntary quote from dump*/ ?>
                                        <?php if (Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_VOLUNTARY_QUOTE)) : ?>
                                            <?php if ($flight = ArrayHelper::getValue($quote, 'flightQuote.fqFlight')) : ?>
                                                <?php echo Html::a('<i class="fas fa-plus-circle"></i> Add Voluntary Change Quote', null, [
                                                    'data-flight-id' => $flight->getId(),
                                                    'class' => 'dropdown-item btn_create_voluntary',
                                                    'data-url' => Url::to([
                                                        '/flight/flight-quote/create-voluntary-quote',
                                                        'flight_id' => $flight->getId(),
                                                        'case_id' => $case->cs_id,
                                                        'origin_quote_id' => $quote->pq_id,
                                                        'change_id' => $changeItem->pqc_id,
                                                    ]),
                                                    'title' => 'Add Voluntary Change Quote'
                                                ]) ?>
                                            <?php endif ?>
                                        <?php endif ?>

                                        <?php /** @abac new $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_SEND_OFFER_EXCHANGE_EMAIL, Btn Send offer exchange email*/ ?>
                                        <?php if (Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_SEND_OFFER_EXCHANGE_EMAIL)) : ?>
                                            <?php if ($changeItem->productQuoteChangeRelations && $flight = ArrayHelper::getValue($quote, 'flightQuote.fqFlight')) : ?>
                                                <?php echo Html::a('<i class="fas fa-envelope"></i> Send offer exchange email', null, [
                                                    'data-flight-id' => $flight->getId(),
                                                    'class' => 'dropdown-item btn_voluntary_offer_email',
                                                    'data-url' => Url::to([
                                                        '/product/product-quote/preview-voluntary-offer-email',
                                                        'flight_id' => $flight->getId(),
                                                        'case_id' => $case->cs_id,
                                                        'origin_quote_id' => $quote->pq_id,
                                                        'change_id' => $changeItem->pqc_id,
                                                        'order_id' => $order->or_id
                                                    ]),
                                                    'title' => 'Send offer exchange email',
                                                ]) ?>
                                            <?php endif ?>
                                        <?php endif ?>

                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endif ?>

                        <?php if ($changeItem->isTypeReProtection()) : ?>
                            <?php if (Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_RE_PROTECTION_QUOTE)) : ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <?php /** @abac $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_RE_PROTECTION_QUOTE, Flight Create Reprotection quote from dump*/ ?>
                                    <?php if (Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_RE_PROTECTION_QUOTE)) : ?>
                                        <?php if ($flight = ArrayHelper::getValue($quote, 'flightQuote.fqFlight')) : ?>
                                            <?= Html::a('<i class="fas fa-plus-circle"></i> Add ReProtection Quote', null, [
                                                'data-flight-id' => $flight->getId(),
                                                'class' => 'dropdown-item btn_create_from_dump',
                                                'data-url' => Url::to([
                                                    '/flight/flight-quote/create-re-protection-quote',
                                                    'flight_id' => $flight->getId(),
                                                    'change_id' => $changeItem->pqc_id,
                                                ]),
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'right',
                                                'title' => 'Add ReProtection Quote'
                                            ]) ?>
                                        <?php endif ?>
                                    <?php endif ?>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endif ?>

                            </td>
                        </tr>

                        <?php if ($quoteChangeRelations) : ?>
                            <tr>
                                <td></td>
                                <td colspan="7">
                                    <p><b>Change Product Quote List:</b></p>
                                    <table class="table table-bordered table-striped table-hover">
                                      <thead>
                                        <tr>
                                              <th style="width: 60px;">Nr</th>
                                              <th style="width: 50px" title="Product Quote Info">Data</th>
                                              <th>Status</th>
                                              <th style="width: 45px;" title="Product Quote Options">Opt</th>
                                              <th style="width: 130px">Created</th>
                                              <th>Exp.</th>
                                              <th>Extra Markup, <?php echo Currency::getDefaultCurrencyCodeByDb() ?></th>
                                              <th style="white-space: nowrap;">Price, <?php echo Currency::getDefaultCurrencyCodeByDb() ?></th>
                                              <th style="width: 60px;">Action</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php foreach ($quoteChangeRelations as $key => $quoteRelation) : ?>
                                            <?php
                                            $changeQuote = $quoteRelation->pqcrPq;
                                            $isRecommended = $changeQuote->isRecommended();
                                            $isConfirmed = $changeQuote->isConfirmed();

                                            $relatedPrQtAbacDto = new RelatedProductQuoteAbacDto($changeQuote);
                                            $relatedPrQtAbacDto->mapOrderAttributes($order);
                                            $relatedPrQtAbacDto->mapProductQuoteChangeAttributes($changeItem);
                                            $relatedPrQtAbacDto->mapCaseAttributes($case);

                                            $productQuoteAbacDto = new ProductQuoteAbacDto($changeQuote);
                                            $productQuoteAbacDto->mapCaseAttributes($case);
                                            $productQuoteAbacDto->mapOrderAttributes($order);
                                            ?>
                                            <tr>
                                                <td data-toggle="tooltip" data-original-title="Product QuoteID: <?=Html::encode($changeQuote->pq_id)?>, GID: <?=Html::encode($changeQuote->pq_gid)?>" title="Product QuoteID: <?=Html::encode($changeQuote->pq_id)?>, GID: <?=Html::encode($changeQuote->pq_gid)?>">
                                                    <small>Pq <?=($nr + 1)?>.<?=($key + 1)?></small>
                                                </td>
                                                <td>
                                                    <?= $isConfirmed ? Html::tag('i', null, ['class' => 'fas fa-check warning', 'title' => 'Confirmed']) : '' ?>
                                                    <?= $isRecommended ? Html::tag('i', null, ['class' => 'fas fa-star warning', 'title' => 'Recommended']) : '' ?>
                                                    <?= !$isRecommended && !$isConfirmed ? '-' : ''?>
                                                </td>
                                              <td><?= ProductQuoteStatus::asFormat($changeQuote->pq_status_id)?></td>
                                              <td><?= ($changeQuote->getProductQuoteOptionsCount() ?: '-') ?></td>
                                              <td><small><?=$changeQuote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($changeQuote->pq_created_dt)) : '-'?></small></td>
                                              <td class="text-center"
                                                  data-toggle="tooltip"
                                                  data-original-title="Date expiration: <?= ($dateExpiration = $changeQuote->pq_expiration_dt ? Yii::$app->formatter->asDatetime($changeQuote->pq_expiration_dt) : 'No set') ?>"
                                                  title="Date expiration: <?= $dateExpiration ?>"
                                              >
                                                  <?= Html::tag(
                                                      'i',
                                                      '',
                                                      [
                                                          'class' => ['fa', 'fa-clock', (ProductQuoteHelper::checkingExpirationDate($changeQuote) ? 'success' : 'danger')],
                                                      ]
                                                  ) ?>
                                              </td>
                                              <td class="text-right">
                                                <span style="white-space: nowrap;">
                                                    <?php echo FlightQuotePaxPriceHelper::priceFormat($changeQuote->pq_agent_markup) ?>
                                                </span>
                                              </td>
                                              <td class="text-right">
                                                <span style="white-space: nowrap;">
                                                    <?php echo FlightQuotePaxPriceHelper::priceFormat($changeQuote->pq_price) ?>
                                                </span>
                                              </td>
                                              <td>
                                                <div class="btn-group">

                                                  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-bars"></i>
                                                  </button>
                                                  <div class="dropdown-menu">
                                                      <?php /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_ACCESS_DETAILS, ReProtection Quote View Details */ ?>
                                                      <?php if (Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_ACCESS_DETAILS)) : ?>
                                                            <?= Html::a('<i class="fas fa-info-circle" title=""></i> view Details', null, [
                                                              'data-product-quote-gid' => $changeQuote->pq_gid,
                                                              'class' => 'dropdown-item btn-show-product-quote-details',
                                                              'data-url' => Url::to([
                                                                  $changeQuote->getQuoteDetailsPageUrl(),
                                                                  'id' => $changeQuote->pq_id,
                                                                  'case_id' => $case->cs_id,
                                                                  'order_id' => $order->or_id,
                                                                  'pqc_id' => $changeItem->pqc_id
                                                              ]),
                                                              'data-toggle' => 'tooltip',
                                                              'data-placement' => 'right',
                                                              'title' => 'View Details'
                                                          ]) ?>
                                                      <?php endif; ?>
                                                      <?php
                                                        /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL, ReProtection Quote send email */
                                                        if ($changeItem->isTypeReProtection() && !$changeQuote->isDeclined() && Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL)) {
                                                            echo Html::a('<i class="fa fa-envelope"></i> send SC Email', null, [
                                                                'class' => 'dropdown-item btn-send-reprotection-quote-email',
                                                                'data-url' => Url::to([
                                                                    '/product/product-quote/preview-reprotection-quote-email',
                                                                    'reprotection-quote-id' => $changeQuote->pq_id,
                                                                    'case-id' => $case->cs_id,
                                                                    'order-id' => $order->or_id,
                                                                    'pqc_id' => $changeItem->pqc_id
                                                                ]),
                                                                'data-toggle' => 'tooltip',
                                                                'data-placement' => 'right',
                                                                'title' => 'Send Schedule Change Email'
                                                            ]);
                                                        }
                                                        ?>
                                                      <?php
                                                      /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL, ReProtection View Difference */
                                                        if (Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_ACCESS_DIFF)) {
                                                            echo Html::a('<i class="fas fa-columns"></i> view Difference', null, [
                                                                'class' => 'dropdown-item btn-origin-reprotection-quote-diff',
                                                                'data-url' => Url::to([
                                                                    $quote->getDiffUrlOriginReprotectionQuotes(),
                                                                    'reprotection-quote-id' => $changeQuote->pq_id,
                                                                    'origin-quote-id' => $quote->pq_id,
                                                                    'case_id' => $case->cs_id,
                                                                    'order_id' => $order->or_id,
                                                                    'pqc_id' => $changeItem->pqc_id
                                                                ]),
                                                                'data-toggle' => 'tooltip',
                                                                'data-placement' => 'right',
                                                                'title' => 'View Origin and Change quotes Difference'
                                                            ]);
                                                        }
                                                        ?>

                                                      <?php if ($changeItem->isTypeReProtection()) : ?>
                                                            <?php /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_CONFIRM, Flight ReProtection quote confirm */ ?>
                                                            <?php if (Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_CONFIRMED)) : ?>
                                                                <?= Html::a('<i class="fa fa-check-circle-o"></i> set Confirmed', null, [
                                                                  'class' => 'dropdown-item btn-reprotection-confirm',
                                                                  'data-url' => Url::to(['/product/product-quote/flight-reprotection-confirm']),
                                                                  'data-reprotection-quote-id' => $changeQuote->pq_id,
                                                                  'data-case_id' => $case->cs_id,
                                                                  'data-order_id' => $order->or_id,
                                                                  'data-pqc_id' => $changeItem->pqc_id,
                                                                  'data-toggle' => 'tooltip',
                                                                  'data-placement' => 'right',
                                                                  'title' => 'Set Confirm status ReProtection quote'
                                                              ]); ?>
                                                            <?php endif; ?>
                                                      <?php endif ?>

                                                      <?php if ($changeItem->isTypeReProtection()) : ?>
                                                            <?php /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTED, RelatedProductQuoteAbacObject::ACTION_SET_REFUNDED, Flight ReProtection quote refund*/ ?>
                                                            <?php if (Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_REFUNDED)) : ?>
                                                                <?= Html::a('<i class="fa fa-reply"></i> set Refunded', null, [
                                                                  'class' => 'dropdown-item btn-reprotection-refund',
                                                                  'data-url' => Url::to(['/product/product-quote/flight-reprotection-refund']),
                                                                  'data-reprotection-quote-id' => $changeQuote->pq_id,
                                                                  'data-case_id' => $case->cs_id,
                                                                  'data-order_id' => $order->or_id,
                                                                  'data-pqc_id' => $changeItem->pqc_id,
                                                                  'data-title' => 'ReProtection Refund',
                                                                  'data-toggle' => 'tooltip',
                                                                  'data-placement' => 'right',
                                                                  'title' => 'Set Refund status ReProtection quote'
                                                              ]); ?>
                                                            <?php endif; ?>
                                                      <?php endif ?>

                                                      <?php /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_REFUNDED, Flight ReProtection quote recommended */ ?>
                                                      <?php if (!$isRecommended && Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_RECOMMENDED)) : ?>
                                                            <?= Html::a('<i class="fas fa-star"></i> set Recommended', null, [
                                                                'class' => 'dropdown-item btn-reprotection-recommended',
                                                                'data-url' => Url::to(['/product/product-quote/set-recommended']),
                                                                'data-reprotection-quote-id' => $changeQuote->pq_id,
                                                                'data-case_id' => $case->cs_id,
                                                                'data-order_id' => $order->or_id,
                                                                'data-pqc_id' => $changeItem->pqc_id,
                                                                'data-title' => 'ReProtection Set Recommended',
                                                                'data-toggle' => 'tooltip',
                                                                'data-placement' => 'right',
                                                                'title' => 'Set Recommended for ReProtection quote'
                                                        ]); ?>
                                                      <?php endif; ?>

                                                      <?php /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_SHOW_STATUS_LOG, Action Show status logs */ ?>
                                                      <?php if (Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_SHOW_STATUS_LOG)) : ?>
                                                            <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                                                              'class' => 'dropdown-item btn-product-quote-status-log',
                                                              'data-url' => \yii\helpers\Url::to(['/product/product-quote-status-log/show', 'gid' => $changeQuote->pq_gid]),
                                                              'data-gid' => $changeQuote->pq_gid,
                                                              'title' => 'View status log'
                                                          ]) ?>
                                                      <?php endif; ?>

                                                      <?php /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_DECLINE, ReProtection quote decline */ ?>
                                                      <?php if (Yii::$app->abac->can($relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SET_DECLINE)) : ?>
                                                            <?= Html::a('<i class="fas fa-times text-danger"></i> set Decline', null, [
                                                              'class' => 'dropdown-item btn-reprotection-decline',
                                                              'data-url' => Url::to(['/product/product-quote/ajax-decline-reprotection-quote']),
                                                              'data-reprotection-quote-id' => $changeQuote->pq_id,
                                                              'data-title' => 'Decline Change Quote',
                                                              'data-toggle' => 'tooltip',
                                                              'data-placement' => 'right',
                                                              'title' => 'Decline Change quote',
                                                              'data-case_id' => $case->cs_id,
                                                              'data-order_id' => $order->or_id,
                                                              'data-pqc_id' => $changeItem->pqc_id,
                                                              'data-title' => 'Decline ReProtection',
                                                              'data-toggle' => 'tooltip',
                                                              'data-placement' => 'right',
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
                            </tr>
                        <?php endif ?>
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
            <td colspan="6">
                <p><b>Refund List:</b></p>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="width: 30px;">Nr</th>
                        <th style="width: 80px;">Type</th>
                        <th title="Count of Objects">Obj</th>
                        <th title="Count of Options">Opt</th>
                        <th>Status</th>
                        <th title="Client Status mapping from SiteSettings for OTA" data-toggle="tooltip">Client Status</th>
                        <th>Selling price</th>
                        <th>Refund amount</th>
<!--                        <th>Client currency</th>-->
                        <th style="width: 140px">Created</th>
                        <th>Exp.</th>
                        <th style="width: 60px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($quote->productQuoteRefunds as $nr => $refundItem) : ?>
                        <?php $pqrAbacDto = new ProductQuoteRefundAbacDto($refundItem) ?>
                        <tr>
                            <td data-toggle="tooltip" data-html="true" title="Refund ID: <?=Html::encode($refundItem->pqr_id)?> <br> Refund GID: <?=Html::encode($refundItem->pqr_gid)?> <br> Refund CID: <?=Html::encode($refundItem->pqr_cid)?>">
                                <?=($nr + 1)?>
                            </td>
                            <td>
                                <?= Html::tag('span', $refundItem->getShortTypeName(), ['class' => 'badge badge-light', 'title' => $refundItem->getTypeName()]); ?>
                            </td>
                            <td><?= $refundItem->getCountOfObjects() ?></td>
                            <td><?= $refundItem->getCountOfOptions() ?></td>
                            <td><?= $refundItem->getStatusLabel()?></td>
                            <td><?= Html::encode($refundItem->getClientStatusName()) ?></td>
                            <td><?= $refundItem->getClientSellingPriceFormat() ?></td>
                            <td><?= $refundItem->getClientRefundAmountPriceFormat() ?></td>
                            <!-- <td><?php // $refundItem->pqr_client_currency ? Html::encode($refundItem->pqr_client_currency) : '-'?></td> -->
                            <td><small><?=$refundItem->pqr_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($refundItem->pqr_created_dt)) : '-'?></small></td>
                            <td class="text-center"
                                data-toggle="tooltip"
                                data-original-title="Date expiration: <?= ($dateExpiration = $refundItem->pqr_expiration_dt ? Yii::$app->formatter->asDatetime($refundItem->pqr_expiration_dt) : 'No set') ?>"
                                title="Date expiration: <?= $dateExpiration ?>"
                            >
                                <?= Html::tag(
                                    'i',
                                    '',
                                    [
                                        'class' => ['fa', 'fa-clock', (ProductQuoteRefundHelper::checkingExpirationDate($refundItem) ? 'success' : 'danger')],
                                    ]
                                ) ?>
                            </td>
                            <td>
                              <div class="btn-group">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu">
                                  <?php /** @abac $pqrAbacDto, ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_ACCESS_DETAILS, Product quote refund view details */ ?>
                                  <?php if (Yii::$app->abac->can($pqrAbacDto, ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_ACCESS_DETAILS)) : ?>
                                        <?= Html::a('<i class="fas fa-info-circle"></i> View Details', null, [
                                        'data-refund-quote-id' => $refundItem->pqr_id,
                                        'class' => 'dropdown-item btn-show-refund-quote-details',
                                        'data-url' => Url::to(['/product/product-quote-refund/ajax-view-details', 'id' => $refundItem->pqr_id]),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'right',
                                        'title' => 'View Details'
                                    ]) ?>
                                  <?php endif; ?>

                                  <?php /** @abac $pqrAbacDto, ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_SEND_VOL_REFUND_EMAIL, Product quote refund send email */ ?>
                                  <?php if (Yii::$app->abac->can($pqrAbacDto, ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_SEND_VOL_REFUND_EMAIL)) : ?>
                                        <?= Html::a('<i class="fa fa-envelope"></i> Send VR Email', null, [
                                          'class' => 'dropdown-item btn-send-voluntary-refund-quote-email',
                                          'data-url' => Url::to(['/product/product-quote-refund/preview-refund-offer-email', 'product-quote-refund-id' => $refundItem->pqr_id, 'case-id' => $case->cs_id, 'order-id' => $order->or_id, 'origin-quote-id' => $quote->pq_id]),
                                          'data-toggle' => 'tooltip',
                                          'data-placement' => 'right',
                                          'title' => 'Send Voluntary Refund Email'
                                      ]); ?>
                                  <?php endif; ?>

                                  <?php
                                  /** @abac new ProductQuoteRefundAbacDto($model), ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_UPDATE, Update Voluntary Quote Refund */
                                    if (Yii::$app->abac->can(new ProductQuoteRefundAbacDto($refundItem), ProductQuoteRefundAbacObject::OBJ_PRODUCT_QUOTE_REFUND, ProductQuoteRefundAbacObject::ACTION_UPDATE)) : ?>
                                        <?= Html::a('<i class="fa fa-pencil"></i> Edit', null, [
                                        'class' => 'dropdown-item btn-edit-voluntary-refund-quote',
                                        'data-url' => Url::to(['/product/product-quote-refund/edit-refund', 'product-quote-refund-id' => $refundItem->pqr_id]),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'right',
                                        'title' => 'Edit Voluntary Refund'
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
