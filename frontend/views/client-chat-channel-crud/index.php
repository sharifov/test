<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatChannel\entity\search\ClientChatChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Channels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-channel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat Channel', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'rowOptions' => static function (ClientChatChannel $model) {

            if ($model->ccc_disabled) {
                return [
                    'class' => 'danger'
                ];
            }

        },
        'columns' => [
            ['attribute' => 'ccc_id',
                'headerOptions' => ['style' => 'width:70px'],
            ],
            'ccc_project_id:projectName',
            'ccc_name',
            'ccc_frontend_name',
            'ccc_dep_id:departmentName',
            [
                'attribute' => 'ccc_ug_id',
				'value' => static function (ClientChatChannel $model) {
					return $model->cccUg ? $model->cccUg->ug_name : null;
				}
            ],
            'ccc_disabled:boolean',
            'ccc_frontend_enabled:booleanByLabel',
            'ccc_default:boolean',
            'ccc_priority',
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ccc_created_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ccc_updated_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'ccc_created_user_id',
				'relation' => 'cccCreatedUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'ccc_updated_user_id',
				'relation' => 'cccCreatedUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
