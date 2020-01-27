<?php

use modules\offer\src\entities\offerStatusLog\search\OfferStatusLogSearch;
use modules\offer\src\grid\columns\OfferStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\DurationColumn;
use sales\yii\grid\UserColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel OfferStatusLogSearch */

$this->title = 'Status log';

?>

<div class="product-quote-status-log">

    <h1><?= Html::encode($this->title) ?></h1>

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
