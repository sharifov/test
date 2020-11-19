<?php

use common\models\Currency;

use dosamigos\datepicker\DatePicker;
use sales\model\clientAccount\entity\ClientAccount;
use sales\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccount\entity\ClientAccount */
/* @var $form ActiveForm */
?>

<div class="client-account-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ca_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'ca_uuid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_hid')->input('number', ['min' => 1, 'step' => 1]) ?>

        <?= $form->field($model, 'ca_username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_first_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_middle_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_last_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_nationality_country_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_dob')->widget(DatePicker::class, [
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'clearBtn' => true,
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' =>'Choose Date',
                    'readonly' => '1',
                ],
                'clientEvents' => [
                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                ],
        ]) ?>

        <?= $form->field($model, 'ca_gender')->dropDownList(ClientAccount::GENDER_LIST, ['prompt' => '--']) ?>

        <?= $form->field($model, 'ca_phone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_subscription')->checkbox() ?>

        <?= $form->field($model, 'ca_language_id')->dropDownList(\common\models\Language::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'ca_currency_code')->dropDownList(\common\models\Currency::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'ca_timezone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_created_ip')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ca_enabled')->checkbox() ?>

        <?= $form->field($model, 'ca_origin_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'ca_origin_updated_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
