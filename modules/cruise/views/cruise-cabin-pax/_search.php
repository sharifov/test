<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabinPax\search\CruiseCabinPaxSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="cruise-cabin-pax-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'crp_id') ?>

    <?= $form->field($model, 'crp_cruise_cabin_id') ?>

    <?= $form->field($model, 'crp_type_id') ?>

    <?= $form->field($model, 'crp_age') ?>

    <?= $form->field($model, 'crp_first_name') ?>

    <?php // echo $form->field($model, 'crp_last_name') ?>

    <?php // echo $form->field($model, 'crp_dob') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
