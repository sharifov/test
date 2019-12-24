<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'ch_code')->dropDownList(\common\models\Currency::getList(), ['prompt' => '-']) ?>

			<?= $form->field($model, 'ch_base_rate')->input('number', ['step' => 0.00001]) ?>

			<?= $form->field($model, 'ch_app_rate')->input('number', ['step' => 0.00001]) ?>

			<?= $form->field($model, 'ch_app_percent')->input('number', ['step' => 0.001]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'ch_created_date')->textInput() ?>

            <?= $form->field($model, 'ch_main_created_dt')->textInput() ?>

            <?= $form->field($model, 'ch_main_updated_dt')->textInput() ?>

            <?= $form->field($model, 'ch_main_synch_dt')->textInput() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
