<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CasesCategorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'cc_key') ?>

    <?= $form->field($model, 'cc_name') ?>

    <?= $form->field($model, 'cc_dep_id') ?>

    <?= $form->field($model, 'cc_system') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
