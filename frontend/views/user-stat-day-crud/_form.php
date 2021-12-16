<?php

use sales\model\userStatDay\entity\UserStatDayKey;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userStatDay\entity\UserStatDay */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-stat-day-form">

    <div class="col-md-3">
      <?php $form = ActiveForm::begin(); ?>

      <?= $form->field($model, 'usd_key')->dropDownList(UserStatDayKey::getList(), ['prompt' => '--']) ?>

      <?= $form->field($model, 'usd_value')->textInput(['maxlength' => true]) ?>

      <?= $form->field($model, 'usd_user_id')->widget(\sales\widgets\UserSelect2Widget::class, [
          'data' => $model->usd_user_id ? [
              $model->usd_user_id => $model->user->username
          ] : [],
      ]) ?>

      <?= $form->field($model, 'usd_day')->textInput() ?>

      <?= $form->field($model, 'usd_month')->textInput() ?>

      <?= $form->field($model, 'usd_year')->textInput() ?>

      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>

      <?php ActiveForm::end(); ?>
    </div>

</div>
