<?= yii\helpers\Html::tag('p', '2017 - 2022 © All Rights Reserved', ['class' => 'copyright-block']); ?>

<?php
$css = <<<CSS
.copyright-block {
    display: block;
    width: 100%;
    margin: 35px auto 0 auto;
    padding-top: 30px;
    border-top: 1px dotted #c3c3c3;
    text-align: center;
}

CSS;

$this->registerCss($css);
