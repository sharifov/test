<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\attraction\models\AttractionPaxSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attraction Paxes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-pax-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Attraction Pax', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'atnp_id',
            'atnp_atn_id',
            'atnp_type_id',
            'atnp_age',
            'atnp_first_name',
            'atnp_last_name',
            'atnp_dob',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
