<?php

use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent */

$this->title = $model->getComponentEventName();
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Component Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-component-event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccce_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccce_id], [
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
                'ccce_id',
                [
                    'attribute' => 'ccce_chat_channel_id',
                    'value' => static function (ClientChatComponentEvent $model) {
                        return $model->chatChannel->ccc_name ?? null;
                    }
                ],
                [
                    'attribute' => 'ccce_component',
                    'value' => static function (ClientChatComponentEvent $model) {
                        return $model->getComponentEventName();
                    }
                ],
                [
                    'attribute' => 'ccce_event_type',
                    'value' => static function (ClientChatComponentEvent $model) {
                        return $model->getComponentTypeName();
                    }
                ],
                'ccce_enabled:booleanByLabel',
                'ccce_sort_order',
                'ccce_created_user_id:username',
                'ccce_updated_user_id:username',
                'ccce_created_dt:byUserDateTime',
                'ccce_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

    <div class="col-md-8">
        <h2>Component Config:</h2>
        <?php if ($model->ccce_component_config) : ?>
            <pre>
            <?php \yii\helpers\VarDumper::dump(@json_decode($model->ccce_component_config, true), 10, true) ?>
            </pre>
        <?php endif;?>
    </div>

</div>
