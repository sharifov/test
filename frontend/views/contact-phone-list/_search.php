<?php

use src\model\contactPhoneData\service\ContactPhoneDataDictionary;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneList\entity\ContactPhoneListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-phone-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 0
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'cpl_phone_number') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'cpd_key')->dropDownList(ContactPhoneDataDictionary::KEY_LIST, ['prompt' => '---']) ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cpd_value') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
