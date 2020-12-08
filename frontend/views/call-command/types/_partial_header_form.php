<?php

use sales\model\call\entity\callCommand\types\CommandTypeInterface;
use sales\model\call\services\CallCommandTypeService;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var CommandTypeInterface $model */

?>

<?php
    $callCommandTypeService = new CallCommandTypeService($model->getTypeId());

    $infoLink = '';
    $typeName = $callCommandTypeService->getTypeName();
    $sortValue = $model->getSort();

    $result = '<h5 class="head_sub_type">Command <strong class="head_sort">';

if (is_numeric($sortValue)) {
    $result .= $sortValue;
}
    $result .= '</strong>';
    $result .= ' : <strong>' . $typeName . '</strong> ';

if (method_exists($model, 'getDocUrl')) {
    $result .= '<sup>' .
        Html::a(
            '<i class="fa fa-info-circle"></i>',
            $model->getDocUrl(),
            ['target' => '_blank', 'data-pjax' => 0, 'class' => 'info_link']
        )
    . '</sup>';
}

    $result .= '</h5>';

    echo $result;






