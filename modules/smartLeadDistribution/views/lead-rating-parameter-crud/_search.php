<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\smartLeadDistribution\src\entities\LeadRatingParameterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-rating-parameter-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lrp_id') ?>

    <?= $form->field($model, 'lrp_name') ?>

    <?= $form->field($model, 'lrp_lrc_id') ?>

    <?= $form->field($model, 'lrp_point') ?>

    <?= $form->field($model, 'lrp_rules_json') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
