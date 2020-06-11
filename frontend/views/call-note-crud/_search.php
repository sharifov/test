<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callNote\entity\search\CallNoteSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="call-note-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cn_id') ?>

    <?= $form->field($model, 'cn_call_id') ?>

    <?= $form->field($model, 'cn_note') ?>

    <?= $form->field($model, 'cn_created_dt') ?>

    <?= $form->field($model, 'cn_updated_dt') ?>

    <?php // echo $form->field($model, 'cn_created_user_id') ?>

    <?php // echo $form->field($model, 'cn_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
