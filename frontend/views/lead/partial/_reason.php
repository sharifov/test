<?php
/**
 * @var $lead \common\models\Lead
 * @var $reason \common\models\Reason
 * @var $activeLeadIds array
 */

use yii\helpers\Html;

$activeLeadIds = '[' . implode(',', $activeLeadIds) . ']';

$js = <<<JS
$('#salesale-snooze_for').attr('readonly', true);
$('#unassign-form').on('beforeSubmit', function () {
    $('#reason-other').val($.trim($('#reason-other').val()));
    if ($('#reason-reason').val() == 'Other' && $('#reason-other').val().length == 0) {
        $('#reason-other').parent().addClass('has-error');
        return false
    }
    $('#reason-duplicateleadid').val($.trim($('#reason-duplicateleadid').val()));
    if ($('#reason-reason').val() == 'Duplicate') {
        if ($('#reason-duplicateleadid').val().length == 0) {
            $('#reason-duplicateleadid').parent().addClass('has-error');
            return false
        } else {
            var activeLeadIds = $activeLeadIds;
            if ($.inArray(parseInt($('#reason-duplicateleadid').val()), $activeLeadIds) != -1) {
                $('#reason-duplicateleadid').parent().removeClass('has-error');
                return true;
            }
            $('#reason-duplicateleadid').parent().addClass('has-error');
            return false;
        }
    }
    if ($('#salesale-snooze_for').length != 0 && $('#salesale-snooze_for').val().length == 0) {
         $('#salesale-snooze_for').parent().parent().addClass('has-error');
        return false
    }
    if ($('#reason-returntoqueue').val() == 'processing'  && 
        $('#select-agent').length != 0  && 
        $('#select-agent').val().length == 0) 
    {
        $('#reason-other').parent().removeClass('has-error');
        $('#assign-agent-wrapper').addClass('has-error');
        return false
    }
    return true;
});
JS;
$this->registerJs($js);

$reasonUrl = \yii\helpers\Url::to([
    'lead/unassign',
    'id' => $lead->id
]);
$reasonForm = \yii\widgets\ActiveForm::begin([
    'action' => $reasonUrl,
    'id' => 'unassign-form'
]) ?>

<?= $reasonForm->field($reason, 'queue', [
    'options' => [
        'tag' => false,
    ],
    'template' => '{input}'
])->hiddenInput()
?>
    <div class="row">
        <div class="<?= (in_array($reason->queue, ['snooze', 'return'])) ? 'col-sm-6' : 'col-sm-12' ?>">
            <?= $reasonForm->field($reason, 'reason', [
                'template' => '{label}<div class="select-wrap-label">{input}</div>'
            ])->dropDownList(\common\models\Reason::getReason($reason->queue), [
                'prompt' => 'Select reason',
                'onchange' => "
                var val = $(this).val();
                if (val == 'Other') {
                    $('#unassign-other-wrapper').addClass('in');
                    $('#unassign-duplicate-wrapper').removeClass('in');
                } else if (val == 'Duplicate') {
                    $('#unassign-duplicate-wrapper').addClass('in');
                    $('#unassign-other-wrapper').removeClass('in');
                } else {
                    $('#unassign-other-wrapper').removeClass('in');
                    $('#unassign-duplicate-wrapper').removeClass('in');
                }
            "]) ?>
        </div>
        <?php if ($reason->queue == 'return') : ?>
            <div class="col-sm-6">
                <?= $reasonForm->field($reason, 'returnToQueue', [
                    'template' => '{label}<div class="select-wrap-label">{input}</div>'
                ])->dropDownList(['follow-up' => 'Follow Up', 'processing' => 'Assign to Agent'], [
                    'onchange' => "
                var val = $(this).val();
                if (val == 'processing') {
                    $('#assign-agent-wrapper').addClass('in');
                } else {
                    $('#assign-agent-wrapper').removeClass('in');
                }
            "])->label('Return lead in') ?>
            </div>
        <?php endif; ?>
        <?php if ($reason->queue == 'snooze') : ?>
            <div class="col-sm-6">
                <?= $reasonForm->field($lead, 'snooze_for', ['template' => '{label}{input}'])->widget(
                    \dosamigos\datetimepicker\DateTimePicker::class, [
                    'clientOptions' => [
                        'autoclose' => true,
                        "todayHighlight" => true,
                        "format" => "yyyy-mm-dd hh:ii",
                        "orientation" => "bottom left",
                        "startDate" => date('Y-m-d H:i:s')
                    ]
                ]) ?>
            </div>
        <?php endif; ?>
    </div>

<?php if ($reason->queue == 'return') :
    $employees = \common\models\Employee::getAllEmployees();
    ?>
    <div class="form-group collapse" id="assign-agent-wrapper">
        <label class="control-label" for="reason-reason">Select the agent:</label>
        <div class="select-wrap-label">
            <?= Html::dropDownList('agent', null, $employees, ['id' => 'select-agent',
                'class' => 'form-control',
                'prompt' => 'Select agent',]) ?>
        </div>
    </div>
<?php endif; ?>

    <div class="form-group collapse" id="unassign-duplicate-wrapper">
        <?= $reasonForm->field($reason, 'duplicateLeadId', ['options' => ['tag' => false,]])->textInput() ?>
    </div>

    <div class="form-group collapse" id="unassign-other-wrapper">
        <?= $reasonForm->field($reason, 'other', ['options' => ['tag' => false,]])->textarea(['rows' => 5]) ?>
    </div>

    <div class="actions-btn-wrapper">
        <?= \yii\bootstrap\Html::submitButton('Add', ['class' => 'btn btn-success popover-close-btn']) ?>
    </div>
<?php \yii\widgets\ActiveForm::end() ?>