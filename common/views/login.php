<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\LoginForm;

?>
<div class="login-wrapper">
    <div class="site-login panel panel-primary">
        <div class="panel-heading">
            <h3>Login</h3>
        </div>
        <div class="panel-body">
            <p class="mb-20">Please fill out the following fields to login:</p>
            <?php foreach (Yii::$app->session->getAllFlashes(true) as $key => $message) : ?>
                <div id="alerts" class="alert alert-<?= $key ?>">
                    <?php if ($key == 'danger') : ?>
                        <i class="fa fa-exclamation-triangle"></i>
                    <?php endif; ?>
                    <div><?= $message ?></div>
                </div>
            <?php endforeach; ?>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>