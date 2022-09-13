<div class="two-factor-auth-footer-block">
    <?= yii\helpers\Html::tag('p', 'CRM - Sales!', ['class' => 'project-name-block']); ?>
    <?= yii\helpers\Html::tag('p', '2017 - 2022 © All Rights Reserved', ['class' => 'copyright-block']); ?>
</div>

<?php
$css = <<<CSS
.two-factor-auth-footer-block {
    display: block;
    width: 100%;
    border-top: 1px dashed #c3c3c3;
    padding: 25px 0 0 0;
    margin: 25px 0 0 0;
}
.project-name-block {
    margin: 0 auto;
    text-align: center;
    font-size: 18pt;
}
.copyright-block {
    margin: 20px auto 0 auto;
    text-align: center;
}

CSS;

$this->registerCss($css);
