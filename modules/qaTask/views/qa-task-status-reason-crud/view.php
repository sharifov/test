<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReason */

$this->title = $model->tsr_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Status Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qa-task-status-reason-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->tsr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->tsr_id], [
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
            'tsr_id',
            'tsr_object_type_id:qaObjectType',
            'tsr_status_id:qaTaskStatus',
            'tsr_key',
            'tsr_name',
            'tsr_description',
            'tsr_comment_required:booleanByLabel',
            'tsr_enabled:booleanByLabel',
            'createdUser:userName',
            'updatedUser:userName',
            'tsr_created_dt:byUserDateTime',
            'tsr_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
