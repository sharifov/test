<?php

use modules\offer\src\entities\offerViewLog\search\OfferViewLogSearch;
use sales\yii\grid\DateTimeColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel OfferViewLogSearch */

?>

<div class="offer-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [
            [
                'attribute' => 'ofvwl_id',
                'options' => ['style' => 'width:80px'],
            ],
            'ofvwl_visitor_id',
            'ofvwl_ip_address',
            'ofvwl_user_agent',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ofvwl_created_dt',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
