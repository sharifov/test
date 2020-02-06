<?php

use modules\offer\src\grid\columns\OfferColumn;
use sales\yii\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\offer\src\entities\offerViewLog\search\OfferViewLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Offer View Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-view-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Offer View Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ofvwl_id',
                'options' => ['style' => 'width:80px'],
            ],
            [
                'class' => OfferColumn::class,
                'attribute' => 'ofvwl_offer_id',
                'relation' => 'offer',
            ],
            'ofvwl_visitor_id',
            'ofvwl_ip_address',
            'ofvwl_user_agent',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ofvwl_created_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
