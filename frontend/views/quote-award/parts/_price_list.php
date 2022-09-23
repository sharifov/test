<?php

use modules\quoteAward\src\entities\QuoteFlightProgram;
use modules\quoteAward\src\entities\QuoteFlightProgramQuery;
use yii\helpers\Html;

/**
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

?>

<div>
    <div style="margin-top: 15px">
        <?php if (count($model->flights)) : ?>
            <table class="table table-neutral table-award-price" id="price-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Miles</th>
                    <th>Selling Price</th>
                    <th>Net Price</th>
                    <th>Fare</th>
                    <th>Taxes</th>
                    <th>Mark-up</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($model->flights as $index => $flight) :
                    ?>
                    <?php $totalPriceFlight = $flight->getTotalPrice(); ?>

                    <tr class="total-price_flight">
                        <td><?= 'Flight ' . ($flight->id + 1) ?></td>
                        <td
                        </td>
                        <td>
                            <div id="<?= Html::getInputId($flight, '[' . $flight->id . ']selling') ?>"><?= $totalPriceFlight['selling'] ?></div>
                        </td>
                        <td>
                            <div id="<?= Html::getInputId($flight, '[' . $flight->id . ']net') ?>"><?= $totalPriceFlight['net'] ?></div>
                        </td>
                        <td>
                            <div id="<?= Html::getInputId($flight, '[' . $flight->id . ']fare') ?>"><?= $totalPriceFlight['fare'] ?></div>
                        </td>
                        <td>
                            <div id="<?= Html::getInputId($flight, '[' . $flight->id . ']taxes') ?>"><?= $totalPriceFlight['taxes'] ?></div>
                        </td>
                        <td>
                            <div id="<?= Html::getInputId($flight, '[' . $flight->id . ']markUp') ?>"><?= $totalPriceFlight['markUp'] ?></div>
                        </td>
                    </tr>
                    <?php
                    foreach ($flight->prices as $type => $price) :
                        ?>
                        <tr id="flight-price-index-<?= $flight->id . '-' . $type ?>" class="js-flight-price">
                            <td style="width:105px">
                                <i class="fa fa-user"></i>
                                <?= $type . ' x ' . $price->passenger_count ?>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']flight', ['options' => ['tag' => false,], 'template' => '{input}'])->hiddenInput() ?>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']passenger_type', ['options' => ['tag' => false,], 'template' => '{input}'])->hiddenInput() ?>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']oldParams', ['options' => ['tag' => false,], 'template' => '{input}'])->hiddenInput() ?>
                            </td>
                            <td style="width:80px">
                                <div class="js-display-quote-program <?= $flight->isRequiredAwardProgram() ? '' : 'd-none' ?>"
                                     data-id="<?= $flight->id ?>">
                                    <?= $form->field($price, '[' . $flight->id . '-' . $type . ']miles')
                                        ->textInput(['type' => 'number', 'class' => 'form-control alt-award-quote-price'])
                                        ->label(false)
                                    ?>
                                </div>

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
                        </tr>
                    <?php endforeach; ?>


                <?php endforeach; ?>
                </tbody>
            </table>

            <table class="table">
                <?php $totalPrice = $model->getTotalPrice(); ?>
                <tbody>
                <tr class="total-price_flight">
                    <td style="width:105px">Booking total :</td>
                    <td style="width:80px"></td>
                    <td style="width:150px">
                        <div id="<?= Html::getInputId($model, 'selling') ?>">
                            <?= $totalPrice['selling'] ?>
                        </div>
                    </td>
                    <td style="width:150px">
                        <div id="<?= Html::getInputId($model, 'net') ?>"><?= $totalPrice['net'] ?></div>
                    </td>
                    <td style="width:150px">
                        <div id="<?= Html::getInputId($model, 'fare') ?>"><?= $totalPrice['fare'] ?></div>
                    </td>
                    <td style="width:150px">
                        <div id="<?= Html::getInputId($model, 'taxes') ?>"><?= $totalPrice['taxes'] ?></div>
                    </td>
                    <td style="width:150px">
                        <div id="<?= Html::getInputId($model, 'markUp') ?>"><?= $totalPrice['markUp'] ?></div>
                    </td>
                </tr>
                </tbody>

            </table>

        <?php endif; ?>
    </div>
</div>

<style>
    .table-award-price td, .table-award-price th {
        padding: 5px;
    }

    .table-award-price .input-group, .table-award-price .form-group {
        margin-bottom: 2px;
    }

    .table-award-price th {
        font-weight: 400;
        font-size: 12px;
        line-height: 16px;
        color: #8895A7;
    }

    .total-price_flight {
        background-color: #F4F7FA;
        font-weight: 500;
        font-size: 14px;
        line-height: 24px;
        color: #474F58;
    }
</style>
