<?php

use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\flight\src\useCases\flightQuote\createManually\VoluntaryQuotePaxPriceForm;
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
        <h6>Pax pricing:</h6>
        <table class="table table-striped table-bordered">

            <tr>
                <th>Nr</th>
                <th>Pax Type</th>
                <th>Count</th>
                <th>Price Difference, <?= Html::encode($originProductQuote->pq_origin_currency) ?></th>
                <th>Airline Penalty, <?= Html::encode($originProductQuote->pq_origin_currency) ?></th>
                <th>Processing Fee, <?= Html::encode($originProductQuote->pq_origin_currency) ?></th>
                <th>Agent Markup, <?= Html::encode($originProductQuote->pq_origin_currency) ?></th>
                <th>Price, <?= Html::encode($originProductQuote->pq_origin_currency) ?> <span id="box_loading"></span></th>
            </tr>


            <?php
            foreach ($createQuoteForm->prices as $index => $price) : ?>
                <tr class="pax-type-<?php echo $price['paxCode'] ?>" id="price-index-<?php echo $index ?>">
                    <td style="padding-top: 14px;">
                        <?= ($index + 1) ?>.
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][paxCode]')->hiddenInput()->label(false) ?>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][paxCodeId]')->hiddenInput()->label(false) ?>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][cnt]')->hiddenInput()->label(false) ?>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][net]')->hiddenInput()->label(false) ?>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][systemMarkUp]')->hiddenInput()->label(false) ?>
                    </td>
                    <td style="padding-top: 14px;">
                        <?php echo $price['paxCode'] ?>
                    </td>
                    <td style="padding-top: 14px;">
                        <?php echo $price['cnt'] ?>
                    </td>
                    <td>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][fare]')->input('number', [
                            'class' => 'form-control alt-quote-price',
                            'min' => VoluntaryQuotePaxPriceForm::getMinDecimalVal(),
                            'max' => VoluntaryQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01
                        ])->label(false) ?>
                    </td>
                    <td>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][taxes]')->input('number', [
                            'class' => 'form-control alt-quote-price',
                            'min' => VoluntaryQuotePaxPriceForm::getMinDecimalVal(),
                            'max' => VoluntaryQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01
                        ])->label(false) ?>
                    </td>
                    <td>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][systemMarkUp]')->input('number', [
                            'class' => 'form-control ',
                            'max' => VoluntaryQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01,
                            'readonly' => 'readonly',
                        ])->label(false) ?>
                    </td>
                    <td>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][markup]')->input('number', [
                            'class' => 'form-control alt-quote-price',
                            'max' => VoluntaryQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01
                        ])->label(false) ?>
                    </td>
                    <td>
                        <?php echo $form->field($createQuoteForm, 'prices[' . $index . '][selling]')->input('number', [
                            'class' => 'form-control ',
                            'min' => VoluntaryQuotePaxPriceForm::getMinDecimalVal(),
                            'max' => VoluntaryQuotePaxPriceForm::getMaxDecimalVal(),
                            'step' => 0.01,
                            'readonly' => 'readonly',
                        ])->label(false) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
