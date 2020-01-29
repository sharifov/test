<?php

use modules\lead\src\grid\columns\LeadColumn;
use modules\offer\src\grid\columns\OfferStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\offer\src\entities\offer\search\OfferCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Offers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Offer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'of_id',
            'of_gid',
            'of_uid',
            'of_name',
            [
                'class' => LeadColumn::class,
                'attribute' => 'of_lead_id',
                'relation' => 'ofLead',
            ],
            [
                'class' => OfferStatusColumn::class,
                'attribute' => 'of_status_id',
            ],

            'of_client_currency',
            'of_client_currency_rate',
            'of_app_total',
            'of_client_total',
            'of_profit_amount',
            [
                'class' => UserColumn::class,
                'attribute' => 'of_owner_user_id',
                'relation' => 'ofOwnerUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'of_created_user_id',
                'relation' => 'ofCreatedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'of_updated_user_id',
                'relation' => 'ofUpdatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'of_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'of_updated_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
