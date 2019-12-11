<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $hotelProduct modules\hotel\models\Hotel */
/* @var $dataProviderQuotes \yii\data\ActiveDataProvider */


//\yii\web\YiiAsset::register($this);

//$searchModel = new HotelRoomSearch();
//$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$pjaxId = 'pjax-product-quote-list-' . $hotelProduct->ph_product_id;
?>
<div class="hotel-view-product-quotes">
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fa fa-folder-o"></i> Hotel Quotes (<?=$dataProviderQuotes->totalCount?>)
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <?= Html::a('<i class="fa fa-search warning"></i> Search Quotes', null, [
                        'data-url' => \yii\helpers\Url::to([
                            '/hotel/hotel-quote/search-ajax',
                            'id' => $hotelProduct->ph_id
                        ]),
                        'data-hotel-id' => $hotelProduct->ph_id,
                        'class' => 'btn-search-hotel-quotes'
                    ]) ?>
                </li>

                <?//php if ($is_manager) : ?>
                <!--                    <li>-->
                <!--                        --><?//=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                <!--                    </li>-->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                    <div class="dropdown-menu" role="menu">
                        <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                        <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/hotel/hotel-quote/search-ajax',
                                'id' => $hotelProduct->ph_id
                            ]),
                            'data-hotel-id' => $hotelProduct->ph_id,
                            'class' => 'dropdown-item text-success btn-search-hotel-quotes'
                        ]) ?>


                    </div>
                </li>
                <?//php endif; ?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProviderQuotes,
                /*'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],*/
                'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                'itemView' => function ($model, $key, $index, $widget) use ($hotelProduct) {
                    return $this->render('_list_product_quote', ['model' => $model, 'index' => $index, 'key' => $key, 'hotelProduct' => $hotelProduct]);
                },
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