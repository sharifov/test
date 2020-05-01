<?php

use common\models\ClientEmail;
use common\models\ClientPhone;
use frontend\models\form\ContactForm;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\Html;
use \yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

/* @var yii\web\View $this */
/* @var common\models\Client $model */
/* @var ContactForm $contactForm */

$this->title = 'Create Contact';
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];

$this->registerCss('
    .multiple-input-box .table th, .table td {
       padding: 0 2px 5px 0;       
    }
    .multiple-input-box .multiple-input-list__btn {
        margin-left: 5px;
    }
    .multiple-input-box .table.multiple-input-list tr > th {
        border: 0;
    }   
'
);

?>

<div class="contact-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-12 col-sm-12">

        <?php $form = ActiveForm::begin([
            'id' => $contactForm->formName() . '-form',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'validationUrl' =>['/contacts/validate-contact'],
            'action' => ['/contacts/create']
        ]) ?>

        <div class="x_panel">
            <div class="col-md-3">

                    <?= $form->field($contactForm, 'is_company')->checkbox(['class' => 'is_company']) ?>
                <div class="user_elements" <?php echo $contactForm->is_company === 1 ? 'style="display: none;"' : '' ?> >
                    <?= $form->field($contactForm, 'first_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
                    <?= $form->field($contactForm, 'middle_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
                    <?= $form->field($contactForm, 'last_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
                </div>

                <div class="company_elements" <?php echo $contactForm->is_company !== 1 ? 'style="display: none;"' : '' ?> >
                    <?= $form->field($contactForm, 'company_name')->textInput(['maxlength' => true, 'style' => 'width: 320px']) ?>
                </div>

                <?= $form->field($contactForm, 'description')->textarea(['rows' => 4, 'style' => 'width: 320px', 'class' => 'form-control']) ?>
                <?= $form->field($contactForm, 'is_public')->checkbox() ?>
                <?= $form->field($contactForm, 'disabled')->checkbox() ?>

                <?php echo Html::checkbox('ucl_favorite', 0,
                    ['id' => 'ucl_favorite', ]) ?> Favorite

            </div>

            <div class="col-md-4 multiple-input-box" id="create-contact-email">
                <?= $form->field($contactForm, 'emails')->widget(MultipleInput::class, [
                    'max' => 10,
                    'enableError' => true,
                    'columns' => [
                        [
                            'title' => 'Email',
                            'name' => 'email',
                        ],
                        [
                            'title' => 'Title',
                            'name' => 'ce_title',
                        ],
                        [
                            'name' => 'help',
                            'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT
                        ],
                    ]
                ])->label(false) ?>
            </div>

            <div class="col-md-4 multiple-input-box" id="create-contact-phone">
                <?= $form->field($contactForm, 'phones')->widget(MultipleInput::class, [
                    'max' => 10,
                    'enableError' => true,
                    'columns' => [
                        [
                            'title' => 'Phone',
                            'name' => 'phone',
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
                            'title' => 'Title',
                            'name' => 'cp_title',
                        ],
                        [
                            'name' => 'help',
                            'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT
                        ],
                    ]
                ])->label(false) ?>
            </div>

        </div>

        <div class="form-group" style="margin-top: 12px;">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$js =<<<JS

    $(document).on('click', '.is_company', function(e) {                
        let isCompany = $(this).prop("checked") ? 1 : 0;
        
        if (isCompany === 1) {
            $('.user_elements').hide();
            $('.company_elements').show();
        } else {
            $('.user_elements').show();
            $('.company_elements').hide();
        }
    });    
JS;
$this->registerJs($js);
?>


