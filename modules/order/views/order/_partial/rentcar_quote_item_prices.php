<?php

use common\models\Airports;
use kartik\editable\Editable;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use sales\helpers\product\ProductQuoteHelper;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ProductQuote $productQuote
 */

$rentCarQuote = $productQuote->rentCarQuote;
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
        <td width="145">
            <?php echo $rentCarQuote->rcq_pick_up_location ?><br />
            <?php if ($rentCarQuote->rcq_pick_up_dt) : ?>
                <?php $pickUpdDate = DateTime::createFromFormat('Y-m-d H:i:s', $rentCarQuote->rcq_pick_up_dt); ?>
                <?php echo Html::tag('i', '', ['class' => 'fa fa-calendar']) ?>
                <?php echo $pickUpdDate->format('d-M-Y') ?> <?php echo $pickUpdDate->format('H:i') ?>
            <?php endif ?>
        </td>
        <td width="145">
            <?php echo $rentCarQuote->rcq_drop_of_location ?><br />
            <?php if ($rentCarQuote->rcq_drop_off_dt) : ?>
                <?php $dropOffDate = DateTime::createFromFormat('Y-m-d H:i:s', $rentCarQuote->rcq_drop_off_dt); ?>
                <?php echo Html::tag('i', '', ['class' => 'fa fa-calendar']) ?>
                <?php echo $dropOffDate->format('d-M-Y') ?> <?php echo $dropOffDate->format('H:i') ?>
            <?php endif ?>
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
                                $.pjax.reload({container: '#pjax-product-quote-{$productQuote->pq_id}', async: false});
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

