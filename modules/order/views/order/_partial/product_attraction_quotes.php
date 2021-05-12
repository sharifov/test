<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\data\ArrayDataProvider;

/**
 * @var $data array
 * @var $productId int
 * @var $this yii\web\View
 * @var $dataProvider ArrayDataProvider
 */

$dataProvider = new ArrayDataProvider([
    'allModels' => array_reverse($data),
    'pagination' => [
        'pageSize' => 1,
    ],
]);

$pjaxId = 'pjax-product-quote-list-' . $productId;
?>
<div class="attraction-view-product-quotes">
    <?php Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fa fa-folder-o"></i> Attraction Quotes
                <?php if ($dataProvider->totalCount) : ?>
                    <sup>(<?=$dataProvider->totalCount?>)</sup>
                <?php endif; ?>
            </h2>

            <ul class="nav navbar-right panel_toolbox">
                <!--<li>
                    <?/*= Html::a('<i class="fa fa-search warning"></i> Search Quotes', null, [
                        'data-url' => \yii\helpers\Url::to([
                            '/attraction/attraction-quote/search-ajax',
                            'id' => $productId
                        ]),
                        'data-hotel-id' => $productId,
                        'class' => 'btn-search-attraction-quotes'
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
                /*'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],*/
                'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                'itemView' => 'product_attraction_quote_item',
                /*'itemView' => function ($model, $key, $index, $widget) use ($attractionProduct) {
                    return $this->render('_list_product_quote', ['model' => $model, 'index' => $index, 'key' => $key, 'attractionProduct' => $attractionProduct]);
                },*/
                //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
                'itemOptions' => [
                    //'class' => 'item',
                    'tag' => false,
                ],
            ]) ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>