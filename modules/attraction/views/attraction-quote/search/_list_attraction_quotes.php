<?php

use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataHotel array */
/* @var $index int */
/* @var $key int */

/* @var $attraction \modules\attraction\models\Attraction */

$roomDataProvider = new ArrayDataProvider([
    'allModels' => $dataHotel ?? [],
    'pagination' => [
        'pageSize' => 15,
        'pageParam' => 'qh-page' . $key
    ],
    /*'sort' => [
        'attributes' => ['ranking', 'name', 's2C'],
    ],*/
]);

//\yii\helpers\VarDumper::dump($dataHotel, 10, true); exit;
?>
<table class="table table-striped table-bordered">
    <?php //\yii\helpers\VarDumper::dump($dataHotel, 3, true); die();?>

    <?php //\yii\widgets\Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $roomDataProvider,
        'options' => [
            'tag' => 'table',
            'class' => 'table table-bordered',
        ],
        'emptyText' => '<div class="text-center">No any search results at this moment</div><br>',
        'itemView' => function ($modelRoom, $key, $index, $widget) use ($dataHotel, $attraction) {
            return $this->render('_list_attraction_item', [
                'dataRoom' => $modelRoom,
                'dataHotel' => $dataHotel,
                'index' => $index,
                'key' => $key,
                'attraction' => $attraction
            ]);
        },
        //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
        'itemOptions' => [
            //'class' => 'item',
            'tag' => false,
        ],
    ]) ?>
</table>