<?php

/**
 * @var string $message
 * @var array $errors
 * @var \modules\flight\src\useCases\voluntaryRefund\manualCreate\VoluntaryRefundCreateForm $form
 */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
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
      pjaxOffFormSubmit('#voluntary_refund_create_pjax');
  </script>
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

    <?php echo $activeForm->errorSummary($form); ?>

    <?= $activeForm->field($form, 'bookingId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($form, 'originProductQuoteId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($form, 'orderId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($form, 'caseId')->hiddenInput()->label(false) ?>
    <?= $activeForm->field($form->getRefundForm(), 'currency')->hiddenInput()->label(false) ?>
    <?php foreach ($form->getRefundForm()->getAuxiliaryOptionsForms() as $key => $auxiliaryOptionForm) : ?>
        <?= $activeForm->field($auxiliaryOptionForm, 'type')->hiddenInput([
        'name' => 'refund[auxiliaryOptions][' . $key . '][type]'
    ])->label(false) ?>
        <?= $activeForm->field($auxiliaryOptionForm, 'amount')->hiddenInput([
        'name' => 'refund[auxiliaryOptions][' . $key . '][amount]'
    ])->label(false) ?>
        <?= $activeForm->field($auxiliaryOptionForm, 'refundable')->hiddenInput([
        'name' => 'refund[auxiliaryOptions][' . $key . '][refundable]'
    ])->label(false) ?>
        <?= $activeForm->field($auxiliaryOptionForm, 'refundAllow')->hiddenInput([
        'name' => 'refund[auxiliaryOptions][' . $key . '][refundAllow]'
    ])->label(false) ?>
        <?= $activeForm->field($auxiliaryOptionForm, 'details')->hiddenInput([
        'name' => 'refund[auxiliaryOptions][' . $key . '][details]'
    ])->label(false) ?>
    <?php endforeach; ?>
    <?= \common\widgets\Alert::widget() ?>
  <h4>Refund data</h4>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Booking Id</th>
            <th>Currency</th>
            <th>Total Processing Fee</th>
            <th>Total Airline Penalty</th>
            <th>Total Refundable</th>
            <th>Total Paid</th>
            <th>Refund Cost</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?= $form->bookingId ?></td>
            <td><?= $form->getRefundForm()->currency ?></td>
            <td><?= $activeForm->field($form->getRefundForm(), 'totalProcessingFee')->input('number', [
                'readonly' => $form->refundDataReadOnly,
                ])->label(false) ?></td>
            <td><?= $activeForm->field($form->getRefundForm(), 'totalAirlinePenalty')->input('number', [
                'readonly' => $form->refundDataReadOnly
                ])->label(false) ?></td>
            <td><?= $activeForm->field($form->getRefundForm(), 'totalRefundable')->input('number', [
                'readonly' => $form->refundDataReadOnly
                ])->label(false) ?></td>
            <td><?= $activeForm->field($form->getRefundForm(), 'totalPaid')->input('number', [
                'readonly' => $form->refundDataReadOnly
                ])->label(false) ?></td>
            <td><?= $activeForm->field($form->getRefundForm(), 'refundCost')->input('number', [
                'readonly' => $form->refundDataReadOnly
                ])->label(false) ?></td>
          </tr>
        </tbody>
      </table>

      <h4>Tickets Info</h4>
      <table class="table table-bordered">
        <thead>
        <tr>
          <th>Ticket Number</th>
          <th>Selling</th>
          <th>Airline Penalty</th>
          <th>Processing Fee</th>
          <th>Refundable</th>
        </tr>
        </thead>
        <tbody>
          <?php foreach ($form->getRefundForm()->getTicketForms() as $key => $ticketForm) : ?>
                <?= $activeForm->field($ticketForm, 'number')->hiddenInput([
                'name' => 'refund[tickets][' . $key . '][number]'
              ])->label(false) ?>
            <tr>
              <td><?= $ticketForm->number ?></td>
              <td><?= $activeForm->field($ticketForm, 'selling')->input('number', [
                  'readonly' => $form->ticketDataReadOnly,
                  'name' => 'refund[tickets][' . $key . '][selling]'
              ])->label(false) ?></td>
              <td><?= $activeForm->field($ticketForm, 'airlinePenalty')->input('number', [
                  'readonly' => $form->ticketDataReadOnly,
                  'name' => 'refund[tickets][' . $key . '][airlinePenalty]'
                  ])->label(false) ?></td>
              <td><?= $activeForm->field($ticketForm, 'processingFee')->input('number', [
                  'readonly' => $form->ticketDataReadOnly,
                  'name' => 'refund[tickets][' . $key . '][processingFee]'
                  ])->label(false) ?></td>
              <td><?= $activeForm->field($ticketForm, 'refundable')->input('number', [
                  'readonly' => $form->ticketDataReadOnly,
                  'name' => 'refund[tickets][' . $key . '][refundable]'
                  ])->label(false) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?= Html::submitButton('<i class="fa fa-save"></i> Submit', [
          'class' => 'btn btn-sm btn-success',
          'id' => 'voluntary_refund_create_btn_submit'
      ]) ?>
    </div>
  </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
<?php endif; ?>

<?php
$js = <<<JS
$("#voluntary_refund_create_pjax").on("pjax:start", function() {
    $('#voluntary_refund_create_btn_submit').find('i').replaceWith('<i class="fa fa-spin fa-spinner"></i>');
    $('#voluntary_refund_create_btn_submit').addClass('disabled').prop('disabled', true);
});
JS;
$this->registerJs($js);
