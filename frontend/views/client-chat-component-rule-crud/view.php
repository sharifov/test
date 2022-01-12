<?php

use src\model\clientChat\componentRule\entity\ClientChatComponentRule;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\clientChat\componentRule\entity\ClientChatComponentRule */

$this->title = $model->getComponentName();
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Component Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-component-rule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'cccr_component_event_id' => $model->cccr_component_event_id, 'cccr_value' => $model->cccr_value, 'cccr_runnable_component' => $model->cccr_runnable_component], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cccr_component_event_id' => $model->cccr_component_event_id, 'cccr_value' => $model->cccr_value, 'cccr_runnable_component' => $model->cccr_runnable_component], [
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
                [
                    'attribute' => 'cccr_component_event_id',
                    'value' => static function (ClientChatComponentRule $model) {
                        return Html::a($model->componentEvent->getComponentEventName() . ' (' . $model->componentEvent->ccce_id . ')', \yii\helpers\Url::to(['/client-chat-component-event-crud/view', 'id' => $model->cccr_component_event_id]));
                    },
                    'format' => 'raw'
                ],
                'cccr_value',
                [
                    'attribute' => 'cccr_runnable_component',
                    'value' => static function (ClientChatComponentRule $model) {
                        return $model->getComponentName();
                    }
                ],
                'cccr_sort_order',
                'cccr_enabled:booleanByLabel',
                'cccr_created_user_id:username',
                'cccr_updated_user_id:username',
                'cccr_created_dt:byUserDateTime',
                'cccr_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

    <div class="col-md-8">
        <h2>Component Config:</h2>
        <?php if ($model->cccr_component_config) : ?>
            <pre>
            <?php \yii\helpers\VarDumper::dump(@json_decode($model->cccr_component_config, true), 10, true) ?>
            </pre>
        <?php endif;?>
    </div>

</div>
