<?php

use common\models\VisitorLog;
use frontend\helpers\JsonHelper;
use frontend\helpers\OutHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatMessage\entity\search\ClientChatMessageSearch;
use sales\model\clientChatNote\entity\ClientChatNote;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var ClientChat $model */
/* @var yii\web\View $this */
/* @var VisitorLog|null $visitorLog */
/* @var ClientChatMessage $messageModel */
/* @var ClientChatMessageSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var yii\data\ActiveDataProvider $dataProviderNotes */
/* @var ClientChatVisitorData|null $clientChatVisitorData */

$this->title = $model->cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chats QA', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="client-chat-view">
    <div class="row">
        <div class="col-md-4">
            <?php $room = Html::a('<span class="glyphicon glyphicon-list-alt"></span>',
                ['/client-chat-qa/room', 'rid' => $model->cch_rid],
                [
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'Room',
                ]
            ) ?>
            <h5>Chat info <?php echo $room ?></h5>
            <?php echo DetailView::widget([
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
                            return $model->cch_channel_id ?
                                Html::a(Html::encode($model->cchChannel->ccc_name),
                                    ['client-chat-channel-crud/view', 'id' => $model->cch_channel_id],
                                    ['target' => '_blank', 'data-pjax' => 0]) : '-';
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
                            return Html::tag('span', $model->getStatusName(),
                                ['class' => 'badge badge-'.$model->getStatusClass()]);
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
                    'cch_created_dt:byUserDateTime',
                    'cch_updated_dt:byUserDateTime',
                    'cch_created_user_id:username',
                    'cch_updated_user_id:username',
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <h5>Additional Data</h5>
            <?php if ($clientChatVisitorData) :?>
                <?php echo DetailView::widget([
                    'model' => $clientChatVisitorData,
                    'attributes' => [
                        'cvd_country',
                        'cvd_region',
                        'cvd_city',
                        'cvd_latitude',
                        'cvd_longitude',
                        'cvd_url',
                        'cvd_referrer',
                        'cvd_timezone',
                        'cvd_local_time'
                    ]
                ])  ?>
            <?php else: ?>
                <p>Additional Data not found</p>
            <?php endif ?>

            <h5>Leads and case</h5>
            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Case',
                        'value' => static function(ClientChat $model) {
                            $out = '<span id="chat-info-case-info">';
                            foreach ($model->cases as $case) {
                                $out .= Yii::$app->formatter->format($case, 'case') . ' ';
                            }
                            $out .= '</span>';
                            return $out;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Lead',
                        'value' => static function(ClientChat $model) {
                            $out = '<span id="chat-info-lead-info">';
                            foreach ($model->leads as $lead) {
                                $out .= Yii::$app->formatter->format($lead, 'lead') . ' ';
                            }
                            $out .= '</span>';
                            return $out;
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>

        <div class="col-md-4">
            <h5>Visitor log</h5>
            <?php if ($visitorLog) :?>
                <?= DetailView::widget([
                    'model' => $visitorLog,
                    'attributes' => [
                        'vl_project_id:projectName',
                        'vl_ga_client_id',
                        'vl_ga_user_id',
                        'vl_customer_id',
                        'vl_gclid',
                        'vl_dclid',
                        'vl_utm_source',
                        'vl_utm_medium',
                        'vl_utm_campaign',
                        'vl_utm_term',
                        'vl_utm_content',
                        'vl_referral_url:url',
                        'vl_user_agent',
                        'vl_ip_address'
                    ]
                ]) ?>
            <?php else: ?>
                <p>Visitor log not found</p>
            <?php endif ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <h5>Messages</h5><a name="messages"></a>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'message',
                        'value' => static function(ClientChatMessage $model) {
                            if (empty($model->ccm_body) || empty($model->ccm_body['msg'])) {
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

    <div class="row">
        <div class="col-md-6">
            <h5>Chat notes</h5>
            <?php echo GridView::widget([
                'dataProvider' => $dataProviderNotes,
                'filterModel' => false,
                'columns' => [
                    [
                        'attribute' => 'ccn_note',
                        'value' => static function (ClientChatNote $note) {
                            return OutHelper::formattedChatNote($note);
                        },
                        'format' => 'raw',
                    ],
                    'ccn_user_id:userName',
                    'ccn_created_dt:byUserDateTime',
                ],
            ]);  ?>
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
