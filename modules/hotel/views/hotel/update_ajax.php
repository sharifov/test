<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\Hotel */

$title = 'Update Hotel request: ' . $model->ph_id;
$pjaxId = 'pjax-hotel-update'
?>
<div class="hotel-update-ajax">
    <div class="hotel-form">

        <script>
            pjaxOffFormSubmit('#'<?=$pjaxId?>);
        </script>
        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        // $form = ActiveForm::begin(['']);
        $form = ActiveForm::begin([
            'id' => 'hotel-update-form',
            'options' => ['data-pjax' => true],
            'action' => ['/hotel/hotel/update-ajax'],
            'method' => 'post'
        ]);
        ?>
        <?//php $form = ActiveForm::begin(); ?>

        <?//= $form->field($model, 'ph_product_id')->textInput() ?>

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

        <?//= $form->field($model, 'ph_max_price_rate')->textInput() ?>

        <?//= $form->field($model, 'ph_min_price_rate')->textInput() ?>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?php \yii\widgets\Pjax::end(); ?>

    </div>


    <?/*= $this->render('partial/_form', [
        'model' => $model,
    ])*/ ?>

</div>
