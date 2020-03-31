<?php

use modules\offer\src\entities\offerStatusLog\search\OfferStatusLogSearch;
use modules\offer\src\grid\columns\OfferStatusActionColumn;
use modules\offer\src\grid\columns\OfferStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel OfferStatusLogSearch */

?>

<div class="offer-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [
            [
                'attribute' => 'osl_id',
                'options' => ['style' => 'width:80px'],
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
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'osl_description',
                'format' => 'ntext',
                'options' => ['style' => 'width:280px'],
            ],
            [
                'class' => OfferStatusActionColumn::class,
                'attribute' => 'osl_action_id'
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'ownerUser',
                'attribute' => 'osl_owner_user_id',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'createdUser',
                'attribute' => 'osl_created_user_id',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>
