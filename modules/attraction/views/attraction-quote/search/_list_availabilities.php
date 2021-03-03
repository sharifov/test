<?php

/**
 * @var $dataProvider \yii\data\ArrayDataProvider
 */
//var_dump($dataProvider); die();
?>

<?= \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    /*'options' => [
            'tag' => 'table',
            'class' => 'table table-bordered',
        ],*/
    'summary' => false,
    'emptyText' => '<div class="text-center">Not found any hotels</div><br>',
    'itemView' => function ($dataAvailabilities, $key, $index, $widget) use ($attractionSearch) {
        //\yii\helpers\VarDumper::dump($dataAvailabilities, 10, true); exit;
        return $this->render('_list_availabilities_grid', ['dataAvailabilities' => $dataAvailabilities, 'index' => $index, 'key' => $key, 'attractionSearch' => $attractionSearch]);
    },
    //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
    'itemOptions' => [
        //'class' => 'item',
        'tag' => false,
    ],
]);
