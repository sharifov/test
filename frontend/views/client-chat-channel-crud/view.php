<?php

use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannel\entity\ClientChatChannel */

$this->title = 'Channel: ' . $model->ccc_name;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-channel-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccc_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccc_id], [
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
                'ccc_id',
                'ccc_name',
                'ccc_project_id:projectName',
                'ccc_dep_id:departmentName',
                [
                    'attribute' => 'ccc_ug_id',
                    'value' => static function (ClientChatChannel $model) {
                        return $model->cccUg ? $model->cccUg->ug_name : null;
                    }
                ],
                'ccc_disabled:booleanByLabel',
                'ccc_created_dt:byUserDateTime',
                'ccc_updated_dt:byUserDateTime',
                'ccc_created_user_id:username',
                'ccc_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
