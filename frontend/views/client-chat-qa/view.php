<?php

use frontend\helpers\JsonHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var sales\model\clientChat\entity\ClientChat $model */
/* @var ClientChatMessage $messageModel */
/* @var sales\model\clientChatMessage\entity\search\ClientChatMessageSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */


$this->title = $model->cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chats QA', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="client-chat-view">
    <div class="row">
        <div class="col-md-4">
            <h5>Chat info</h5>
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
                        'attribute' => 'cch_status_id',
                        'value' => static function (ClientChat $model) {
                            return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                        },
                        'format' => 'raw',
                    ],
                    'cch_ip',
                    [
                        'attribute' => 'cch_language_id',
                        'value' => static function (ClientChat $model) {
                            return $model->language ? $model->language->name : '<span class="not-set">(not set)</span>';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'cch_status_id',
                        'value' => static function (ClientChat $model) {
                            return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                        },
                        'format' => 'raw',
                    ],
                    'cch_created_dt:byUserDateTime',
                    'cch_updated_dt:byUserDateTime',
                    'cch_created_user_id:username',
                    'cch_updated_user_id:username',
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <h5>Additional Data</h5>
            <?= DetailView::widget([
                'model' => $model->cchData,
                'attributes' => [
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
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h5>Messages</h5>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'message',
                        'value' => static function(ClientChatMessage $model) {
                            if (!isset($model->ccm_body) || is_null($model->ccm_body) || is_null($model->ccm_body['msg'])) {
                                return '';
                            }
                            return $model->ccm_body['msg'];
                        },
                        'format' => 'raw',
                        'contentOptions' => ['style' => 'width:240px; white-space: normal;'],
                    ],
                    'ccm_client_id:client',
                    'ccm_user_id:userName',
                    'ccm_sent_dt:byUserDateTime',
                    [
                        'attribute' => 'ccm_body',
                        'value' => static function(ClientChatMessage $model) {
                            return '<pre><small>' .
                                (StringHelper::truncate(JsonHelper::encode($model->ccm_body), 240, '...', null, true)) . '</small></pre> 
                            <a href="' .
                                Url::to(['client-chat-qa/message-body-view', 'id' => $model->ccm_id]) . '" 
                                    title="Message ' . $model->ccm_id . '" class="btn btn-sm btn-success showModalButton" data-pjax="0">
                                        <i class="fas fa-eye"></i> details</a>';
                        },
                        'format' => 'raw',
                    ],
                    'ccm_has_attachment:booleanByLabel',
                    [
                        'attribute' => 'files',
                        'value' => static function(ClientChatMessage $model) {
                            $view = '';
                            if (array_key_exists('attachments', $model->ccm_body)) {
                                foreach ($model->ccm_body['attachments'] as $attachment) {
                                    $titleLink = explode('.', $attachment['title_link']);
                                    $title = '[' . StringHelper::truncate($attachment['title'], 30) . '].' . end($titleLink);
                                    $view .= Html::a($title,
                                        '/client-chat-message-crud/download?url=' . base64_encode($attachment['title_link']),
                                        ['target'=>'_blank']) . '<br /> ';
                                }
                            }
                            return $view;
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>

<?php
yii\bootstrap4\Modal::begin([
        'title' => 'Message detail',
        'id' => 'modal',
        'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
    ]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showModalButton', function(){
        $('#modal').modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#modal-title').html($(this).attr('title'));
        $.get($(this).attr('href'), function(data) {
            $('#modal .modal-body').html(data);
        });
       return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
