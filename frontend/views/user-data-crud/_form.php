<?php

use src\model\userData\entity\UserDataKey;
use src\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\userData\entity\UserData */
/* @var $form ActiveForm */
?>

<div class="user-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ud_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'ud_key')->dropDownList(UserDataKey::getList(), ['prompt' => 'Select key']) ?>

        <?= $form->field($model, 'ud_value')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
