<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserGroup\entity\search\PhoneLineUserGroupSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="phone-line-user-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'plug_line_id') ?>

    <?= $form->field($model, 'plug_ug_id') ?>

    <?= $form->field($model, 'plug_created_dt') ?>

    <?= $form->field($model, 'plug_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
