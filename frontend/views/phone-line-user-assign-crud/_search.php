<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserAssign\entity\search\PhoneLineUserAssignSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="phone-line-user-assign-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'plus_line_id') ?>

    <?= $form->field($model, 'plus_user_id') ?>

    <?= $form->field($model, 'plus_allow_in') ?>

    <?= $form->field($model, 'plus_allow_out') ?>

    <?= $form->field($model, 'plus_uvm_id') ?>

    <?php // echo $form->field($model, 'plus_enabled') ?>

    <?php // echo $form->field($model, 'plus_settings_json') ?>

    <?php // echo $form->field($model, 'plus_created_user_id') ?>

    <?php // echo $form->field($model, 'plus_updated_user_id') ?>

    <?php // echo $form->field($model, 'plus_created_dt') ?>

    <?php // echo $form->field($model, 'plus_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
