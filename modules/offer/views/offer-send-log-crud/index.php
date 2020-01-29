<?php

use modules\offer\src\grid\columns\OfferColumn;
use modules\offer\src\grid\columns\OfferSendLogTypeColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\offer\src\entities\offerSendLog\search\OfferSendLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Offer Send Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-send-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Offer Send Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ofsndl_id',
            [
                'class' => OfferColumn::class,
                'attribute' => 'ofsndl_offer_id',
                'relation' => 'offer',
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
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
