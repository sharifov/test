<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat User Channels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-user-channel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat User Channel', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

			[
				'class' => UserSelect2Column::class,
				'attribute' => 'ccuc_user_id',
				'relation' => 'ccucUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],
            'ccuc_channel_id',
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ccuc_created_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'ccuc_created_user_id',
				'relation' => 'ccucCreatedUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
