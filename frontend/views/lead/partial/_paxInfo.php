<?php
/**
 * @var $lead Lead
 * @var $this \yii\web\View
 */

use common\models\Lead;
use common\models\Quote;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

$formID = sprintf('pax-info-form-%d', $lead->id);
$quote = $lead->getAppliedAlternativeQuotes();

$js = <<<JS
    $('#cancel-pax').click(function (e) {
        e.preventDefault();
        $('#create-quote').modal('hide');
    });

    $('#save-pnr-pax').click(function (event) {
        var form = $('#$formID');
        $.ajax({
            type: 'post',
            url: form.attr('action'),
            data: form.serialize(),
            success: function (data) {
                $.each(data.errors, function( index, value ) {
                    $('#'+index).parent().addClass('has-error');
                });
            },
            error: function (error) {			
                console.log('Error: ' + error);			
            }
        });
    });
JS;
$this->registerJs($js);

?>
<?php $form = ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['lead/add-pnr', 'leadId' => $lead->id]),
    'errorCssClass' => '',
    'successCssClass' => '',
    'id' => $formID
]) ?>
    <!------------- Add/Edit Alternative Quote Form ------------->
    <div class="alternatives__item">
        <div class="table-wrapper table-responsive ticket-details-block__table mb-20">
            <?= $form->field($lead, 'id', [
                'options' => [
                    'tag' => false,
                ],
                'template' => '{input}'
            ])->hiddenInput() ?>
            <div class="row" style="margin: 0 0 20px;">
                <div class="col-md-6 form-inline">
                    <?= $form->field($lead->additionalInformationForm[0], 'pnr')->textInput()->label('Add PNR:&nbsp;') ?>
                    <div class="form-group" style="margin: 0 0 10px 10px;">
                        <label class="control-label">GDS:&nbsp;</label>
                        <?= Quote::getGDSName($quote->gds) ?>
                    </div>
                    <div class="form-group" style="margin: 0 0 10px 10px;">
                        <label class="control-label">PCC:&nbsp;</label>
                        <?= $quote->pcc ?>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <?= Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', [
                        'id' => 'cancel-pax',
                        'class' => 'btn btn-danger btn-with-icon'
                    ]) ?>
                    <?= Html::button('<span class="btn-icon"><i class="fa fa-save"></i></span><span>Save</span>', [
                        'id' => 'save-pnr-pax',
                        'class' => 'btn btn-primary btn-with-icon'
                    ]) ?>
                </div>
            </div>
            <table class="table table-striped table-neutral">
                <thead>
                <tr>
                    <th style="min-width: 100px;">Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($lead->additionalInformationForm[0]->paxInfo as $index => $passenger) : ?>
                    <tr>
                        <td class="td-input">
                            <?= $passenger['pax'] ?>
                        </td>
                        <td class="td-input">
                            <?= $passenger['dob'] ?>
                        </td>
                        <td class="td-input">
                            <?= $passenger['sex'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php ActiveForm::end() ?>