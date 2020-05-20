<?php

/**
 * @var $prices []
 * @var QuotePrice $price
 */

use common\models\QuotePrice;
use yii\bootstrap\Html;

$applyBtn = [];
foreach ($prices as $index => $price) : ?>
    <tr class="pax-type-<?= $price->passenger_type ?> zzz" id="price-index-<?= $index ?>">
        <td class="td-input">
            <?= $price->passenger_type ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']id') ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']passenger_type') ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']service_fee') ?>
            <?= Html::activeHiddenInput($price, '[' . $index . ']extra_mark_up') ?>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']selling', [
                    'class' => 'input-group form-control alt-quote-price',
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']net', [
                    'class' => 'input-group form-control alt-quote-price',
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']fare', [
                    'class' => 'input-group form-control alt-quote-price',
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']taxes', [
                    'class' => 'input-group form-control alt-quote-price',
                    'readonly' => true,
                ]) ?>
            </div>
        </td>
        <td class="td-input">
            <div class="input-group field-quoteprice-0-selling">
                <span class="input-group-addon">$</span>
                <?= Html::activeTextInput($price, '[' . $index . ']mark_up', [
                    'class' => 'input-group form-control alt-quote-price',
                ]) ?>
            </div>
        </td>
        <td class="td-input text-right">
            <?php  if (!in_array($price->passenger_type, $applyBtn) && $paxCntTypes[$price->passenger_type] > 1) {
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
            }  ?>
        </td>
    </tr>
<?php endforeach; ?>