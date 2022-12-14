<?php

use yii\data\ArrayDataProvider;
use yii\web\View;

/**
 * @var $attraction \modules\attraction\models\Attraction
 * @var $productKey string
 * @var $key string
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
            'class' => 'table table-bordered',
        ],
        'emptyText' => '<div class="alert alert-warning" role="alert"><div class="text-center">No availabilities at this moment.</div></div>',
        'itemView' => function ($availabilityItem, $key, $index, $widget) use ($attraction, $productKey) {
            return $this->render('_list_availabilities_item', [
                'availabilityItem' => $availabilityItem,
                'index' => $index,
                'key' => $key,
                'attraction' => $attraction,
                'productKey' => $productKey
            ]);
        },
        //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
        'layout' => '{summary}<thead class="thead-light"> <tr> <th>#</th> <th>ID</th> <th>Date</th> <th>Price</th> <th></th> </tr> </thead>{items}',
        'itemOptions' => [
            //'class' => 'item',
            'tag' => false,
        ],
    ]);
