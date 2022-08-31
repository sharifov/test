<?php

use modules\quoteAward\src\entities\QuoteFlightProgram;

/**
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \modules\quoteAward\src\forms\AwardQuoteForm
 */

?>

<div>
    <h5>Price List</h5>

    <div style="margin-top: 15px">
        <?php if (count($model->flights)) : ?>
            <?php
            foreach ($model->flights as $index => $flight) :
                ?>
                <p>
                    <?= 'Flight ' . $flight->id ?>
                </p>
                <table class="table table-neutral" id="price-table">
                    <thead>
                    <tr>
                        <th>Pax</th>
                        <th></th>
                        <th>Selling Price</th>
                        <th>Net Price</th>
                        <th>Fare</th>
                        <th>Taxes</th>
                        <th>Mark-up</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($flight->prices as $type => $price) :
                        ?>
                        <tr id="flight-price-index-<?= $flight->id . '-' . $type ?>">
                            <td style="width:105px"><?= $price->passenger_count . ' x ' . $type ?></td>
                            <td>
                                <div class="js-display-quote-program <?= $price->is_required_award_program ? '' : 'd-none' ?>"
                                     data-id="<?= $flight->id ?>">
                                    <?= $form->field($price, '[' . $flight->id . '-' . $type . ']award_program')->dropDownList(QuoteFlightProgram::getList(), ['required' => 'required'])->label(false) ?>
                                </div>
                            </td>
                            <td><?= $form->field($price, '[' . $flight->id . '-' . $type . ']selling', [
                                    'options' => [
                                        'class' => 'input-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control alt-quote-price',
                                    'maxlength' => 10,
                                ]) ?>
                            </td>

                            <td>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']net', [
                                    'options' => [
                                        'class' => 'input-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control ',
                                    'readonly' => true,
                                    'maxlength' => 10,
                                ]) ?>
                            </td>
                            <td>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']fare', [
                                    'options' => [
                                        'class' => 'input-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control alt-quote-price',
                                    'readonly' => true,
                                    'maxlength' => 10,
                                ]) ?>
                            </td>
                            <td>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']taxes', [
                                    'options' => [
                                        'class' => 'input-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control alt-quote-price',
                                    'readonly' => true,
                                    'maxlength' => 10,
                                ]) ?>
                            </td>
                            <td>
                                <?= $form->field($price, '[' . $flight->id . '-' . $type . ']mark_up', [
                                    'options' => [
                                        'class' => 'input-group',
                                    ],
                                    'template' => '<div class="input-group"><span class="input-group-addon">$</span>{input}</div>{error}'
                                ])->textInput([
                                    'class' => 'form-control alt-quote-price mark-up',
                                    'readonly' => true,
                                    'maxlength' => 10,
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>
