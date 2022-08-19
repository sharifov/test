<?php

/**
 * @var string $message
 * @var array $errors
 * @var \modules\flight\src\useCases\voluntaryRefund\manualCreate\VoluntaryRefundCreateForm $refundForm
 */

use common\components\i18n\Formatter;
use common\models\query\CurrencyQuery;
use frontend\helpers\JsonHelper;
use kartik\form\ActiveForm;
use modules\flight\src\useCases\voluntaryRefund\manualCreate\AuxiliaryOptionForm;
use modules\flight\src\useCases\voluntaryRefund\manualCreate\TicketForm;
use modules\product\src\abac\ProductQuoteAbacObject;
use yii\bootstrap4\Alert;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\VarDumper;
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
      pjaxOffFormSubmit('#voluntary_refund_create_pjax');
  </script>
    <?php $currency = (CurrencyQuery::getCurrencySymbolByCode($refundForm->getRefundForm()->currency) ?: $refundForm->getRefundForm()->currency); ?>
    <?php
      $infoText = 'Refundable amount: ';
      $infoText .= $refundForm->originData['allow'] ?
        '<span style="color: green">Successfully identified</span>' :
        '<span style="color:blue;">Check manually</span>';
    ?>
    <?php
    /** @abac null, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_VIEW_VOL_REFUND_ORIGIN_DATA_FROM_BO, View Origin Data From BO (Voluntary Refund) */
    $viewOriginDataFromBO = \Yii::$app->abac->can(null, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_VIEW_VOL_REFUND_ORIGIN_DATA_FROM_BO);
    ?>
    <?php Pjax::begin([
    'id' => 'voluntary_refund_create_pjax',
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
    'id' => 'voluntary_refund_create_form',
    'enableClientValidation' => false
    ]) ?>

    <?php echo $activeForm->errorSummary($refundForm); ?>

    <?= $activeForm->field($refundForm, 'bookingId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($refundForm, 'originProductQuoteId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($refundForm, 'orderId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($refundForm, 'caseId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($refundForm->getRefundForm(), 'currency')->hiddenInput()->label(false) ?>
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
        <div class="d-flex justify-content-between align-items-center">
          <h6><b>Tickets</b></h6>
          <h6><b><?= $infoText ?></b></h6>
          <?php if ($viewOriginDataFromBO) : ?>
            <span data-toggle="collapse" href="#collapseResponseBO" role="button" aria-expanded="false" aria-controls="collapseExample">
              <i class="fas fa-info-circle"></i> Origin Data From BO
            </span>
          <?php endif; ?>
        </div>
        <?php if ($viewOriginDataFromBO) : ?>
          <div id="collapseResponseBO" class="collapse">
            <div class="card-body card">
              <h4>Search Query Params</h4>
              <pre><?= Html::encode(VarDumper::dumpAsString($refundForm->originData)) ?></pre>
            </div>
          </div>
        <?php endif; ?>
        <?php $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $refundForm->getRefundForm()->getTicketForms(),
            'totalCount' => 0,
            'pagination' => false
        ]) ?>
        <?php echo \yii\grid\GridView::widget([
          'dataProvider' => $dataProvider,
          'columns' => [
              ['class' => SerialColumn::class],
              [
                  'attribute' => 'number',
                  'content' => static function (TicketForm $model, $index) use ($activeForm) {
                      return $activeForm->field($model, 'refundAllowed')->hiddenInput([
                          'name' => 'refund[tickets][' . $index . '][refundAllowed]'
                      ])->label(false) . $activeForm->field($model, 'number')->hiddenInput([
                          'name' => 'refund[tickets][' . $index . '][number]'
                      ])->label(false) . $model->number;
                  }
              ],
              [
                  'attribute' => 'selling',
                  'content' => static function (TicketForm $model, $index) use ($activeForm, $refundForm, $currency) {
                      return $activeForm->field($model, 'selling', [
                          'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                      ])->input('number', [
                          'readonly' => $refundForm->ticketDataReadOnly,
                          'name' => 'refund[tickets][' . $index . '][selling]',
                          'step' => 0.01,
                          'data-total-input-row-attr' => 'refund[tickets][' . $index . '][refundable]',
                          'data-calc-input-rows-attr' => 'refund[tickets][' . $index . '][selling],refund[tickets][' . $index . '][airlinePenalty],refund[tickets][' . $index . '][processingFee]',
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
                          'name' => 'refund[tickets][' . $index . '][airlinePenalty]',
                          'step' => 0.01,
                          'class' => 'totalCalc',
                          'data-total-input-id' => 'totalAirlinePenalty',
                          'data-total-input-row-attr' => 'refund[tickets][' . $index . '][refundable]',
                          'data-calc-input-rows-attr' => 'refund[tickets][' . $index . '][selling],refund[tickets][' . $index . '][airlinePenalty],refund[tickets][' . $index . '][processingFee]',
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
                          'name' => 'refund[tickets][' . $index . '][processingFee]',
                          'step' => 0.01,
                          'class' => 'totalCalc',
                          'data-total-input-id' => 'totalProcessingFee',
                          'data-total-input-row-attr' => 'refund[tickets][' . $index . '][refundable]',
                          'data-calc-input-rows-attr' => 'refund[tickets][' . $index . '][selling],refund[tickets][' . $index . '][airlinePenalty],refund[tickets][' . $index . '][processingFee]',
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
                          'readonly' => $refundForm->ticketDataReadOnly,
                          'name' => 'refund[tickets][' . $index . '][refundable]',
                          'step' => 0.01,
                          'class' => 'totalCalc',
                          'data-total-input-id' => 'totalRefundable',
                      ])->label(false);
                  },
                  'format' => 'raw'
              ],
              [
                  'attribute' => 'status',
                  'content' => static function (TicketForm $model, $index) use ($activeForm) {
                      $inputOptions = [
                          'name' => 'refund[tickets][' . $index . '][status]',
                      ];
                      return $activeForm->field($model, 'status')->hiddenInput($inputOptions)->label(false) . $model->status;
                  }
              ],

          ]
      ]) ?>

      <h6><b>Options</b></h6>
      <?php $dataProvider = new \yii\data\ArrayDataProvider([
          'allModels' => $refundForm->getRefundForm()->getAuxiliaryOptionsForms(),
          'totalCount' => 0,
          'pagination' => false
      ]) ?>
      <?php echo \yii\grid\GridView::widget([
          'dataProvider' => $dataProvider,
          'columns' => [
              ['class' => SerialColumn::class],
              [
                  'attribute' => 'type',
                  'content' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm) {
                      $type = $model->type;
                      return $activeForm->field($model, 'type')->hiddenInput([
                          'name' => 'refund[auxiliaryOptions][' . $index . '][type]'
                      ])->label(false) . $type;
                  }
              ],
              [
                  'attribute' => 'amount',
                  'content' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm) {
                      return $activeForm->field($model, 'amount')->hiddenInput([
                          'name' => 'refund[auxiliaryOptions][' . $index . '][amount]'
                      ])->label(false) . $model->amount;
                  }
              ],
              [
                  'attribute' => 'refundable',
                  'content' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm) {
                      $inputOptions = [
                          'name' => 'refund[auxiliaryOptions][' . $index . '][refundable]',
                      ];
                      if ($model->refundAllow) {
                          $inputOptions['data-total-input-id'] = 'totalRefundable';
                      }
                      return $activeForm->field($model, 'refundable')->hiddenInput($inputOptions)->label(false) . $model->refundable;
                  }
              ],
              [
                  'attribute' => 'status',
                  'content' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm) {
                      $inputOptions = [
                          'name' => 'refund[auxiliaryOptions][' . $index . '][status]',
                      ];
                      return $activeForm->field($model, 'status')->hiddenInput($inputOptions)->label(false) . $model->status;
                  }
              ],
              [
                  'attribute' => 'refundAllow',
                  'content' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm) {
                      $format = new Formatter();
                      return $activeForm->field($model, 'refundAllow')->hiddenInput([
                          'name' => 'refund[auxiliaryOptions][' . $index . '][refundAllow]'
                      ])->label(false) . $format->asBoolean($model->refundAllow);
                  }
              ],
              [
                  'attribute' => 'details',
                  'value' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm) {
                      $content = $activeForm->field($model, 'details')->hiddenInput([
                          'name' => 'refund[auxiliaryOptions][' . $index . '][details]'
                      ])->label(false);
                      $type = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $model->type . 'details'));
                    if ($model->details) {
                        $content .= Html::a(
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
                          VarDumper::dumpAsString(JsonHelper::decode($model->details), 10, true) . '</pre>' : '-';

                      return $content;
                  },
                  'format' => 'raw',
                  'contentOptions' => [
                      'style' => ['max-width' => '800px', 'word-wrap' => 'break-word !important'],
                  ],
              ],
              [
                  'attribute' => 'amountPerPax',
                  'value' => static function (AuxiliaryOptionForm $model, $index) use ($activeForm) {
                      $content = $activeForm->field($model, 'amountPerPax')->hiddenInput([
                          'name' => 'refund[auxiliaryOptions][' . $index . '][amountPerPax]'
                      ])->label(false);
                      $hash = md5($model->amountPerPax);
                    if ($model->amountPerPax) {
                        $content .= Html::a(
                            '<i class="fas fa-eye"></i> details</a>',
                            null,
                            [
                              'class' => 'btn btn-sm btn-success',
                              'data-pjax' => 0,
                              'onclick' => '(function ( $event ) { $("#data_' . $hash . '").toggle(); })();',
                              ]
                        );
                    }
                      $content .= $model->amountPerPax ?
                          '<pre id="data_' . $hash . '" style="display: none;">' .
                          VarDumper::dumpAsString(JsonHelper::decode($model->amountPerPax), 10, true) . '</pre>' : '-';

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
          <td><?= $refundForm->getRefundForm()->currency ?></td>

          <td><?= $activeForm->field($refundForm->getRefundForm(), 'totalPaid', [
                  'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
              ])->input('number', [
                  'readonly' => $refundForm->refundDataReadOnly,
                  'step' => 0.01
              ])->label(false) ?></td>
          <td><?= $activeForm->field($refundForm->getRefundForm(), 'totalAirlinePenalty', [
                  'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
              ])->input('number', [
                  'readonly' => $refundForm->refundDataReadOnly,
                  'step' => 0.01,
                  'id' => 'totalAirlinePenalty'
              ])->label(false) ?></td>
          <td><?= $activeForm->field($refundForm->getRefundForm(), 'totalProcessingFee', [
                  'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
              ])->input('number', [
                  'readonly' => $refundForm->refundDataReadOnly,
                  'step' => 0.01,
                  'id' => 'totalProcessingFee'
              ])->label(false) ?></td>
          <td><?= $activeForm->field($refundForm->getRefundForm(), 'totalRefundable', [
                  'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
              ])->input('number', [
                  'readonly' => $refundForm->refundDataReadOnly,
                  'step' => 0.01,
                  'id' => 'totalRefundable'
              ])->label(false) ?></td>
          <td><?= $activeForm->field($refundForm->getRefundForm(), 'refundCost', [
                  'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
              ])->input('number', [
                  'step' => 0.01
              ])->label(false) ?></td>
        </tr>
        </tbody>
      </table>

      <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Submit', [
            'class' => 'btn btn-sm btn-success',
            'id' => 'voluntary_refund_create_btn_submit'
              ]) ?>

        <?= Html::resetButton('<i class="fas fa-redo-alt"></i> Reset Form', [
            'class' => 'btn btn-sm btn-warning',
            'id' => 'voluntary_refund_create_btn_submit'
        ]) ?>
      </div>
    </div>
  </div>
    <?php ActiveForm::end(); ?>

    <?php
    $js = <<<JS
