<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\entity\search\ClientChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cch_id',
            'cch_rid',
            'cch_ccr_id',
            'cch_title',
            'cch_description',
            'cch_project_id:projectName',
            'cch_dep_id:department',
            'cch_channel_id',
            'cch_client_id:client',
            'cch_owner_user_id',
            'cch_case_id:case',
            'cch_lead_id:lead',
            'cch_note',
            [
                'attribute' => 'cch_status_id',
                'value' => static function (\sales\model\clientChat\entity\ClientChat $model) {
                    return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                },
                'format' => 'raw'
            ],
            'cch_ip',
            'cch_ua',
            'cch_language_id',
            [
				'class' => DateTimeColumn::class,
				'attribute' => 'cch_created_dt',
				'format' => 'byUserDateTime'
            ],
            [
				'class' => DateTimeColumn::class,
				'attribute' => 'cch_updated_dt',
				'format' => 'byUserDateTime'
            ],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'cch_created_user_id',
				'relation' => 'cchCreatedUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'cch_updated_user_id',
				'relation' => 'cchUpdatedUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
