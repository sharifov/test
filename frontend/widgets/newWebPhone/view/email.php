<?php

use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Json;

/** @var View $this */
/** @var array $userEmails */

$sendUrl = Url::to(['/email/send']);
$userEmailsJson =  Json::encode($userEmails);

$js = <<<JS
PhoneWidgetEmail.init('{$sendUrl}', {$userEmailsJson}); 
JS;

$this->registerJs($js);
