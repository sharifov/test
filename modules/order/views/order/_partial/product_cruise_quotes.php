<?php

use modules\cruise\src\entity\cruise\Cruise;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $cruiseProduct Cruise */

/**
 * @var $data array
 * @var $productId int
 */

$dataProvider = new ArrayDataProvider([
    'allModels' => $data,
    'pagination' => [
        'pageSize' => 1,
    ],
]);

$pjaxId = 'pjax-product-quote-list-' . $productId;
?>
<div class=cruise-view-product-quotes">
    <?php Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fa fa-folder-o"></i> Cruise Quotes
                <?php if ($dataProvider->totalCount) : ?>
                    <sup>(<?=$dataProvider->totalCount?>)</sup>
                <?php endif; ?>
            </h2>

            <ul class="nav navbar-right panel_toolbox">
                <!--<li>
                    <?/*= Html::a('<i class="fa fa-search warning"></i> Search Quotes', null, [
                        'data-url' => \yii\helpers\Url::to([
                            '/cruise/cruise-quote/search-ajax',
                            'id' => $cruiseProduct->crs_id
                        ]),
                        'data-cruise-id' => $cruiseProduct->crs_id,
                        'class' => 'btn-search-cruise-quotes'
                    ]) */?>
                </li>-->

                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,

                'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                'itemView' => 'product_cruise_quote_item',
                /*'itemView' => function ($model, $key, $index, $widget) use ($cruiseProduct) {
                    return $this->render('_list_product_quote', ['model' => $model, 'index' => $index, 'key' => $key, 'cruiseProduct' => $cruiseProduct]);
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
