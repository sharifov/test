<?php

use common\models\Airports;
use kartik\editable\Editable;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use sales\helpers\product\ProductQuoteHelper;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var RentCarQuote $rentCarQuote
 * @var RentCar $rentCar
 */

$productQuote = $rentCarQuote->rcqProductQuote;
?>

<table class="table table-bordered table-striped" id="quote-prices-<?php echo $rentCarQuote->getId()?>">
    <thead>
        <tr>
            <th>PickUp</th>
            <th>DropOff</th>
            <th>NP, $</th>
            <th>Mkp, $</th>
            <th>Ex Mkp, $</th>
            <th>SFP, %</th>
            <th>SFP, $</th>
            <th>SP, $</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php echo Airports::getCityByIata($rentCar->prc_pick_up_code) ?> (<?php echo $rentCar->prc_pick_up_code ?>)<br />
                Date: <?php echo $rentCar->prc_pick_up_date ?>
            </td>
            <td>
                <?php $dropOffCode = $rentCar->prc_drop_off_code ?: $rentCar->prc_pick_up_code ?>
                <?php echo Airports::getCityByIata($dropOffCode) ?> (<?php echo $dropOffCode ?>)<br />
                Date: <?php echo $rentCar->prc_drop_off_date ?>
            </td>
            <td>
                <?php echo number_format($productQuote->pq_origin_price, 2) ?>
            </td>
            <td>
                <?php echo $rentCarQuote->rcq_system_mark_up ?>
            </td>
            <td>
                <?php if ($productQuote->isNew()) :?>
                    <?= Editable::widget([
                        'name' => 'extra_markup[' . $rentCarQuote->getId() . ']',
                        'asPopover' => false,
                        'pjaxContainerId' => 'pjax-quote_prices-' . $productQuote->pq_id,
                        'value' => number_format($rentCarQuote->getAgentMarkUp(), 2),
                        'header' => 'Extra markup',
                        'size' => 'sm',
                        'inputType' => Editable::INPUT_TEXT,
                        'buttonsTemplate' => '{submit}',
                        'pluginEvents' => [
                            'editableSuccess' => "function(event, val, form, data) { 
                                $.pjax.reload({container: '#pjax-quote_prices-{$rentCarQuote->getId()}', async: false}); 
                                $('#quote_profit_{$rentCarQuote->getId()}').popover('hide').popover('dispose');
                                $('#quote_profit_{$rentCarQuote->getId()}').popover();
                                $.pjax.reload({container: '#pjax-product-quote-{$productQuote->pq_id}', async: false});
                                pjaxReload({container: '#pjax-lead-orders'});
                                pjaxReload({container: '#pjax-lead-offers'});
                            }",
                        ],
                        'inlineSettings' => [
                            'templateBefore' => '<div class="editable-pannel">{loading}',
                            'templateAfter' => '{buttons}{close}</div>'
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'style' => 'width:50px;',
                            'placeholder' => 'Enter extra markup',
                            'resetButton' => '<i class="fa fa-ban"></i>'
                        ],
                        'formOptions' => [
                            'action' => Url::toRoute(['/rent-car/rent-car-quote/ajax-update-agent-markup'])
                        ]
                    ]) ?>
                <?php else :?>
                    <?php echo number_format($rentCarQuote->getAgentMarkUp(), 2) ?>
                <?php endif ?>
            </td>
            <td>
                <?php echo $rentCarQuote->rcq_service_fee_percent ?>
            </td>
            <td>
                <?php echo $productQuote->pq_service_fee_sum ?>
            </td>
            <td>
                <?php echo $productQuote->pq_price ?>
            </td>
        </tr>
    </tbody>
</table>

