<?php

use modules\offer\src\grid\columns\OfferColumn;
use modules\offer\src\grid\columns\OfferSendLogTypeColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use src\helpers\email\MaskEmailHelper;
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

    <?php Pjax::begin(['scrollTo' => 0]); ?>
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
                'value' => static function ($model) {
                    return MaskEmailHelper::masking($model->ofsndl_send_to);
                },
                'options' => ['style' => 'width:280px'],
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ofsndl_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
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
