<?php

use common\models\Airports;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuoteSearch;
use modules\rentCar\src\helpers\RentCarHelper;
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
                        [
                            'attribute' => 'prc_pick_up_date',
                            'value' => static function (RentCar $rentCar) {
                                return Yii::$app->formatter->asDate($rentCar->prc_pick_up_date) . ' / ' .
                                    Yii::$app->formatter->asTime($rentCar->prc_pick_up_time);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'prc_pick_up_code',
                            'value' => static function (RentCar $rentCar) {
                                if (!$rentCar->prc_pick_up_code) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                return RentCarHelper::locationByIata($rentCar->prc_pick_up_code);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'prc_drop_off_date',
                            'value' => static function (RentCar $rentCar) {
                                return Yii::$app->formatter->asDate($rentCar->prc_drop_off_date) . ' / ' .
                                    Yii::$app->formatter->asTime($rentCar->prc_drop_off_time);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'prc_drop_off_code',
                            'value' => static function (RentCar $rentCar) {
                                $dropOffCode = $rentCar->prc_drop_off_code ?: $rentCar->prc_pick_up_code;
                                if (!$dropOffCode) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                return RentCarHelper::locationByIata($dropOffCode);
                            },
                            'format' => 'raw',
                        ],
                    ],
                ]) ?>
            </div>
            <?php if ($model->prcProduct->pr_market_price || $model->prcProduct->pr_client_budget) : ?>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model->prcProduct,
                    'attributes' => [
                        'pr_market_price',
                        'pr_client_budget',
                    ],
                ]) ?>
            </div>
            <?php endif ?>
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