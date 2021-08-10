<?php

use modules\cases\src\abac\CasesAbacObject;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\helpers\Html;
use yii\helpers\Url;

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
if ($nr && $reprotectionQuotes = $quote->relates) {
    $hasReprotection = true;
}

?>

    <td title="Product Quote ID: <?= Html::encode($quote->pq_id)?>"><?= $nr ?></td>
    <td title="<?=Html::encode($quote->pq_product_id)?>">
        <?= $quote->pqProduct->prType->pt_icon_class ? Html::tag('i', '', ['class' => $quote->pqProduct->prType->pt_icon_class]) : '' ?>
        <?=Html::encode($quote->pqProduct->prType->pt_name)?>
        <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
      <br>
      <?= $hasReprotection ? Html::a('Has reprotection quotes', '#', [
          'class' => 'has_reprotection_quotes',
      ]) : '' ?>
    </td>

    <!--                    <td>--><?php //=\yii\helpers\VarDumper::dumpAsString($quote->attributes, 10, true)?><!--</td>-->

    <td><?=Html::encode($quote->pq_name)?></td>
    <td><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
    <td><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
    <td class="text-right"><?=number_format($quote->optionAmountSum, 2)?></td>
    <td class="text-right"><?=number_format($quote->pq_service_fee_sum, 2)?></td>
    <td class="text-right"><?=number_format($quote->pq_price, 2)?></td>
    <td class="text-right"><?=number_format($quote->pq_client_price, 2)?> <?=Html::encode($quote->pq_client_currency)?></td>
    <td>
      <div class="btn-group">

        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-bars"></i>
        </button>
        <div class="dropdown-menu">
        <?php if ($isReprotection) : ?>
            <?php
            if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                echo Html::a('<i class="fa fa-envelope text-success" title="Send Email"></i> Send Flight Schedule Change Email', null, [
                  'class' => 'dropdown-item btn-send-reprotection-quote-email',
                  'data-url' => Url::to(['/product/product-quote/preview-reprotection-quote-email', 'reprotection-quote-id' => $quote->pq_id, 'case-id' => $caseId])
                ]);
            }
            ?>
        <?php else : ?>
            <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i> Remove', null, [
                'data-order-id' => $order->or_id,
                'data-product-quote-id' => $quote->pq_id,
                'class' => 'dropdown-item btn-delete-quote-from-order',
                'data-url' => \yii\helpers\Url::to(['/order/order-product/delete-ajax'])
            ])
            ?>
            <?= Html::a('<i class="fas fa-info-circle" data-toggle="tooltip" title="Details"></i> Details', null, [
                'data-product-quote-gid' => $quote->pq_gid,
                'class' => 'dropdown-item btn-show-product-quote-details',
                'data-url' => Url::to([$quote->getQuoteDetailsPageUrl(), 'id' => $quote->pq_id])
            ]); ?>
        <?php endif; ?>
        </div>
      </div>
    </td>

    <?php if ($nr && $reprotectionQuotes) : ?>
      <tr class="<?= $hasReprotection ? 'hidden' : '' ?>">
        <?php foreach ($reprotectionQuotes as $reprotectionQuote) : ?>
            <?= $this->render('_product_quote_item', [
              'quote' => $reprotectionQuote,
              'nr' => null,
              'order' => $order,
              'isReprotection' => true,
              'caseId' => $caseId,
                'caseAbacDto' => $caseAbacDto
            ]) ?>
        <?php endforeach; ?>
      </tr>
    <?php endif; ?>