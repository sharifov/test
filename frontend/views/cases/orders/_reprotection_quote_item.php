<?php

/**
 * @var $quote \modules\product\src\entities\productQuote\ProductQuote
 * @var $caseAbacDto \modules\cases\src\abac\dto\CasesAbacDto
 * @var $order \modules\order\src\entities\order\Order
 * @var int $caseId
 * @var int $nr
 * */

use modules\cases\src\abac\CasesAbacObject;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php
/*

<td style="padding:5px;" title="Product Quote ID: <?=Html::encode($quote->pq_id)?>, GID: <?=Html::encode($quote->pq_gid)?>">
    <?= $quote->pqProduct->prType->pt_icon_class ? Html::tag('i', '', ['class' => $quote->pqProduct->prType->pt_icon_class]) : '' ?>
    <?=Html::encode($quote->pqProduct->prType->pt_name)?>
    <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
</td>
<td style="padding:5px;"><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
*/ ?>
<td style="padding:5px;" data-toggle="tooltip" data-original-title="Product Quote ID: <?=Html::encode($quote->pq_id)?>, GID: <?=Html::encode($quote->pq_gid)?>" title="Product Quote ID: <?=Html::encode($quote->pq_id)?>, GID: <?=Html::encode($quote->pq_gid)?>"><?=($nr + 1)?></td>
<td style="padding:5px;"><?= ProductQuoteStatus::asFormat($quote->pq_status_id)?></td>
<td style="padding:5px;"><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
<td style="padding:5px;">
    <div class="btn-group">

        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-bars"></i>
        </button>
        <div class="dropdown-menu">
            <?php
            if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::REPROTECTION_QUOTE_SEND_EMAIL, CasesAbacObject::ACTION_ACCESS)) {
                echo Html::a('<i class="fa fa-envelope text-success" title="Send Email"></i> Send Flight Schedule Change Email', null, [
                    'class' => 'dropdown-item btn-send-reprotection-quote-email',
                    'data-url' => Url::to(['/product/product-quote/preview-reprotection-quote-email', 'reprotection-quote-id' => $quote->pq_id, 'case-id' => $caseId, 'order-id' => $order->or_id])
                ]);
            }
            ?>
            <?php if (Yii::$app->abac->can($caseAbacDto, CasesAbacObject::ACT_PRODUCT_QUOTE_VIEW_DETAILS, CasesAbacObject::ACTION_ACCESS)) : ?>
                <?= Html::a('<i class="fas fa-info-circle" data-toggle="tooltip" title="Details"></i> Details', null, [
                    'data-product-quote-gid' => $quote->pq_gid,
                    'class' => 'dropdown-item btn-show-product-quote-details',
                    'data-url' => Url::to([$quote->getQuoteDetailsPageUrl(), 'id' => $quote->pq_id])
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</td>
