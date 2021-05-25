<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\appProjectKey\entity\AppProjectKeySearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="app-project-key-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'apk_id') ?>

    <?= $form->field($model, 'apk_key') ?>

    <?= $form->field($model, 'apk_project_id') ?>

    <?= $form->field($model, 'apk_project_source_id') ?>

    <?= $form->field($model, 'apk_created_dt') ?>

    <?php // echo $form->field($model, 'apk_updated_dt') ?>

    <?php // echo $form->field($model, 'apk_created_user_id') ?>

    <?php // echo $form->field($model, 'apk_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>