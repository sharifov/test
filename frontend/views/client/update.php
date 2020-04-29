<?php

use common\models\UserContactList;
use frontend\models\form\ContactForm;
use sales\auth\Auth;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\Html;
use \yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

/* @var yii\web\View $this */
/* @var common\models\Client $model */
/* @var ContactForm $contactForm */

$this->title = 'Update Client: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="contact-update">
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

                <?php
                    /*$favorite = false;
                    if ($contactForm->id !== null && $contact = UserContactList::getUserContact(Auth::id(), $contactForm->id)) {
                        $favorite = $contact->ucl_favorite;
                    }*/
                ?>

                <?php echo Html::checkbox('ucl_favorite', 0,
                    ['id' => 'ucl_favorite', ]) ?> Favorite

            </div>

           <div class="col-md-3" id="create-lead-phone">
                <?= $form->field($contactForm, 'emails')->widget(MultipleInput::class, [
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

            <div class="col-md-3" id="create-lead-phone">
                <?= $form->field($contactForm, 'phones')->widget(MultipleInput::class, [
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
