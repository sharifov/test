<?php

use frontend\widgets\frontendWidgetList\userflow\assets\UserFlowWidgetAsset;
//use frontend\helpers\JsonHelper;
use yii\web\View;

/** @var array $params */
/** @var View $this */
/** @var string $identify */
/** @var $scriptId */
UserFlowWidgetAsset::register($this);
//$paramsEncoded = JsonHelper::encode($params);

$js = <<<JS

userflow.init('{$token}')
userflow.identify('{$user_id}', {
  name: '{$user_name}',
  email: '{$user_email}',
  signed_up_at: '{$user_signed_up_datetime}'
})

JS;
$this->registerJs($js, View::POS_LOAD);