$("#voluntary_refund_create_pjax").on("pjax:start", function() {
    $('#voluntary_refund_create_btn_submit').find('i').replaceWith('<i class="fa fa-spin fa-spinner"></i>');
    $('#voluntary_refund_create_btn_submit').addClass('disabled').prop('disabled', true);
});

(function () {
  $(document).on('input', '.totalCalc', function (e) {
      let _self = $(this);
      let targetInputId = _self.attr('data-total-input-id');
      
      if (_self.attr('data-calc-input-rows-attr') && _self.attr('data-total-input-row-attr')) {
          let totalRow = 0;
          let calcInputRowsNames = _self.attr('data-calc-input-rows-attr').split(',');
          let targetInputRow = $('#voluntary_refund_create_form').find("input[name='"+_self.attr('data-total-input-row-attr')+"']");
          
          calcInputRowsNames.forEach(function (value, i) {
             if (i === 0) {
                 totalRow = +$('#voluntary_refund_create_form').find("input[name='"+value+"']").val();
             } else {
                 totalRow -= +$('#voluntary_refund_create_form').find("input[name='"+value+"']").val();
             }
          });
          targetInputRow.val((Math.round(totalRow * 100) / 100).toFixed(2));
          calcTotal(targetInputRow.attr('data-total-input-id'));
      }
      
      calcTotal(targetInputId);
  });
  
  function calcTotal(targetInputId) {
      let total = 0;
      $('#voluntary_refund_create_form').find("[data-total-input-id='"+targetInputId+"']").each(function (i, e) {
          total += +$(e).val();
      });
      $('#'+targetInputId).val((Math.round(total * 100) / 100).toFixed(2));
  }
})();
JS;
    $this->registerJs($js);
    Pjax::end();
endif; ?>
