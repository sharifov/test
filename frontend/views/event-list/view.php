<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventList */

$this->title = $model->el_id;
$this->params['breadcrumbs'][] = ['label' => 'Event Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'el_id' => $model->el_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'el_id' => $model->el_id], [
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
            'el_id',
            'el_key',
            'el_category',
            'el_description',
            'el_enable_type',
            'el_enable_log',
            'el_break',
            'el_sort_order',
            'el_cron_expression',
            'el_condition:ntext',
            'el_builder_json',
            'el_updated_dt',
            'el_updated_user_id',
        ],
    ]) ?>

</div>
