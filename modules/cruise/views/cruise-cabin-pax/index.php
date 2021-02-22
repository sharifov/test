<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\cruise\src\entity\cruiseCabinPax\search\CruiseCabinPaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cruise Cabin Paxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cruise-cabin-pax-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cruise Cabin Pax', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-cruise-cabin-pax']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'crp_id',
            'crp_cruise_cabin_id',
            'crp_type_id',
            'crp_age',
            'crp_first_name',
            //'crp_last_name',
            //'crp_dob',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
