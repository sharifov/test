<?php

use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\ClientChat */

$this->title = $model->cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cch_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cch_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'cch_id',
                'cch_rid',
                'cch_ccr_id',
                'cch_title',
                'cch_description',
                'cch_project_id:projectName',
                'cch_dep_id:department',
                //'cch_channel_id',
                [
                    'attribute' => 'cch_channel_id',
                    'value' => static function (ClientChat $model) {
                        return $model->cch_channel_id ? Html::a(Html::encode($model->cchChannel->ccc_name), ['client-chat-channel-crud/view', 'id' => $model->cch_channel_id], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                    },
                    'format' => 'raw',
                ],
                'cch_client_id:client',
                [
                    'class' => \common\components\grid\UserSelect2Column::class,
                    'attribute' => 'cch_owner_user_id',
                    'relation' => 'cchOwnerUser',
                    'format' => 'username',
                ],
                [
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
                'cch_note',

                [
                    'attribute' => 'cch_status_id',
                    'value' => static function (ClientChat $model) {
                        return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                    },
                    'format' => 'raw',
                ],
                'cch_ip',
                'cch_ua',
                'cch_language_id',
				[
					'attribute' => 'cch_source_type_id',
					'value' => static function (ClientChat $model) {
						return $model->getSourceTypeName();
					},
					'format' => 'raw',
				],
                'cch_missed:booleanByLabel',
                'cch_created_dt:byUserDateTime',
                'cch_updated_dt:byUserDateTime',
                'cch_created_user_id:username',
                'cch_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
