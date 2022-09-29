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
            
            <?php
            try {
                echo FlashAlert::widget();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            ?>
            
            <div>
                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'maxlength' => true, 'placeholder' => 'Username'])->label(false) ?>
            </div>
            
            <div>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'placeholder' => 'Password'])->label(false) ?>
            </div>

            <?php if ((new AntiBruteForceService())->checkCaptchaEnable()) : ?>
                <div>
                    <?php
                    try {
                        echo $form->field($model, 'verifyCode')->widget(Captcha::class, [
                            'captchaAction' => Url::to('/site/captcha'),
                            'options' => ['autocomplete' => 'off', 'value' => ''],
                            'template' => '<div class="row"><div class="col-lg-4">{image}</div><div class="col-lg-7">{input}</div></div>',
                        ]);
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                    ?>
                </div>
            <?php endif ?>

            <div class="form-group">
                <div class="text-left"><?= $form->field($model, 'rememberMe')->checkbox() ?></div>
                <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
            </div>

            <?php
            if (SettingHelper::isEnabledAuthClients()) {
                echo Html::tag('div', Html::tag('h1', 'Or', ['style' => 'font-size: 20px;']), ['style' => 'position: relative; margin-top: 20px;']);

                $authChoice = AuthChoice::begin([
                    'baseAuthUrl' => ['site/auth'],
                    'popupMode' => true,
                    'id' => 'auth-choice',
                    'clientOptions' => [
                        'popup' => [
                            'width' => 450,
                            'height' => 750,
                        ],
                    ],
                ]);

                echo Html::beginTag('div', ['class' => 'd-flex justify-content-center flex-wrap']);
                foreach ($authChoice->getClients() as $client) {
                    $googleEnabled = SettingHelper::isEnabledGoogleAuthClient();
                    $microsoftEnabled = SettingHelper::isEnabledMicrosoftAuthClient();
                    if (($client->getName() == 'google' && $googleEnabled) || ($client->getName() == 'microsoft' && $microsoftEnabled)) {
                        try {
                            echo $authChoice->clientLink(
                                $client,
                                '<button type="button" class="login-with-btn login-with-' . $client->getName() . '-btn">Sign in with ' . $client->getTitle() . '</button>',
                                [
                                    'style' => 'margin: 0'
                                ]
                            );
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                }
                echo Html::endTag('div');
                AuthChoice::end();
            }
            ?>

            <div class="clearfix"></div>

            <div class="separator">
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
