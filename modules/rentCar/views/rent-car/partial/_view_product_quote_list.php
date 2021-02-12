<?php

use modules\rentCar\src\entity\rentCar\RentCar;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $rentCar rentCar */
/* @var $dataProviderQuotes \yii\data\ActiveDataProvider */

$pjaxId = 'pjax-product-quote-list-' . $rentCar->prc_product_id;
?>
<div class="hotel-view-product-quotes">
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fa fa-folder-o"></i> Rent Car Quotes
                <?php if ($dataProviderQuotes->totalCount) : ?>
                    <sup>(<?=$dataProviderQuotes->totalCount?>)</sup>
                <?php endif; ?>
            </h2>

            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <?= Html::a('<i class="fa fa-search warning"></i> Search Rent Car', null, [
                        'data-url' => \yii\helpers\Url::to([
                            '/rent-car/rent-car-quote/search-ajax',
                            'id' => $rentCar->prc_id
                        ]),
                        'data-model-id' => $rentCar->prc_id,
                        'class' => 'btn-search-rent-car-quotes'
                    ]) ?>
                </li>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProviderQuotes,
                'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                'itemView' => function ($model, $key, $index, $widget) use ($rentCar) {
                    return $this->render('_list_product_quote', ['modelQuote' => $model, 'index' => $index, 'key' => $key, 'rentCar' => $rentCar]);
                },
                'itemOptions' => [
                    'tag' => false,
                ],
            ]) ?>
        </div>
    </div>

    <?php \yii\widgets\Pjax::end(); ?>
</div>