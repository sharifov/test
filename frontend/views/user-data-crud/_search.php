<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userData\entity\search\UserDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ud_user_id') ?>

    <?= $form->field($model, 'ud_key') ?>

    <?= $form->field($model, 'ud_value') ?>

    <?= $form->field($model, 'ud_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
