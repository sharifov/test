<?php

use modules\product\src\entities\productQuoteStatusLog\search\ProductQuoteStatusLogSearch;
use modules\product\src\grid\columns\ProductQuoteStatusActionColumn;
use modules\product\src\grid\columns\ProductQuoteStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel ProductQuoteStatusLogSearch */

?>

<div class="product-quote-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [
            [
                'attribute' => 'pqsl_id',
                'options' => ['style' => 'width:80px'],
            ],
            [
                'class' => ProductQuoteStatusColumn::class,
                'attribute' => 'pqsl_start_status_id',
            ],
            [
                'class' => ProductQuoteStatusColumn::class,
                'attribute' => 'pqsl_end_status_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqsl_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqsl_end_dt',
            ],
            [
                'class' => DurationColumn::class,
                'attribute' => 'pqsl_duration',
                'startAttribute' => 'pqsl_start_dt',
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'pqsl_description',
                'format' => 'ntext',
                'options' => ['style' => 'width:280px'],
            ],
            [
                'class' => ProductQuoteStatusActionColumn::class,
                'attribute' => 'pqsl_action_id',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'ownerUser',
                'attribute' => 'pqsl_owner_user_id',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'createdUser',
                'attribute' => 'pqsl_created_user_id',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>
