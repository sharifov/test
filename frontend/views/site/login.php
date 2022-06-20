<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model LoginForm */

use frontend\themes\gentelella_v2\widgets\FlashAlert;
use src\helpers\setting\SettingHelper;
use src\services\authentication\AntiBruteForceService;
use yii\authclient\widgets\AuthChoice;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\LoginForm;
use yii\helpers\Url;

?>

    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">

                <?php $form = ActiveForm::begin(['id' => 'login-form', 'validateOnBlur' => false]); ?>
                <h1>Login Form</h1>
                <?= FlashAlert::widget() ?>
                <?php /*<div>
                    <?=$form->errorSummary($model); ?>
                </div>*/ ?>
                <div>
                    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'maxlength' => true, 'placeholder' => 'Username'])->label(false) ?>
                    <?php /*<input type="text" class="form-control" placeholder="Username" required="" />*/ ?>
                </div>
                <div>
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'placeholder' => 'Password'])->label(false) ?>
                    <?php /*<input type="password" class="form-control" placeholder="Password" required="" />*/ ?>
                </div>

                <?php if ((new AntiBruteForceService())->checkCaptchaEnable()) : ?>
                    <div>
                        <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                            'captchaAction' => Url::to('/site/captcha'),
                            'options' => ['autocomplete' => 'off', 'value' => ''],
                            'template' => '<div class="row"><div class="col-lg-4">{image}</div><div class="col-lg-7">{input}</div></div>',
                        ]) ?>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <div class="text-left"><?= $form->field($model, 'rememberMe')->checkbox() ?></div>
                    <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
                </div>

                <?php if (SettingHelper::isEnabledAuthClients()) : ?>
                <div style="position: relative; margin-top: 20px;">
                    <h1 style="font-size: 20px;">Or</h1>
                </div>

                    <?php $authChoice = AuthChoice::begin([
                        'baseAuthUrl' => ['site/auth'],
                        'popupMode' => true,
                        'id' => 'auth-choice',
                        'clientOptions' => [
                            'popup' => [
                                'width' => 450,
                                'height' => 750,
                            ],
                        ],
                    ]) ?>
                        <div class="d-flex justify-content-center flex-wrap" style>
                            <?php foreach ($authChoice->getClients() as $client) : ?>
                                <?php
                                $googleEnabled = SettingHelper::isEnabledGoogleAuthClient();
                                $microsoftEnabled = SettingHelper::isEnabledMicrosoftAuthClient();
                                if (($client->getName() == 'google' && $googleEnabled) || ($client->getName() == 'microsoft' && $microsoftEnabled)) : ?>
                                    <?= $authChoice->clientLink(
                                        $client,
                                        '<button type="button" class="login-with-btn login-with-' . $client->getName() . '-btn">Sign in with ' . $client->getTitle() . '</button>',
                                        [
                                            'style' => 'margin: 0'
                                        ]
                                    ) ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php AuthChoice::end() ?>
                <?php endif; ?>

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
                        <h1>CRM - Sales!</h1>
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