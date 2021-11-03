<?php

use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\form\VoluntaryQuoteCreateForm;
use modules\product\src\entities\productQuote\ProductQuote;
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var View $this
 * @var VoluntaryQuoteCreateForm $createQuoteForm
 * @var ProductQuote $originProductQuote
 * @var ActiveForm $form
 */
?>

<div class="row">
    <?php echo Html::hiddenInput('oldPrices', $createQuoteForm->oldPrices, ['id' => 'oldPrices'])?>
    <div class="col-md-12">
        <table class="table table-striped table-neutral">
            <thead>
            <tr >
                <th>Pax Type</th>
                <th>X</th>
                <th>Fare</th>
                <th>Taxes</th>
                <th>Mark-up</th>
                <th>SFP, %</th>
                <th>Net</th>
                <th>Selling Price, <?php echo $originProductQuote->pq_origin_currency ?><span id="box_loading"></span></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($createQuoteForm->prices as $index => $price) : ?>
                <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][paxCode]')->hiddenInput()->label(false) ?>
                <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][paxCodeId]')->hiddenInput()->label(false) ?>
                <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][cnt]')->hiddenInput()->label(false) ?>
                <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][net]')->hiddenInput()->label(false) ?>

                <tr class="pax-type-<?php echo $price['paxCode'] ?>" id="price-index-<?php echo $index ?>">
                    <td class="td-input">
                        <?php echo $price['paxCode'] ?>
                    </td>
                    <td><?php echo $price['cnt'] ?></td>
                    <td class="td-input">
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][fare]')->input('number', [
                            'class' => 'form-control alt-quote-price',
                            'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
                            'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01
                        ])->label(false) ?>
                    </td>
                    <td class="td-input">
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][taxes]')->input('number', [
                            'class' => 'form-control alt-quote-price',
                            'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
                            'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01
                        ])->label(false) ?>
                    </td>
                    <td class="td-input">
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][markup]')->input('number', [
                            'class' => 'form-control alt-quote-price mark-up',
                            'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
                            'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01
                        ])->label(false) ?>
                    </td>
                    <td>
                        <?php echo $createQuoteForm->serviceFee ?>
                    </td>
                    <td>
                        <?php echo $price['net'] ?>
                    </td>
                    <td class="text-right">
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][selling]')->input('number', [
                            'class' => 'form-control alt-quote-price',
                            'min' => FlightQuotePaxPriceForm::getMinDecimalVal(),
                            'max' => FlightQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01
                        ])->label(false) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
