<?php

use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Json;

/** @var View $this */
/** @var array $userPhones */

$listUrl = Url::to(['/sms/list-ajax']);
$sendUrl = Url::to(['/sms/send']);
$userPhonesJson =  Json::encode($userPhones);

$js = <<<JS
PhoneWidgetSms.init('{$listUrl}', '{$sendUrl}', {$userPhonesJson});
JS;

$this->registerJs($js);
