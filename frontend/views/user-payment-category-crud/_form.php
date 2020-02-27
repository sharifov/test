<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\paymentCategory\UserPaymentCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-payment-category-form">

    <?php Pjax::begin(); ?>

    <?php $form = ActiveForm::begin([
            'options' => [
                    'data-pjax' => 1
            ]
    ]); ?>

    <div class="row">
        <div class="col-md-3">
			<?= $form->errorSummary($model) ?>

			<?= $form->field($model, 'upc_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'upc_description')->textarea(['maxlength' => true]) ?>

            <?= $form->field($model, 'upc_enabled')->checkbox() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

</div>
