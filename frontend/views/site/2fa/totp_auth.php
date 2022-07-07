<?php
/** @var string $qrcodeSrc */
/** @var $form \yii\widgets\ActiveForm */
/** @var $model \src\useCase\login\twoFactorAuth\forms\TotpAuthForm */
/** @var $twoFactorKeyExist bool */
?>

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
    <?= $form->field($model, 'secretKey', ['template' => '{input}{error}'])
        ->textInput(['autofocus' => true, 'maxlength' => true, 'placeholder' => '', 'autocomplete' => 'off']) ?>
</div>

<?php
$jsCode = <<<JS
    $(document).on('click', '.btn-show-re-scan', function(){
       $('#qr-box').toggle();
       return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);