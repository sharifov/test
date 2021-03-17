<?php

use yii\data\ArrayDataProvider;
use yii\web\View;

/**
 * @var $attraction \modules\attraction\models\Attraction
 */

$availabilitiesDataProvider = new ArrayDataProvider([
    'allModels' => $dataAvailabilities ?? [],
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

    <?php //\yii\widgets\Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>
    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $availabilitiesDataProvider,
        'options' => [
            'tag' => 'table',
            'class' => 'table table-striped table-bordered',
        ],
        'emptyText' => '<div class="text-center">No availabilities at this moment</div><br>',
        'itemView' => function ($availabilityItem, $key, $index, $widget) use ($attraction) {
            //\yii\helpers\VarDumper::dump($availabilityItem, 10, true); exit;
            return $this->render('_list_availabilities_item', ['availabilityItem' => $availabilityItem, 'index' => $index, 'key' => $key, 'attraction' => $attraction]);
        },
        //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
        'layout' => '{summary}<thead class="thead-light"> <tr> <th>#</th> <th>ID</th>  <th>Date</th> <th>Travellers</th> <th>Price</th> <th></th>  </tr> </thead>{items}',
        'itemOptions' => [
            //'class' => 'item',
            'tag' => false,
        ],
    ]);
