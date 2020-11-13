<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat */

$this->title = $model->ucac_conn_id;
$this->params['breadcrumbs'][] = ['label' => 'User Connection Active Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-connection-active-chat-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'ucac_conn_id' => $model->ucac_conn_id, 'ucac_chat_id' => $model->ucac_chat_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'ucac_conn_id' => $model->ucac_conn_id, 'ucac_chat_id' => $model->ucac_chat_id], [
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
                'ucac_conn_id',
                'ucac_chat_id',
            ],
        ]) ?>

    </div>

</div>
