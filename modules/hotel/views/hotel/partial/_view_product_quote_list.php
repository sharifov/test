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
    <?php \yii\widgets\Pjax::end(); ?>
</div>