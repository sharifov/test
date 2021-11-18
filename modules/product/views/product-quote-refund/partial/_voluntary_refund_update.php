<?php

/**
 * @var string $message
 * @var array $errors
 * @var \modules\flight\src\useCases\voluntaryRefund\manualUpdate\VoluntaryRefundUpdateForm $form
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
    <?php $currency = (CurrencyQuery::getCurrencySymbolByCode($form->currency) ?: $form->currency); ?>
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

    <?php echo $activeForm->errorSummary($form) ?>

    <?= \common\widgets\Alert::widget() ?>
    <div class="row">
        <div class="col-md-12">
            <?= $activeForm->field($form, 'refundId')->hiddenInput()->label(false) ?>
            <?php $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $form->tickets,
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
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $form) {
                            return $activeForm->field($model, 'id')->hiddenInput([
                                    'name' => '' . $form->formName() . '[tickets][' . $index . '][id]'
                                ])->label(false) . $model->number;
                        }
                    ],
                    [
                        'attribute' => 'selling',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $form, $currency) {
                            return $activeForm->field($model, 'selling', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $index . '][selling]',
                                'step' => 0.01
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'airlinePenalty',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $form, $currency) {
                            return $activeForm->field($model, 'airlinePenalty', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $index . '][airlinePenalty]',
                                'step' => 0.01
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'processingFee',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $form, $currency) {
                            return $activeForm->field($model, 'processingFee', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $index . '][processingFee]',
                                'step' => 0.01
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'refundable',
                        'content' => static function (TicketForm $model, $index) use ($activeForm, $form, $currency) {
                            return $activeForm->field($model, 'refundable', [
                                'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                            ])->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $index . '][refundable]',
                                'step' => 0.01
                            ])->label(false);
                        },
                        'format' => 'raw'
                    ],
                    'status',
                    'refundAllowed:boolean',
                ]
            ]) ?>

          <h6><b>Options</b></h6>
            <?php $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $form->options,
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
                    'amount',
                    'refundable',
                    'status',
                    'refundAllow:boolean',
                    [
                        'attribute' => 'details',
                        'value' => static function (AuxiliaryOptionForm $model) {
                            $content = '';
                            $type = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $model->type));
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
                            $type = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $model->type));
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
                <td><?= $form->bookingId ?></td>
                <td><?= $form->currency ?></td>

                <td><?= $activeForm->field($form, 'totalPaid', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($form, 'totalAirlinePenalty', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($form, 'totalProcessingFee', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01,
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($form, 'totalRefundable', [
                        'addon' => ['prepend' => ['content' => $currency, 'options' => [ 'class' => 'alert-success', 'style' => 'padding-top: 0; padding-bottom: 0;']]]
                    ])->input('number', [
                        'step' => 0.01,
                    ])->label(false) ?></td>
                <td><?= $activeForm->field($form, 'refundCost', [
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
JS;
$this->registerJs($js);
