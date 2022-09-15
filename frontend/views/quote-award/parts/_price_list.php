<?php

use modules\quoteAward\src\entities\QuoteFlightProgram;
use modules\quoteAward\src\entities\QuoteFlightProgramQuery;

/**
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

?>

<div>
    <h5 style="font-weight:bold">Price List</h5>
    <div style="margin-top: 15px">
        <?php if (count($model->flights)) : ?>
            <?php
            foreach ($model->flights as $index => $flight) :
                ?>
                <h6 style="font-weight:bold">
                    <?= 'Flight ' . ($flight->id + 1) ?>
                </h6>

                <di class="js-flight-wrap">
                    <div class="js-display-quote-program <?= $flight->isRequiredAwardProgram() ? '' : 'd-none' ?>"
                         data-id="<?= $flight->id ?>">
                        <div class="row">
                            <div class="col-lg-4">
                                <?= $form->field($flight, '[' . $flight->id . ']awardProgram')
                                    ->dropDownList(
                                        QuoteFlightProgram::getList(),
                                        ['required' => 'required', 'class' => 'form-control js-award-program', 'options' => QuoteFlightProgramQuery::getListWithPpm()]
                                    )->label('Flight Program') ?>
                            </div>

                            <div class="col-lg-4">
                                <?= $form->field($flight, '[' . $flight->id . ']ppm')->textInput([
                                    'class' => 'form-control alt-award-quote-price js-award-ppm',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-neutral table-bordered table-award-price" id="price-table">
                    <thead>
                    <tr>
                        <th>Pax</th>
                        <th>Selling Price</th>
                        <th>Net Price</th>
                        <th>Fare</th>
                        <th>Taxes</th>
                        <th>Mark-up</th>
                        <th>Miles</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($flight->prices as $type => $price) :
                        ?>
                        <tr id="flight-price-index-<?= $flight->id . '-' . $type ?>" class="js-flight-price">
                            <td style="width:105px">
                                <?= $price->passenger_count . ' x ' . $type ?>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']flight', ['options' => ['tag' => false,], 'template' => '{input}'])->hiddenInput() ?>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']passenger_type', ['options' => ['tag' => false,], 'template' => '{input}'])->hiddenInput() ?>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']oldParams', ['options' => ['tag' => false,], 'template' => '{input}'])->hiddenInput() ?>
                            </td>
                            <td style="width:150px"><?= $form->field($price, '[' . $flight->id . '-' . $type . ']selling', [
                                    'options' => [
                                        'class' => 'form-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput(['class' => 'form-control alt-award-quote-price', 'maxlength' => 10]) ?>
                            </td>

                            <td style="width:150px">
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']net', [
                                    'options' => [
                                        'class' => 'form-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control ',
                                    'readonly' => true,
                                    'maxlength' => 10
                                ]) ?>
                            </td>
                            <td style="width:150px">
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']fare', [
                                    'options' => [
                                        'class' => 'form-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control alt-award-quote-price',
                                    'maxlength' => 10,
                                ]) ?>
                            </td>
                            <td style="width:150px">
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']taxes', [
                                    'options' => [
                                        'class' => 'form-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control alt-award-quote-price',
                                    'maxlength' => 10,
                                ]) ?>
                            </td>
                            <td style="width:150px">
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']mark_up', [
                                    'options' => [
                                        'class' => 'form-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control alt-award-quote-price mark-up',
                                    'maxlength' => 10,
                                ]) ?>
                            </td>
                            <td style="width:150px">
                                <div class="js-display-quote-program <?= $price->is_required_award_program ? '' : 'd-none' ?>"
                                     data-id="<?= $flight->id ?>">
                                    <?= $form->field($price, '[' . $flight->id . '-' . $type . ']miles')
                                        ->textInput(['type' => 'number', 'class' => 'form-control alt-award-quote-price'])
                                        ->label(false)
                                    ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

            <?php endforeach; ?>

        <?php endif; ?>
    </div>

<style>
    .table-award-price td, .table-award-price th {
        padding: 5px;
    }

    .table-award-price .input-group, .table-award-price .form-group {
        margin-bottom: 2px;
    }
</style>
