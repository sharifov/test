<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login Step Two';
?>

<div class="login_wrapper">
    <div class="animate form login_form">
        <section class="login_content">
            <?php $form = ActiveForm::begin(['id' => 'step-two-form']); ?>
                <h1><?= $this->title ?></h1>

                <div id="qr-box" style="display: <?= $twoFactorKeyExist ? 'none' : 'block' ?>;">
                    <p><?= Yii::t('user', 'Please scan this QR-code with your Google Authenticator') ?></p>

                    <img src="<?= $qrcodeSrc ?>" alt="" />
                </div>
                
                <div class="clearfix"></div><br />

                <p>
                    <?= Yii::t('user', 'Please enter the six-digit code from your Google Authenticator') ?>
                    <?php /*
                    <u class="btn-show-re-scan" style="cursor: pointer; display: <?= $twoFactorKeyExist ? 'inline' : 'none' ?>;"">
                        <?= Yii::t('user', 'or re-scan QR-code') ?>
                    </u>
                    */ ?>
                </p>
                <div>
                    <?= $form->field($model, 'secret_key', ['template' => '{input}{error}'])
                        ->textInput(['autofocus' => true, 'maxlength' => true, 'placeholder' => '', 'autocomplete' => 'off']) ?>
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
                </div>

                <div class="clearfix"></div>
                <div class="separator">
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

<?php

$jsCode = <<<JS
    $(document).on('click', '.btn-show-re-scan', function(){
       $('#qr-box').toggle();
       return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
