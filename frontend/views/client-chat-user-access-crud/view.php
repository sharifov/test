<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserAccess\entity\ClientChatUserAccess */

$this->title = $model->ccua_cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-user-access-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'ccua_cch_id' => $model->ccua_cch_id, 'ccua_user_id' => $model->ccua_user_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'ccua_cch_id' => $model->ccua_cch_id, 'ccua_user_id' => $model->ccua_user_id], [
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
                'ccua_cch_id',
                'ccua_user_id:username',
                //'ccua_status_id',
                [
                    'attribute' => 'ccua_status_id',
                    'value' => static function (ClientChatUserAccess $model) {
                        return $model->ccua_status_id ?  Html::tag('span', ClientChatUserAccess::STATUS_LIST[$model->ccua_status_id], ['class' => 'badge badge-'.ClientChatUserAccess::STATUS_CLASS_LIST[$model->ccua_status_id]]) : null;
                    },
                    'format' => 'raw',
                ],
                'ccua_created_dt:byUserDateTime',
                'ccua_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
