<?php
/** @var string $qrcodeSrc */
/** @var $form \yii\widgets\ActiveForm */
/** @var $model \src\useCase\login\twoFactorAuth\forms\OtpEmailForm */
/** @var $twoFactorKeyExist bool */
/** @var \yii\web\View $this */
/** @var integer $secondsRemain */
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
<div class="form-group">
    <button data-id="resend" type="button" class="btn btn-default" style="display: none;">Resend Code</button>
    <span data-id="countdown">
        <span data-id="title">Resend after: </span>
        <span data-id="time"><?= $secondsRemain ?></span>
    </span>
</div>
<div>
    <?= $form->field($model, 'secretKey', ['template' => '{input}{error}'])
        ->textInput(['autofocus' => true, 'maxlength' => true, 'placeholder' => '', 'autocomplete' => 'off']) ?>
</div>

<?php

$js = <<<JS
    var countDownEl = document.querySelector('[data-id="countdown"]');
    var timeEl = countDownEl.querySelector('[data-id="time"]');
    var btnEl = document.querySelector('[data-id="resend"]');
    var countDown = function (time) {
        timeEl.textContent = time;
        if (--time > 0) {
            setTimeout(function () {
                countDown(time);
            }, 1000);
        } else {
            $(countDownEl).fadeOut(150, function () {
                $(btnEl).fadeIn();
            });
        }
    };
    countDown($secondsRemain);
    $(btnEl).on('click', function () {
        $.pjax.reload({
            container: '#stepTwoPjax',
            type: 'post',
            data: $('#step-two-form').serialize(),
            timeout: 10000,
            push: false, 
            replace: false
        }); 
    });
JS;
$this->registerJs($js);
