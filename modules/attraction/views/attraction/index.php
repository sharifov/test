<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\attraction\models\search\AttractionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attractions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Attraction', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'atn_id',
            'atn_product_id',
            'atn_date_from:date',
            'atn_date_to:date',
            'atn_destination',
            'atn_destination_code',
            'atn_request_hash_key',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
