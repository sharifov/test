<?php

/**
 * @var string $message
 * @var array $errors
 * @var \modules\flight\src\useCases\voluntaryRefund\manualUpdate\VoluntaryRefundUpdateForm $refundForm
 */

use common\models\query\CurrencyQuery;
use frontend\helpers\JsonHelper;
use kartik\form\ActiveForm;
use modules\flight\src\useCases\voluntaryRefund\manualUpdate\AuxiliaryOptionForm;
use modules\flight\src\useCases\voluntaryRefund\manualUpdate\TicketForm;
use yii\bootstrap4\Alert;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

?>

<?php if ($message || $errors) : ?>
    <?= Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
        'body' => $message ?: implode(',', $errors),
    ]); ?>
<?php else : ?>
    <script>
        pjaxOffFormSubmit('#voluntary_refund_edit_pjax');
    </script>
    <?php $currency = (CurrencyQuery::getCurrencySymbolByCode($refundForm->currency) ?: $refundForm->currency); ?>
    <?php Pjax::begin([
        'id' => 'voluntary_refund_edit_pjax',
        'timeout' => 5000,
        'enablePushState' => false,
        'enableReplaceState' => false
    ]) ?>
    <?php $activeForm = ActiveForm::begin([
        'method' => 'post',
        'options' => [
            'data-pjax' => 1,
            'class' => 'panel-body',
        ],
        'id' => 'voluntary_refund_update_form',
        'enableClientValidation' => false
    ]) ?>

    <?php echo $activeForm->errorSummary($refundForm) ?>

    <?= \common\widgets\Alert::widget() ?>

    <div class="row">
        <div class="col-md-4">
            <?= $activeForm->field($refundForm, 'expirationDate', [
                'labelOptions' => ['class' => 'control-label']
            ])->widget(DatePicker::class, [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $activeForm->field($refundForm, 'refundId')->hiddenInput()->label(false) ?>
            <?php $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $refundForm->tickets,
                'totalCount' => 0,
                'pagination' => false
            ]) ?>
            <h6><b>Tickets Info</b></h6>
            <?php echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'attribute' => 'number',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $refundForm) {
                            return $activeForm->field($model, 'id')->hiddenInput([
                                    'name' => $refundForm->formName() . '[tickets][' . $index . '][id]'
                                ])->label(false) . $model->number;
                        }
                    ],
                    [
                        'attribute' => 'selling',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $refundForm, $currency) {
                            return $activeForm->field($model, 'selling', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => $refundForm->formName() . '[tickets][' . $index . '][selling]',
                                'step' => 0.01,
                                'class' => 'totalCalc',
                                'data-total-input-id' => 'totalPaid',
                                'data-total-input-row-attr' => $refundForm->formName() . '[tickets][' . $index . '][refundable]',
                                'data-calc-input-rows-attr' => $refundForm->formName() . '[tickets][' . $index . '][selling],' . $refundForm->formName() . '[tickets][' . $index . '][airlinePenalty],' . $refundForm->formName() . '[tickets][' . $index . '][processingFee]',
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'airlinePenalty',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $refundForm, $currency) {
                            return $activeForm->field($model, 'airlinePenalty', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => $refundForm->formName() . '[tickets][' . $index . '][airlinePenalty]',
                                'step' => 0.01,
                                'data-total-input-row-attr' => $refundForm->formName() . '[tickets][' . $index . '][refundable]',
                                'data-calc-input-rows-attr' => $refundForm->formName() . '[tickets][' . $index . '][selling],' . $refundForm->formName() . '[tickets][' . $index . '][airlinePenalty],' . $refundForm->formName() . '[tickets][' . $index . '][processingFee]',
                                'class' => 'totalCalc',
                                'data-total-input-id' => 'totalAirlinePenalty',
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'processingFee',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $refundForm, $currency) {
                            return $activeForm->field($model, 'processingFee', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => $refundForm->formName() . '[tickets][' . $index . '][processingFee]',
                                'step' => 0.01,
                                'data-total-input-row-attr' => $refundForm->formName() . '[tickets][' . $index . '][refundable]',
                                'data-calc-input-rows-attr' => $refundForm->formName() . '[tickets][' . $index . '][selling],' . $refundForm->formName() . '[tickets][' . $index . '][airlinePenalty],' . $refundForm->formName() . '[tickets][' . $index . '][processingFee]',
                                'class' => 'totalCalc',
                                'data-total-input-id' => 'totalProcessingFee',
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'refundable',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $refundForm, $currency) {
                            return $activeForm->field($model, 'refundable', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => $refundForm->formName() . '[tickets][' . $index . '][refundable]',
                                'step' => 0.01,
                                'class' => 'totalCalc',
                                'data-total-input-id' => 'totalRefundable',
                                'readonly' => true
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    'status'
                ]
            ]) ?>

          <h6><b>Options</b></h6>
            <?php $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $refundForm->options,
                'totalCount' => 0,
                'pagination' => false
            ]) ?>
            <?php echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'attribute' => 'type',
                    ],
                    [
                        'attribute' => 'amount',
                        'content' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm, $refundForm) {
                            return $activeForm->field($model, 'amount')->hiddenInput([
                                    'name' => $refundForm->formName() . '[auxiliaryOptions][' . $index . '][amount]',
                                    'data-total-input-id' => 'totalPaid'
                                ])->label(false) . $model->amount;
                        }
                    ],
                    [
                        'attribute' => 'refundable',
                        'content' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm, $refundForm) {
                            $inputOptions = [
                                'name' => $refundForm->formName() . '[auxiliaryOptions][' . $index . '][refundable]',
                            ];
                            if ($model->refundAllow) {
                                $inputOptions['data-total-input-id'] = 'totalRefundable';
                            }
                            return $activeForm->field($model, 'refundable')->hiddenInput($inputOptions)->label(false) . $model->refundable;
                        }
                    ],
                    'status',
                    'refundAllow:boolean',
                    [
                        'attribute' => 'details',
                        'value' => static function (AuxiliaryOptionForm $model) {
                            $content = '';
                            $type = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $model->type . 'details'));
                            if ($model->details) {
                                $content = Html::a(
                                    '<i class="fas fa-eye"></i> details</a>',
                                    null,
                                    [
                                        'class' => 'btn btn-sm btn-success',
                                        'data-pjax' => 0,
                                        'onclick' => '(function ( $event ) { $("#data_' . $type . '").toggle(); })();',
                                    ]
                                );
                            }
                            $content .= $model->details ?
                                '<pre id="data_' . $type . '" style="display: none;">' .
                                \yii\helpers\VarDumper::dumpAsString(JsonHelper::decode($model->details), 10, true) . '</pre>' : '-';

                            return $content;
                        },
                        'format' => 'raw',
                        'contentOptions' => [
                            'style' => ['max-width' => '800px', 'word-wrap' => 'break-word !important'],
                        ],
                    ],
                    [
                        'attribute' => 'amountPerPax',
                        'value' => static function (AuxiliaryOptionForm $model) {
                            $content = '';
                            $type = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $model->type . 'amountPerPax'));
                            if ($model->amountPerPax) {
                                $content = Html::a(
                                    '<i class="fas fa-eye"></i> details</a>',
                                    null,
                                    [
                                        'class' => 'btn btn-sm btn-success',
                                        'data-pjax' => 0,
                                        'onclick' => '(function ( $event ) { $("#data_' . $type . '").toggle(); })();',
                                    ]
                                );
                            }
                            $content .= $model->amountPerPax ?
                                '<pre id="data_' . $type . '" style="display: none;">' .
                                \yii\helpers\VarDumper::dumpAsString(JsonHelper::decode($model->amountPerPax), 10, true) . '</pre>' : '-';

                            return $content;
                        },
                        'format' => 'raw',
                        'contentOptions' => [
                            'style' => ['max-width' => '800px', 'word-wrap' => 'break-word !important'],
                        ],
                    ],
                ]
            ]) ?>

            <h6><b>Total</b></h6>
            <table class="table table-bordered">
              <thead>
              <tr>
                <th>Booking Id</th>
                <th>Currency</th>
                <th>Total Paid</th>
                <th>Total Airline Penalty</th>
                <th>Total Processing Fee</th>
                <th>Total Refundable</th>
                <th>Refund Cost</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><?= $refundForm->bookingId ?></td>
                <td><?= $refundForm->currency ?></td>

                <td><?= $activeForm->field($refundForm, 'totalPaid', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01,
                        'id' => 'totalPaid'
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($refundForm, 'totalAirlinePenalty', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01,
                        'id' => 'totalAirlinePenalty'
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($refundForm, 'totalProcessingFee', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01,
                        'id' => 'totalProcessingFee'
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($refundForm, 'totalRefundable', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01,
                        'id' => 'totalRefundable'
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($refundForm, 'refundCost', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01,
                    ])->label(false) ?></td>
              </tr>
              </tbody>
            </table>

            <div class="col-md-12 text-center">
              <?= Html::submitButton('<i class="fa fa-save"></i> Submit', [
                  'class' => 'btn btn-sm btn-success',
                  'id' => 'voluntary_refund_update_btn_submit'
                    ]) ?>

              <?= Html::resetButton('<i class="fas fa-redo-alt"></i> Reset Form', [
                  'class' => 'btn btn-sm btn-warning',
                  'id' => 'voluntary_refund_create_btn_submit'
              ]) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
<?php endif; ?>

<?php
$js = <<<JS
$("#voluntary_refund_edit_pjax").on("pjax:start", function() {
    $('#voluntary_refund_update_btn_submit').find('i').replaceWith('<i class="fa fa-spin fa-spinner"></i>');
    $('#voluntary_refund_update_btn_submit').addClass('disabled').prop('disabled', true);
});

(function () {
  $(document).on('input', '.totalCalc', function (e) {
      let _self = $(this);
      let targetInputId = _self.attr('data-total-input-id');
      
      if (_self.attr('data-calc-input-rows-attr') && _self.attr('data-total-input-row-attr')) {
          let totalRow = 0;
          let calcInputRowsNames = _self.attr('data-calc-input-rows-attr').split(',');
          let targetInputRow = $('#voluntary_refund_update_form').find("input[name='"+_self.attr('data-total-input-row-attr')+"']");
          
          calcInputRowsNames.forEach(function (value, i) {
             if (i === 0) {
                 totalRow = +$('#voluntary_refund_update_form').find("input[name='"+value+"']").val();
             } else {
                 totalRow -= +$('#voluntary_refund_update_form').find("input[name='"+value+"']").val();
             }
          });
          targetInputRow.val((Math.round(totalRow * 100) / 100).toFixed(2));
          calcTotal(targetInputRow.attr('data-total-input-id'));
      }
      
      calcTotal(targetInputId);
  });
  
  function calcTotal(targetInputId) {
      let total = 0;
      $('#voluntary_refund_update_form').find("[data-total-input-id='"+targetInputId+"']").each(function (i, e) {
          total += +$(e).val();
      });
      $('#'+targetInputId).val((Math.round(total * 100) / 100).toFixed(2));
  }
})();
JS;
$this->registerJs($js);
