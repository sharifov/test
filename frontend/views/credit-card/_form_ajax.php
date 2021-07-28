<?php

use common\models\CreditCard;
use frontend\models\form\CreditCardForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model CreditCardForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $isAjax bool */

\frontend\assets\CreditCardAsset::register($this);

$pjaxId = 'pjax-create-credit-card'
?>

<script>pjaxOffFormSubmit('#<?= $pjaxId ?>');</script>
<div class="credit-card-form">

    <div class="col-md-12">
        <?php if (!empty($modelCc) && $modelCc->isNewRecord) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card-wrapper"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]) ?>

        <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1], 'id' => 'credit-card-create-form']); ?>

        <?php echo $form->errorSummary($model); ?>

        <div class="clearfix"></div>

        <?php if (!empty($modelCc) && $modelCc->isNewRecord) : ?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'cc_number')->textInput(['id' => 'cc_number', 'maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <?= $form->field($model, 'cc_expiration')->textInput(['id' => 'cc_expiration', 'maxlength' => 7]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'cc_cvv')->passwordInput(['id' => 'cc_cvv', 'maxlength' => 4, 'autocomplete' => 'new-password']) ?>
            </div>
        </div>
        <?php endif; ?>

        <?= $form->field($model, 'cc_holder_name')->textInput(['id' => 'cc_holder_name', 'maxlength' => true]) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'cc_type_id')->dropDownList(CreditCard::getTypeList(), ['prompt' => '---']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>


<?php
$js = <<<JS

    $(document).ready(function () {
        let card = new Card({
            // a selector or DOM element for the form where users will
            // be entering their information
            form: '#credit-card-create-form', // *required*
            // a selector or DOM element for the container
            // where you want the card to appear
            container: '.card-wrapper', // *required*
        
            formSelectors: {
                numberInput: 'input#cc_number', // optional — default input[name="number"]
                expiryInput: 'input#cc_expiration', // optional — default input[name="expiry"]
                cvcInput: 'input#cc_cvv', // optional — default input[name="cvc"]
                nameInput: 'input#cc_holder_name' // optional - defaults input[name="name"]
            },
        
            width: 300, // optional — default 350px
            formatting: true, // optional - default true
        
            // Strings for translation - optional
            messages: {
                validDate: 'valid date', // optional - default 'valid thru'
                monthYear: 'mm/yy', // optional - default 'month/year'
            },
        
            // Default placeholders for rendered fields - optional
            placeholders: {
                number: '•••• •••• •••• ••••',
                name: 'Full Name',
                expiry: '••/••',
                cvc: '•••'
            },
        
            masks: {
                cardNumber: '•' // optional - mask card number
            },
        
            // if true, will log helpful messages for setting up Card
            debug: false // optional - default false
        });
        
        //$('#cc_number').val('4111111111111111').trigger('change');
        //$('#cc_number').trigger('keyup');
        
        let evt = document.createEvent('HTMLEvents');
        evt.initEvent('keyup', false, true);
        
        document.getElementById('cc_expiration').dispatchEvent(evt);
        document.getElementById('cc_holder_name').dispatchEvent(evt);
        document.getElementById('cc_number').dispatchEvent(evt);
        
        evt.initEvent('input', false, true);
        document.getElementById('cc_number').dispatchEvent(evt);
        
    }); 
  

JS;

$this->registerJs($js, \yii\web\View::POS_READY);
