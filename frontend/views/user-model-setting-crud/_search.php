<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userModelSetting\entity\UserModelSettingSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-model-setting-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ums_id') ?>

    <?= $form->field($model, 'ums_user_id') ?>

    <?= $form->field($model, 'ums_key') ?>

    <?= $form->field($model, 'ums_type') ?>

    <?= $form->field($model, 'ums_class') ?>

    <?php // echo $form->field($model, 'ums_settings_json') ?>

    <?php // echo $form->field($model, 'ums_sort_order_json') ?>

    <?php // echo $form->field($model, 'ums_per_page') ?>

    <?php // echo $form->field($model, 'ums_enabled') ?>

    <?php // echo $form->field($model, 'ums_created_dt') ?>

    <?php // echo $form->field($model, 'ums_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
