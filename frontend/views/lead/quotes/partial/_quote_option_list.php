<?php
/**
 * @var $this View
 * @var $productQuote ProductQuote
 */

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$totalAmountOption = 0;
$totalClientAmountOption = 0;
$totalExtraMarkupOption = 0;
?>


<?php if ($productQuote->productQuoteOptions): ?>
    <h2>Options</h2>
    <table class="table table-striped table-bordered">
        <tr>
            <th>ID</th>
            <th>Option</th>
            <th>Name / Description</th>
            <th>Status</th>
            <th style="width: 120px">Extra markup</th>
            <th style="width: 120px">Price</th>
            <th style="width: 120px">Client Price</th>
            <th style="width: 52px"></th>
        </tr>
        <?php foreach ($productQuote->productQuoteOptions as $quoteOption):
            $totalAmountOption += (float) $quoteOption->pqo_price;
            $totalClientAmountOption += (float) $quoteOption->pqo_client_price;
            $totalExtraMarkupOption += (float) $quoteOption->pqo_extra_markup;
            ?>
            <tr>
                <td style="width: 60px" title="<?= Html::encode($quoteOption->pqo_id)?>"><?=Html::encode($quoteOption->pqo_id)?></td>
                <td style="width: 120px"><?=$quoteOption->pqoProductOption ? Html::encode($quoteOption->pqoProductOption->po_name) : '' ?></td>
                <td>
                    <b><?=Html::encode($quoteOption->pqo_name)?></b>
                    <?=$quoteOption->pqo_description ? '<br>'. Html::encode($quoteOption->pqo_description) . '' : ''?>
                </td>
                <td class="text-center" style="width: 120px"><?= ProductQuoteOptionStatus::asFormat($quoteOption->pqo_status_id)?></td>
                <td class="text-right" title="Extra Markup"><?=number_format($quoteOption->pqo_extra_markup, 2)?> USD</td>
                <td class="text-right"><?=number_format($quoteOption->pqo_price, 2)?> USD</td>
                <td class="text-right"><?=number_format($quoteOption->pqo_client_price, 2)?> <?=Html::encode($productQuote->pq_client_currency)?></td>
                <td>

                    <div class="btn-group">
                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu">
                            <?php
                            echo Html::a('<i class="fa fa-edit text-warning" title="Update"></i> Update', null, [
                                'class' => 'dropdown-item btn-update-product-quote-option',
                                'data-url' => Url::to(['/product/product-quote-option/update-ajax', 'id' => $quoteOption->pqo_id])
                            ]);
                            ?>
                            <div class="dropdown-divider"></div>
                            <?php
                            echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i> Delete', null, [
                                'data-pqo-id' => $quoteOption->pqo_id,
                                'data-product-id' => $productQuote->pq_product_id,
                                'class' => 'dropdown-item btn-delete-product-quote-option',
                                'data-url' => Url::to(['/product/product-quote-option/delete-ajax'])
                            ]);
                            ?>
                        </div>
                    </div>


                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="4" class="text-right">Option Total: </th>
            <?php
                $totalAmountOption = round($totalAmountOption, 2);
                $totalClientAmountOption = round($totalClientAmountOption, 2);
                $totalExtraMarkupOption = round($totalExtraMarkupOption, 2);
            ?>
            <th class="text-right" title="Extra Markup">
                <?=number_format($totalExtraMarkupOption, 2)?> USD
            </th>
            <th class="text-right">
                <?=number_format($totalAmountOption, 2)?> USD
            </th>
            <th class="text-right">
                <b><?=number_format($totalClientAmountOption, 2)?> <?=Html::encode($productQuote->pq_client_currency)?></b>
            </th>
            <th></th>
        </tr>
    </table>
<?php endif; ?>
