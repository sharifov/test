<?php

use modules\order\src\entities\orderStatusLog\search\OrderStatusLogSearch;
use modules\order\src\grid\columns\OrderStatusActionColumn;
use modules\order\src\grid\columns\OrderStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel OrderStatusLogSearch */

?>

<div class="order-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [
            [
                'attribute' => 'orsl_id',
                'options' => ['style' => 'width:80px'],
            ],
            [
                'class' => OrderStatusColumn::class,
                'attribute' => 'orsl_start_status_id',
            ],
            [
                'class' => OrderStatusColumn::class,
                'attribute' => 'orsl_end_status_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'orsl_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'orsl_end_dt',
            ],
            [
                'class' => DurationColumn::class,
                'attribute' => 'orsl_duration',
                'startAttribute' => 'orsl_start_dt',
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'orsl_description',
                'format' => 'ntext',
                'options' => ['style' => 'width:280px'],
            ],
            [
                'class' => OrderStatusActionColumn::class,
                'attribute' => 'orsl_action_id'
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'ownerUser',
                'attribute' => 'orsl_owner_user_id',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'createdUser',
                'attribute' => 'orsl_created_user_id',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>
