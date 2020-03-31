<?php

use modules\offer\src\entities\offerSendLog\search\OfferSendLogSearch;
use modules\offer\src\grid\columns\OfferSendLogTypeColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel OfferSendLogSearch */

?>

<div class="offer-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [
            [
                'attribute' => 'ofsndl_id',
                'options' => ['style' => 'width:80px'],
            ],
            [
                'class' => OfferSendLogTypeColumn::class,
                'attribute' => 'ofsndl_type_id',
            ],
            [
                'attribute' => 'ofsndl_send_to',
                'format' => 'ntext',
                'options' => ['style' => 'width:280px'],
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'createdUser',
                'attribute' => 'ofsndl_created_user_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ofsndl_created_dt',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>
