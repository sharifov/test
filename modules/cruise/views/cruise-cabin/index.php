<?php

use modules\cruise\src\entity\cruiseCabin\search\CruiseCabinSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel CruiseCabinSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cruise Cabins';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cruise-cabin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cruise Cabin', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-cruise-cabin']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'crc_id',
            'crc_cruise_id',
            'crc_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
