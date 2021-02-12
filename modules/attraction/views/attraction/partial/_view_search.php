<?php

use modules\attraction\models\search\AttractionQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\Attraction */
///* @var $dataProviderQuotes \yii\data\ActiveDataProvider */


\yii\web\YiiAsset::register($this);


$searchModel = new AttractionQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['AttractionQuoteSearch']['atnq_attraction_id'] = $model->atn_id;
$dataProviderQuotes = $searchModel->searchProduct($params);
?>

<div class="attraction-view-search">
    <div class="row">
        <div class="col-md-12">
            <h5 title="atn_id: <?= $model->atn_id?>"> Destination:  (<?=Html::encode($model->atn_destination_code)?>)  <?=Html::encode($model->atn_destination)?></h5>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'atn_date_from:date',
                        'atn_date_to:date',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $this->render('_view_product_quote_list', [
                'attractionProduct' => $model,
                'dataProviderQuotes' => $dataProviderQuotes
            ]) ?>
        </div>
    </div>
</div>
