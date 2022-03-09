<?php

use modules\eventManager\src\entities\EventList;
use modules\eventManager\src\services\EventService;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventList */

$this->title = $model->el_key . ' (' . $model->el_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Event Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'el_id' => $model->el_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-close"></i> Delete', ['delete', 'el_id' => $model->el_id], [
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
                'el_id',
                'el_key',
                'el_category',
    //            'el_enable_type',
                [
                    'attribute' => 'el_enable_type',
                    'value' => static function (EventList $model) {
                        return EventService::getEnableTypeLabel($model->el_enable_type);
                    },
                    'format' => 'raw',
                ],
                'el_enable_log:boolean',
                'el_break:boolean',
                'el_description',
                'el_sort_order',
                'el_cron_expression',
                'el_condition:ntext',
                [
                    'label' => 'Handler List',
                    'value' => static function (EventList $model) {
                        $data = [];
                        if ($list = $model->eventHandlers) {
                            foreach ($list as $handler) {
                                $data[] = Html::a(
                                    Html::decode($handler->eh_class . '::' . $handler->eh_method),
                                    ['event-handler/view', 'eh_id' => $handler->eh_id],
                                    ['target' => '_blank', 'data-pjax' => 0, 'title' => 'View ID: ' . $handler->eh_id]
                                );
                            }
                        }
                        return $data ? '<p>' . implode('</p><p>', $data) . '</p>' : '-';
                    },
                    'format' => 'raw',
                ],
                //'el_params:ntext',
                'el_builder_json',
                'el_updated_dt:byUserDateTime',
                'el_updated_user_id:username',
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <h4>Params:</h4>
        <pre><?php \yii\helpers\VarDumper::dump($model->el_params, 10, true) ?></pre>
    </div>

</div>
