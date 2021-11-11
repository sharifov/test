<?php

/**
 * @var string $message
 * @var array $errors
 * @var \modules\flight\src\useCases\voluntaryRefund\manualUpdate\VoluntaryRefundUpdateForm $form
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
        pjaxOffFormSubmit('#voluntary_refund_edit_pjax');
    </script>
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
    <h4>Refund data</h4>
    <div class="row">
        <div class="col-md-12">
            <?= $activeForm->field($form, 'refundId')->hiddenInput()->label(false) ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Booking Id</th>
                    <th>Currency</th>
                    <th>Total Paid</th>
                    <th>Refund Cost</th>
                    <th>Total Processing Fee</th>
                    <th>Total Airline Penalty</th>
                    <th>Total Refundable</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= $form->bookingId ?></td>
                    <td><?= $form->currency ?></td>

                    <td><?= $activeForm->field($form, 'totalPaid')->input('number', [
                            'step' => 0.01
                        ])->label(false) ?></td>
                    <td><?= $activeForm->field($form, 'refundCost')->input('number', [
                            'step' => 0.01
                        ])->label(false) ?></td>
                    <td><?= $activeForm->field($form, 'totalProcessingFee')->input('number', [
                            'step' => 0.01
                        ])->label(false) ?></td>
                    <td><?= $activeForm->field($form, 'totalAirlinePenalty')->input('number', [
                            'step' => 0.01
                        ])->label(false) ?></td>
                    <td><?= $activeForm->field($form, 'totalRefundable')->input('number', [
                            'step' => 0.01
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
                <?= Html::hiddenInput('' . $form->formName() . '[tickets]') ?>
                <?php foreach ($form->tickets as $key => $ticketForm) : ?>
                    <tr>
                        <?= $activeForm->field($ticketForm, 'id')->hiddenInput([
                            'name' => '' . $form->formName() . '[tickets][' . $key . '][id]'
                        ])->label(false) ?>
                        <td><?= $ticketForm->number ?></td>
                        <td><?= $activeForm->field($ticketForm, 'selling')->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $key . '][selling]',
                                'step' => 0.01
                            ])->label(false) ?></td>
                        <td><?= $activeForm->field($ticketForm, 'airlinePenalty')->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $key . '][airlinePenalty]',
                                'step' => 0.01
                            ])->label(false) ?></td>
                        <td><?= $activeForm->field($ticketForm, 'processingFee')->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $key . '][processingFee]',
                                'step' => 0.01
                            ])->label(false) ?></td>
                        <td><?= $activeForm->field($ticketForm, 'refundable')->input('number', [
                                'name' => '' . $form->formName() . '[tickets][' . $key . '][refundable]',
                                'step' => 0.01
                            ])->label(false) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?= Html::submitButton('<i class="fa fa-save"></i> Submit', [
                'class' => 'btn btn-sm btn-success',
                'id' => 'voluntary_refund_update_btn_submit'
            ]) ?>
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
