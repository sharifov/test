<?php

use sales\model\clientChatRequest\entity\ClientChatRequest;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatRequest\entity\search\ClientChatRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'ccr_id',
            //'ccr_event',
            [
                'attribute' => 'ccr_event',
                'value' => static function(ClientChatRequest $model) {
                    return $model->getEventName();
                },
                'filter' => ClientChatRequest::getEventList()
            ],
            'ccr_json_data:ntext',
			[
				'class' => \common\components\grid\DateTimeColumn::class,
				'attribute' => 'ccr_created_dt',
				'format' => 'byUserDateTime'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
