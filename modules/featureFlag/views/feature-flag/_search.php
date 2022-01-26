<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\featureFlag\src\entities\search\FeatureFlagSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="feature-flag-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ff_id') ?>

    <?= $form->field($model, 'ff_key') ?>

    <?= $form->field($model, 'ff_name') ?>

    <?= $form->field($model, 'ff_type') ?>

    <?= $form->field($model, 'ff_value') ?>

    <?php // echo $form->field($model, 'ff_category') ?>

    <?php // echo $form->field($model, 'ff_description') ?>

    <?php // echo $form->field($model, 'ff_enable_type') ?>

    <?php // echo $form->field($model, 'ff_attributes') ?>

    <?php // echo $form->field($model, 'ff_condition') ?>

    <?php // echo $form->field($model, 'ff_updated_dt') ?>

    <?php // echo $form->field($model, 'ff_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
