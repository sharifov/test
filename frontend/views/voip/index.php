<?php

use frontend\widgets\newWebPhone\DeviceHash;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use yii\web\View;

/** @var $this View */

\frontend\widgets\newWebPhone\TwilioAsset::register($this);
$remoteLogsEnabled = SettingHelper::phoneDeviceLogsEnabled() ? 'true' : 'false';

$deviceHash = DeviceHash::generate();
$deviceHashKey = DeviceHash::getHashKey(Auth::id());

$js = <<<JS
window.isTwilioDevicePage = true;
window.remoteLogsEnabled = $remoteLogsEnabled;
window.deviceHash = localStorage.getItem('$deviceHashKey');
if (!window.deviceHash) {
    window.deviceHash = '$deviceHash';
    localStorage.setItem('$deviceHashKey', window.deviceHash);
}
JS;
$this->registerJs($js, View::POS_READY);
