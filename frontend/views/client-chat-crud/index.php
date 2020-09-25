<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\clientChat\entity\ClientChat;
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
            [
                    'attribute' => 'cch_id',
                'options' => ['style' => 'width:100px']
            ],
            [
                'attribute' => 'cch_parent_id'
            ],
            'cch_rid',
            //'cch_ccr_id',
            [
                'attribute' => 'cch_ccr_id',
                'value' => static function (\sales\model\clientChat\entity\ClientChat $model) {
                    return $model->cch_ccr_id ? Html::a('<i class="fa fa-link"></i> ' . $model->cch_ccr_id, ['client-chat-request-crud/view', 'id' => $model->cch_ccr_id], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:100px']
            ],
            //'cch_title',
            //'cch_description',
            //'cch_project_id:projectName',
            [
                'attribute' => 'cch_status_id',
                'value' => static function (\sales\model\clientChat\entity\ClientChat $model) {
                    return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                },
                'format' => 'raw',
                'filter' => \sales\model\clientChat\entity\ClientChat::getStatusList()
            ],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cch_project_id',
                'relation' => 'cchProject',
            ],
            //'cch_dep_id:department',
            [
                'attribute' => 'cch_dep_id',
                'format' => 'department',
                'filter' => \common\models\Department::getList()
            ],
//            'cch_channel_id',
            [
                'attribute' => 'cch_channel_id',
                'value' => static function (\sales\model\clientChat\entity\ClientChat $model) {
                    return $model->cch_channel_id ? Html::a(Html::encode($model->cchChannel->ccc_name), ['client-chat-channel-crud/view', 'id' => $model->cch_channel_id], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
                'filter' => \sales\model\clientChatChannel\entity\ClientChatChannel::getList()
            ],
            //'cch_client_id:client',
            [
                'attribute' => 'cch_client_id',
                'value' => static function (\sales\model\clientChat\entity\ClientChat $model) {
                    return $model->cch_client_id ? Html::a('<i class="fa fa-link"></i> ' . $model->cch_client_id, ['client/view', 'id' => $model->cch_client_id], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
            ],
            //'cch_owner_user_id',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cch_owner_user_id',
                'relation' => 'cchOwnerUser',
                'format' => 'username',
                'options' => ['style' => 'width:200px']
                //'placeholder' => 'Select User'
            ],
			[
				'attribute' => 'cch_source_type_id',
				'options' => ['style' => 'width:100px'],
				'filter' => ClientChat::getSourceTypeList(),
				'value' => static function (ClientChat $model) {
					return $model->getSourceTypeName();
				}
			],
            [
                'attribute' => 'case_id',
                'label' => 'Case',
                'value' => static function (ClientChat $chat) {
                    $out = '';
                    foreach ($chat->cases as $case) {
                        $out .= Yii::$app->formatter->format($case,  'case') . ' ';
                    }
                    return $out;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'lead_id',
                'label' => 'Lead',
                'value' => static function (ClientChat $chat) {
                    $out = '';
                    foreach ($chat->leads as $lead) {
                        $out .= Yii::$app->formatter->format($lead,  'lead') . ' ';
                    }
                    return $out;
                },
                'format' => 'raw'
            ],
            //'cch_note',

            //'cch_ip',
            //'cch_ua',
            [
                'attribute' => 'cch_language_id',
                'filter' => \common\models\Language::getLanguages()
                //'format' => 'byUserDateTime'
            ],
            [
				'class' => DateTimeColumn::class,
				'attribute' => 'cch_created_dt',
				'format' => 'byUserDateTime'
            ],
//            [
//				'class' => DateTimeColumn::class,
//				'attribute' => 'cch_updated_dt',
//				'format' => 'byUserDateTime'
//            ],
//			[
//				'class' => UserSelect2Column::class,
//				'attribute' => 'cch_created_user_id',
//				'relation' => 'cchCreatedUser',
//				'format' => 'username',
//				'placeholder' => 'Select User'
//			],
//			[
//				'class' => UserSelect2Column::class,
//				'attribute' => 'cch_updated_user_id',
//				'relation' => 'cchUpdatedUser',
//				'format' => 'username',
//				'placeholder' => 'Select User'
//			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
