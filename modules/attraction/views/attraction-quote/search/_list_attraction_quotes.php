<?php

use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataAttraction array */
/* @var $index int */
/* @var $key int */

/* @var $attraction \modules\attraction\models\Attraction */

$dataProvider = new ArrayDataProvider([
    'allModels' => $dataAttraction ?? [],
    'pagination' => [
        'pageSize' => 15,
        'pageParam' => 'qh-page' . $key
    ],
    /*'sort' => [
        'attributes' => ['ranking', 'name', 's2C'],
    ],*/
]);

?>
<table class="table table-striped table-bordered">
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
            'tag' => 'table',
            'class' => 'table table-bordered',
        ],
        'emptyText' => '<div class="text-center">No any search results at this moment</div><br>',
        'itemView' => function ($model, $key, $index, $widget) use ($dataAttraction, $attraction) {
            return $this->render('_list_attraction_item', [
                //'model' => $model,
                'dataAttraction' => $dataAttraction,
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