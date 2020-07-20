<?php

use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\ClientChat */

$this->title = $model->cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chats QA', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="client-chat-view">

    <div class="col-md-4">

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
                    'attribute' => 'cchCase',
                    'format' => 'case'
                ],

                [
                    'label' => 'Lead',
                    'attribute' => 'cchLead',
                    'format' => 'lead'
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
                'cch_created_dt:byUserDateTime',
                'cch_updated_dt:byUserDateTime',
                'cch_created_user_id:username',
                'cch_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
