<?php

use sales\model\clientChatCase\entity\ClientChatCase;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ClientChatCase */

$this->title = 'Client Chat Case';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-Case-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cccs_chat_id' => $model->cccs_chat_id, 'cccs_case_id' => $model->cccs_case_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cccs_chat_id' => $model->cccs_chat_id, 'cccs_case_id' => $model->cccs_case_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'cccs_chat_id',
                        'format' => 'clientChat',
                        'value' => static function (ClientChatCase $model) {
                            return $model->chat;
                        }
                    ],
                    [
                        'attribute' => 'cccs_case_id',
                        'format' => 'Case',
                        'value' => static function (ClientChatCase $model) {
                            return $model->case;
                        }
                    ],
                    'cccs_created_dt:byUserDateTime'
                ],
            ]) ?>
        </div>
    </div>

</div>
