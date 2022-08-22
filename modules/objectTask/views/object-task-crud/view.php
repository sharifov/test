<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTask */

$this->title = $model->ot_uuid;
$this->params['breadcrumbs'][] = ['label' => 'Object Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="object-task-view col-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ot_uuid' => $model->ot_uuid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ot_uuid' => $model->ot_uuid], [
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
            'ot_uuid',
            'ot_q_id',
            'ot_object',
            'ot_object_id',
            'execution_dt',
            'ot_command',
            'ot_status',
            'ot_created_dt',
        ],
    ]) ?>

</div>
