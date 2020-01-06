<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightPaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Paxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-pax-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Pax', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'fp_id',
            'fp_flight_id',
            'fp_pax_id',
            'fp_pax_type',
            'fp_first_name',
            //'fp_last_name',
            //'fp_middle_name',
            //'fp_dob',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
