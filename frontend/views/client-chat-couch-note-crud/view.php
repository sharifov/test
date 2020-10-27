<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\ClientChatCouchNote\entity\ClientChatCouchNote */

$this->title = $model->cccn_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Couch Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-couch-note-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cccn_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cccn_id], [
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
                'cccn_id',
                'cccn_cch_id',
                'cccn_rid',
                'cccn_message:ntext',
                'cccn_alias',
                'cccn_created_user_id',
                'cccn_created_dt',
            ],
        ]) ?>

    </div>

</div>
