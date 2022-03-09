<?php

use modules\eventManager\src\entities\EventHandler;
use modules\eventManager\src\services\EventService;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventHandler */

$this->title = $model->eh_class . '::' . $model->eh_method . ' (' . $model->eh_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Event Handlers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-handler-view">

    <h1><i class="fa fa-list"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'eh_id' => $model->eh_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-close"></i> Delete', ['delete', 'eh_id' => $model->eh_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'eh_id',
            'eh_el_id',
            [
                //'attribute' => 'eh_el_id',
                'label' => 'Event Key',
                'value' => static function (EventHandler $model) {
                    return $model->eventList ? $model->eventList->el_key : '-';
                },
            ],

            'eh_class',
            'eh_method',
            //'eh_enable_type',

            [
                'attribute' => 'eh_enable_type',
                'value' => static function (EventHandler $model) {
                    return EventService::getEnableTypeLabel($model->eh_enable_type);
                },
                'format' => 'raw',
            ],

            'eh_enable_log:boolean',
            'eh_asynch:boolean',
            'eh_break:boolean',
            'eh_sort_order',
            'eh_cron_expression',
            'eh_condition:ntext',
            //'eh_params',
            'eh_builder_json',
            'eh_updated_dt:byUserDateTime',
            'eh_updated_user_id:username',
        ],
    ]) ?>
    </div>
    <div class="col-md-6">
        <h4>Params:</h4>
        <pre><?php \yii\helpers\VarDumper::dump($model->eh_params, 10, true) ?></pre>
    </div>

</div>
