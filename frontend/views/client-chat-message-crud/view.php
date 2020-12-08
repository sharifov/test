<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatMessage\entity\ClientChatMessage */

$this->title = $model->ccm_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ccm_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ccm_id], [
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
            'ccm_id',
            'ccm_rid',
            'ccm_cch_id',
            'ccm_client_id',
            'ccm_user_id',
            'ccm_sent_dt',
            [
                'attribute' => 'files',
                'value' => function (\sales\model\clientChatMessage\entity\ClientChatMessage $model) {
                    $view = "";
                    if (array_key_exists('attachments', $model->ccm_body)) {
                        foreach ($model->ccm_body['attachments'] as $attachment) {
                            if (array_key_exists('title_link', $attachment) && array_key_exists('title', $attachment)) {
                                $view = $view . HTML::a($attachment['title'], "/client-chat-message-crud/download?url=" . base64_encode($attachment['title_link']), ['target' => '_blank']) . ", ";
                            }
                            if (array_key_exists('image_url', $attachment)) {
                                $view .= Html::a($attachment['image_url'], $attachment['image_url'], ['target' => '_blank']) . ', ';
                            }
                        }
                    }

                    return $view;
                },
                'format' => 'raw',
            ],
//            'ccm_body',
            [
                'attribute' => 'ccm_body',
                'value' => function (\sales\model\clientChatMessage\entity\ClientChatMessage $model) {
                    return json_encode($model->ccm_body);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'ccm_has_attachment',
                'value' => function (\sales\model\clientChatMessage\entity\ClientChatMessage $model) {
                    if ($model->ccm_has_attachment > 0) {
                        return "Yes";
                    }
                    return "No";
                },
                'format' => 'raw',
            ],
            'byType:ntext:Type'
        ],
    ]) ?>

</div>
