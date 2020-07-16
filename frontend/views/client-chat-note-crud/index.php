<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatNote\entity\ClientChatNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-note-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Note', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            'ccn_id',
            'ccn_chat_id',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccn_user_id',
                'relation' => 'user',
            ],
            'ccn_note:ntext',
            'ccn_deleted:boolean',
            [
				'class' => DateTimeColumn::class,
				'attribute' => 'ccn_created_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ccn_updated_dt',
				'format' => 'byUserDateTime'
			],

            ['class' => ActionColumn::class],
        ],
    ]); ?>


</div>
