<?php

use modules\rentCar\src\entity\rentCarQuote\RentCarQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \modules\rentCar\src\entity\rentCar\RentCar */

\yii\web\YiiAsset::register($this);

$searchModel = new RentCarQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['RentCarQuoteSearch']['rcq_rent_car_id'] = $model->prc_id;
$dataProviderQuotes = $searchModel->searchProduct($params);

?>
<div class="rent-car-view-search">

    <div class="row">
        <div class="col-md-12">
            <h5 title="prc_id: <?=$model->prc_id?>">
                Pick Up: (<?=Html::encode($model->prc_pick_up_code)?>)
                <?php if ($model->prc_drop_off_code) : ?>
                    Drop Off: (<?=Html::encode($model->prc_drop_off_code)?>)
                <?php endif ?>
            </h5>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'prc_pick_up_date:date',
                        'prc_drop_off_date:date',
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model->prcProduct,
                    'attributes' => [
                        'pr_market_price',
                        'pr_client_budget',
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $this->render('_view_product_quote_list', [
                'rentCar' => $model,
                'dataProviderQuotes' => $dataProviderQuotes
            ]) ?>
        </div>
    </div>
</div>