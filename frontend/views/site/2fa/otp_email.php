<?php
/** @var string $qrcodeSrc */
/** @var $form \yii\widgets\ActiveForm */
/** @var $model \src\useCase\login\twoFactorAuth\forms\OtpEmailForm */
/** @var $twoFactorKeyExist bool */
?>

<div class="clearfix"></div><br />

<p>
    <?= Yii::t('user', 'Please enter the six digit code sent to your email') ?>
    <?php /*
                    <u class="btn-show-re-scan" style="cursor: pointer; display: <?= $twoFactorKeyExist ? 'inline' : 'none' ?>;"">
                        <?= Yii::t('user', 'or re-scan QR-code') ?>
                    </u>
                    */ ?>
</p>
<div>
    <?= $form->field($model, 'secretKey', ['template' => '{input}{error}'])
        ->textInput(['autofocus' => true, 'maxlength' => true, 'placeholder' => '', 'autocomplete' => 'off']) ?>
</div>