<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\LoginForm;

?>

    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">

                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <h1>Login Form</h1>
                <?php /*<div>
                    <?=$form->errorSummary($model); ?>
                </div>*/ ?>
                <div>
                    <?= $form->field($model, 'username', ['template' => '{input}{error}'])->textInput(['autofocus' => true, 'maxlength' => true, 'placeholder' => 'Username']) ?>
                    <?php /*<input type="text" class="form-control" placeholder="Username" required="" />*/ ?>
                </div>
                <div>
                    <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput(['maxlength' => true, 'placeholder' => 'Password']) ?>
                    <?php /*<input type="password" class="form-control" placeholder="Password" required="" />*/ ?>
                </div>
                <div class="form-group">
                    <div class="text-left"><?= $form->field($model, 'rememberMe')->checkbox() ?></div>
                    <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>

                </div>

                <?php /*<div>
                    <a class="btn btn-default submit" href="index.html">Log in</a>
                    <a class="reset_pass" href="#">Lost your password?</a>
                </div>*/
                ?>

                <div class="clearfix"></div>

                <div class="separator">
                     <?php /*<p class="change_link">New to site?
                        <a href="#signup" class="to_register"> Create Account </a>
                    </p>*/ ?>

                    <div class="clearfix"></div>
                    <br />

                    <div>
                        <h1><i class="fa fa-dollar"></i>ales</h1>
                        <p>©2017-<?=date('Y')?> All Rights Reserved.</p>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </section>
        </div>

    </div>

<?php /*
<div class="login-wrapper">
    <div class="site-login panel panel-primary">
        <div class="card-header">
            <h3>Login</h3>
        </div>
        <div class="card-body">
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
</div>*/ ?>