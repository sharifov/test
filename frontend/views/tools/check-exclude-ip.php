<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $ip string */

$this->title = 'Check Exclude IP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-exclude-ip">

    <h3><?= Html::encode($this->title) ?></h3>
    <p>
    <pre><?php echo Html::encode(Yii::$app->airsearch->url) . 'airline/ip-check'; ?></pre>
    </pre>

    <?php Pjax::begin(); ?>
    <div class="row">
        <div class="col-md-2">

            <?= Html::beginForm('', 'get', [
                'data-pjax' => 1
            ]) ?>
            <?= Html::label('IP Address:') ?>

            <?= Html::input('text', 'ip', $ip, ['minlength' => 7, 'maxlength' => 50, 'style' => 'width: 100%;',
                'placeholder' => 'xxx.xxx.xxx.xxx',
                'pattern' => '^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$']) ?>
            <br><br>

            <?= Html::submitButton('Check IP', ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>

        </div>

        <div class="col-md-10">
            <?php if ($data) : ?>
                <h2>Parsing response, IP: <?= Html::encode($ip)?></h2>
                <pre><?php \yii\helpers\VarDumper::dump($data, 10, true) ?></pre>
            <?php endif; ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php
//$js = <<<JS
//    let textarea = document.querySelector('textarea');
//    textarea.addEventListener('keyup', function() {
//        if (this.scrollTop > 0) {
//            this.style.height = this.scrollHeight + 'px';
//        }
//    });
//JS;
//$this->registerJs($js);
