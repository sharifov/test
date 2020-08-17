<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatUserAccess\entity\search\ClientChatUserAccessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat User Accesses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-user-access-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat User Access', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'ccua_id',
            'ccua_cch_id',
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'ccua_user_id',
				'relation' => 'ccuaUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],
            //'ccua_status_id',
            [
                'attribute' => 'ccua_status_id',
                'value' => static function (ClientChatUserAccess $model) {
                    return $model->ccua_status_id ?  Html::tag('span', ClientChatUserAccess::STATUS_LIST[$model->ccua_status_id], ['class' => 'badge badge-'.ClientChatUserAccess::STATUS_CLASS_LIST[$model->ccua_status_id]]) : null;
                },
                'format' => 'raw',
                'filter' => ClientChatUserAccess::STATUS_LIST
            ],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ccua_created_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ccua_updated_dt',
				'format' => 'byUserDateTime'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
