<?php

use sales\helpers\setting\SettingHelper;
use yii\web\View;

/** @var $this View */

\frontend\widgets\newWebPhone\TwilioAsset::register($this);
$remoteLogsEnabled = SettingHelper::phoneDeviceLogsEnabled() ? 'true' : 'false';

$js = <<<JS
window.deviceHash = 'qwertyuiop';
window.isTwilioDevicePage = true;
window.remoteLogsEnabled = $remoteLogsEnabled;
JS;
$this->registerJs($js, View::POS_READY);
