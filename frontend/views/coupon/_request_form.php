<?php

/* @var RequestForm $model */

use common\components\bootstrap4\activeForm\ClientBeforeSubmit;
use sales\model\coupon\useCase\request\RequestForm;
use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

?>

<div class="form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'coupon-request-form',
        'action' => ['/coupon/request'],
        'clientBeforeSubmit' => new ClientBeforeSubmit(
            'Request coupons',
            true,
            'modal-sm',
            'let cont = $("#pjax-case-coupons-table").length; if (cont) { pjaxReload({container: \'#pjax-case-coupons-table\'}) } else { pjaxReload({container: \'#pjax-case-coupons\'}) }',
            null,
            null
        ),
        'enableAjaxValidation' => false,
    ]);
    ?>

    <?= $form->field($model, 'caseId')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'count')->dropDownList(array_combine(range(1,9),range(1,9))) ?>

    <?= $form->field($model, 'code')->dropDownList(RequestForm::CODE_LIST) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Send', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
