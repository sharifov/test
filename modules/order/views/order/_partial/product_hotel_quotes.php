<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\widgets\ListView;
use yii\data\ArrayDataProvider;

/**
 * @var $data array
 * @var $productId int
 */

$dataProvider = new ArrayDataProvider([
    'allModels' => array_reverse($data),
    'pagination' => [
        'pageSize' => 1,
    ],
]);


//\yii\web\YiiAsset::register($this);

//$searchModel = new HotelRoomSearch();
//$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$pjaxId = 'pjax-product-quote-list-' . $productId;
?>
<div class="hotel-view-product-quotes">
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fa fa-folder-o"></i> Hotel Quotes
                <?php if ($dataProvider->totalCount) : ?>
                    <sup>(<?=$dataProvider->totalCount?>)</sup>
                <?php endif; ?>
            </h2>

            <ul class="nav navbar-right panel_toolbox">
                <!--<li>
                    <?/*= Html::a('<i class="fa fa-search warning"></i> Search Quotes', null, [
                        'data-url' => \yii\helpers\Url::to([
                            '/hotel/hotel-quote/search-ajax',
                            'id' => $productId
                        ]),
                        'data-hotel-id' => $productId,
                        'class' => 'btn-search-hotel-quotes'
                    ]) */?>
                </li>-->

                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => 'product_hotel_quote_item',
                /*'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],*/
                'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                /*'itemView' => function ($model, $key, $index, $widget) use ($hotelProduct) {
                    return $this->render('product_hotel_quote_item', ['model' => $model, 'index' => $index, 'key' => $key, 'hotelProduct' => $hotelProduct]);
                },*/
                //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
                'itemOptions' => [
                    //'class' => 'item',
                    'tag' => false,
                ],
            ]) ?>
        </div>
    </div>




    <?php \yii\widgets\Pjax::end(); ?>
</div>