<?php

/**
 * @var $prices []
 * @var QuotePrice $price
 * @var Lead $lead
 */

use common\models\Lead;
use common\models\QuotePrice;
use yii\bootstrap\Html;

$paxCntTypes = [
    QuotePrice::PASSENGER_ADULT => $lead->adults,
    QuotePrice::PASSENGER_CHILD => $lead->children,
    QuotePrice::PASSENGER_INFANT => $lead->infants
];
$applyBtn = [];

foreach ($prices as $index => $price) : ?>
    <tr class="pax-type-<?= $price->passenger_type ?> " id="price-index-<?= $index ?>" >
        <td class="td-input">
            <?= $price->passenger_type ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']id') ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']passenger_type') ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']service_fee') ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']extra_mark_up') ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']oldParams') ?>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']selling', [
                    'class' => 'input-group form-control alt-quote-price price_row',
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']net', [
                    'class' => 'input-group form-control alt-quote-price price_row',
                    'readonly' => true,
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']fare', [
                    'class' => 'input-group form-control alt-quote-price price_row',
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']taxes', [
                    'class' => 'input-group form-control alt-quote-price price_row',
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']mark_up', [
                    'class' => 'input-group form-control alt-quote-price price_row',
                ]) ?>
            </div>
        </td>
        <td class="td-input text-right">
            <?php /*  if (!in_array($price->passenger_type, $applyBtn) && $paxCntTypes[$price->passenger_type] > 1) {
                $applyBtn[] = $price->passenger_type;
                echo Html::button('<i class="fa fa-copy"></i>', [
                    'title' => '',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'data-original-title' => 'Clone Price for Pax Type ' . $price->passenger_type,
                    'class' => 'btn btn-primary btn-sm clone-alt-price-by-type',
                    'data-price-index' => $index,
                    'data-type' => $price->passenger_type
                ]);
            } */ ?>
        </td>
    </tr>
<?php endforeach; ?>