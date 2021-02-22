<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\cruise\src\entity\cruise\search\CruiseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cruises';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cruise-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cruise', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-cruise']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'crs_id',
            'crs_product_id',
            'crs_departure_date_from',
            'crs_arrival_date_to',
            'crs_destination_code',
            //'crs_destination_label',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
