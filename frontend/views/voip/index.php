<?php

use frontend\widgets\newWebPhone\DeviceStorageKey;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use yii\web\View;

/** @var $this View */

\frontend\widgets\newWebPhone\TwilioAsset::register($this);
$remoteLogsEnabled = SettingHelper::phoneDeviceLogsEnabled() ? 'true' : 'false';

$deviceIdStorageKey = DeviceStorageKey::getDeviceIdStorageKey(Auth::id());

$js = <<<JS
window.isTwilioDevicePage = true;
window.phoneDeviceIdStorageKey = '$deviceIdStorageKey';
window.remoteLogsEnabled = $remoteLogsEnabled;
JS;
$this->registerJs($js, View::POS_READY);
