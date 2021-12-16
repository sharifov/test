<?php

use frontend\widgets\newWebPhone\DeviceStorageKey;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use yii\web\View;

/** @var $this View */

\frontend\widgets\newWebPhone\DeviceAsset::register($this);
$phoneDeviceRemoteLogsEnabled = SettingHelper::phoneDeviceLogsEnabled() ? 'true' : 'false';

$phoneDeviceIdStorageKey = DeviceStorageKey::getphoneDeviceIdStorageKey(Auth::id());

$js = <<<JS
window.isTwilioDevicePage = true;
window.phoneDeviceIdStorageKey = '$phoneDeviceIdStorageKey';
window.phoneDeviceRemoteLogsEnabled = $phoneDeviceRemoteLogsEnabled;
JS;
$this->registerJs($js, View::POS_READY);
