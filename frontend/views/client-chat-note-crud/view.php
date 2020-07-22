<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatNote\entity\ClientChatNote */

$this->title = $model->ccn_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-note-view">
    <div class="row">
        <div class="col-md-4">

            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a('Update', ['update', 'id' => $model->ccn_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->ccn_id], [
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
                    'ccn_id',
                    'ccn_chat_id',
                    'ccn_user_id',
                    'ccn_note:ntext',
                    'ccn_deleted',
                    'ccn_created_dt',
                    'ccn_updated_dt',
                ],
            ]) ?>
         </div>
    </div>
</div>
