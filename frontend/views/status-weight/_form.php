<?php

use common\models\Lead;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StatusWeight */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="status-weight-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'sw_status_id')->dropDownList(Lead::STATUS_LIST, ['prompt' => ''])?>
    <?php endif;?>

    <?= $form->field($model, 'sw_weight')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
