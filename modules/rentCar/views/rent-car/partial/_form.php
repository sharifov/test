<?php

use modules\rentCar\src\entity\rentCar\RentCar;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model RentCar */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-form">

    <?php $form = ActiveForm::begin(); ?>


        <?= $form->field($model, 'prc_product_id')->textInput() ?>

        <?= $form->field($model, 'prc_pick_up_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'prc_drop_off_code')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'prc_pick_up_date')->widget(
    \dosamigos\datepicker\DatePicker::class,
    [
            'inline' => false,
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayBtn' => true
            ]
            ]
)?>


<?= $form->field($model, 'prc_drop_off_date')->widget(
    \dosamigos\datepicker\DatePicker::class,
    [
            'inline' => false,
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayBtn' => true
            ]
            ]
)?>

        <?= $form->field($model, 'prc_pick_up_time')->textInput() ?>

        <?= $form->field($model, 'prc_drop_off_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
