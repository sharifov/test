<?php

use sales\model\clientChat\entity\actionReason\ClientChatActionReason;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\actionReason\ClientChatActionReason */

$this->title = $model->ccar_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Action Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-action-reason-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccar_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccar_id], [
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
                'ccar_id',
                [
                    'attribute' => 'ccar_action_id',
                    'value' => static function (ClientChatActionReason $model) {
                        return ClientChatStatusLog::getActionLabel($model->ccar_action_id);
                    },
                    'format' => 'raw',
                ],
                'ccar_key',
                'ccar_name',
                'ccar_enabled:booleanByLabel',
                'ccar_comment_required:booleanByLabel',
                'ccar_created_user_id:username',
                'ccar_updated_user_id:username',
                'ccar_created_dt:byUserDateTime',
                'ccar_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
