<?php

use modules\offer\src\grid\columns\OfferColumn;
use modules\offer\src\grid\columns\OfferStatusActionColumn;
use modules\offer\src\grid\columns\OfferStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserSelect2Column;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\offer\src\entities\offerStatusLog\search\OfferStatusLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Offer Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Offer Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'osl_id',
            [
                'class' => OfferColumn::class,
                'attribute' => 'osl_offer_id',
                'relation' => 'offer',
            ],
            [
                'class' => OfferStatusColumn::class,
                'attribute' => 'osl_start_status_id',
            ],
            [
                'class' => OfferStatusColumn::class,
                'attribute' => 'osl_end_status_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'osl_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'osl_end_dt',
            ],
            [
                'class' => DurationColumn::class,
                'attribute' => 'osl_duration',
                'startAttribute' => 'osl_start_dt',
            ],
            'osl_description',
            [
                'class' => OfferStatusActionColumn::class,
                'attribute' => 'osl_action_id'
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'osl_owner_user_id',
                'relation' => 'ownerUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'osl_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
