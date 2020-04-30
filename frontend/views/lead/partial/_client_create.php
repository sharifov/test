<?php

use unclead\multipleinput\MultipleInput;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInputColumn;
use borales\extensions\phoneInput\PhoneInput;
//use frontend\extensions\PhoneInput;

/**
 * @var $this yii\web\View
 * @var $form ActiveForm
 * @var $leadForm sales\forms\lead\LeadCreateForm
 */

?>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($leadForm->client, 'firstName')->textInput() ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($leadForm->client, 'middleName')->textInput() ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($leadForm->client, 'lastName')->textInput() ?>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-12">
        <div id="create-lead-email">
            <?= $form->field($leadForm, 'emails')->widget(MultipleInput::class, [
                'max' => 10,
                'enableError' => true,
                'columns' => [
                    [
                        'name' => 'email',
                        'title' => 'Email',
                    ],
                    [
                        'name' => 'help',
                        'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT
                    ],
                ]
            ])->label(false) ?>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
		<?php
		$js = <<<JS
            document.validationField = {
                isNumeric: function(event) {
                    return !!(event.shiftKey || (event.keyCode < 48 || event.keyCode > 57));
                },
                isNumLockNumericAndPlus: function(event) {
                    return !!(event.keyCode < 96 || event.keyCode > 105 && event.keyCode != 107);
                },
                isRows: function(event) {
                    return !!(event.keyCode < 37 || event.keyCode > 40);
                },
                isSelectionEntire: function(event) {
                    return !!(!event.ctrlKey || event.keyCode != 65);
                },
                isPasteFromClipboard: function(event) {
                    return !!(!event.ctrlKey || event.keyCode != 86);
                },
                isPlusWithoutShift: function(event) {
                    return !!(!event.shiftKey || event.keyCode != 187);
                },
                isBackspace: function(event) {
                    return !!(event.keyCode != 8);
                },
                validate: function (event) {
                    return  this.isNumeric(event) && 
                            this.isNumLockNumericAndPlus(event) && 
                            this.isRows(event) && 
                            this.isSelectionEntire(event) &&
                            this.isPasteFromClipboard(event) && 
                            this.isPlusWithoutShift(event) && 
                            this.isBackspace(event);
                }
            };
JS;
		$this->registerJs($js);
		?>
        <div id="create-lead-phone">
            <?= $form->field($leadForm, 'phones')->widget(MultipleInput::class, [
                'max' => 10,
                'enableError' => true,
                'columns' => [
                    [
                        'name' => 'phone',
                        'title' => 'Phone',
                        'type' => PhoneInput::class,
                        'options' => [
                            'jsOptions' => [
                                'nationalMode' => false,
                                'preferredCountries' => ['us'],
                                'customContainer' => 'intl-tel-input'
                            ],
                            'options' => [
                                'onkeydown' => '
                                        return !validationField.validate(event);
                                    ',
                                'onkeyup' => '
                                        var value = $(this).val();
                                        $(this).val(value.replace(/[^0-9\+]+/g, ""));
                                    '
                            ]
                        ]
                    ],
                    [
                        'name' => 'help',
                        'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT
                    ],
                ]
            ])->label(false) ?>
        </div>
    </div>
    <div class="col-md-4">
		<?= $form->field($leadForm, 'requestIp')->textInput() ?>
    </div>
</div>