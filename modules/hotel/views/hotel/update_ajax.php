<?php

use modules\hotel\models\forms\HotelForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model HotelForm */

$pjaxId = 'pjax-hotel-update'
?>
<div class="hotel-update-ajax">
    <div class="hotel-form">
        <script>
            pjaxOffFormSubmit('#<?=$pjaxId?>');
        </script>
        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/hotel/hotel/update-ajax', 'id' => $model->ph_id],
            'method' => 'post'
        ]);
        ?>

            <?= $form->field($model, 'ph_check_in_date')->textInput() ?>

            <?= $form->field($model, 'ph_check_out_date')->textInput() ?>

            <?= $form->field($model, 'ph_destination_code')->textInput(['maxlength' => true]) ?>

            <div class="col-md-6">
                <?= $form->field($model, 'ph_min_star_rate')->dropDownList(array_combine(range(1, 5), range(1, 5)), ['prompt' => '-']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'ph_max_star_rate')->dropDownList(array_combine(range(1, 5), range(1, 5)), ['prompt' => '-']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'ph_min_price_rate')->input('number', ['min' => 0]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'ph_max_price_rate')->input('number', ['min' => 0]) ?>
            </div>

            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>
