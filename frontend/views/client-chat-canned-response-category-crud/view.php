<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory */

$this->title = $model->crc_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Canned Response Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-canned-response-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->crc_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->crc_id], [
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
                'crc_id',
                'crc_name',
                'crc_enabled',
                'crc_created_dt',
                'crc_updated_dt',
                'crc_created_user_id',
                'crc_updated_user_id',
            ],
        ]) ?>

    </div>

</div>
