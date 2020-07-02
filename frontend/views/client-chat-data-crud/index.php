<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatData\entity\search\ClientChatDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ccd_cch_id',
            'ccd_country',
            'ccd_region',
            'ccd_city',
            'ccd_latitude',
            'ccd_longitude',
            'ccd_url:url',
            'ccd_title',
            'ccd_referrer',
            'ccd_timezone',
            'ccd_local_time',
			[
				'class' => \common\components\grid\DateTimeColumn::class,
				'attribute' => 'ccd_created_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => \common\components\grid\DateTimeColumn::class,
				'attribute' => 'ccd_updated_dt',
				'format' => 'byUserDateTime'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
