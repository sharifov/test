<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventHandler */

$this->title = $model->eh_id;
$this->params['breadcrumbs'][] = ['label' => 'Event Handlers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-handler-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'eh_id' => $model->eh_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'eh_id' => $model->eh_id], [
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
            'eh_id',
            'eh_el_id',
            'eh_class',
            'eh_method',
            'eh_enable_type',
            'eh_enable_log',
            'eh_asynch',
            'eh_break',
            'eh_sort_order',
            'eh_cron_expression',
            'eh_condition:ntext',
            'eh_params:ntext',
            'eh_builder_json',
            'eh_updated_dt',
            'eh_updated_user_id',
        ],
    ]) ?>

</div>
