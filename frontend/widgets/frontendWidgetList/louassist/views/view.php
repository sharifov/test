<?php

use frontend\helpers\JsonHelper;
use yii\web\View;

/** @var array $params */
/** @var int $userId */
/** @var View $this */
/** @var string $identify */
?>

<?php

$paramsEncoded = JsonHelper::encode($params);

$this->registerJsFile('//run.louassist.com/v2.5.1-m?id=' . $userId, [
    'position' => \yii\web\View::POS_HEAD,
]);

$js = <<<JS
    LOU.identify('{$identify}', {$paramsEncoded})
JS;
$this->registerJs($js);
