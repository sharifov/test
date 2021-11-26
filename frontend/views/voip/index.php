<?php

use yii\web\View;

/** @var $this View */

\frontend\widgets\newWebPhone\TwilioAsset::register($this);

$js = <<<JS
window.isTwilioDevicePage = true;
JS;
$this->registerJs($js, View::POS_READY);
