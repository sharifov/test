<?php

use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model QaTaskActionReason */

$this->title = $model->tar_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Action Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qa-task-action-reason-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->tar_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->tar_id], [
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
            'tar_id',
            'tar_object_type_id:qaObjectType',
            'tar_action_id:qaTaskAction',
            'tar_key',
            'tar_name',
            'tar_description',
            'tar_comment_required:booleanByLabel',
            'tar_enabled:booleanByLabel',
            'createdUser:userName',
            'updatedUser:userName',
            'tar_created_dt:byUserDateTime',
            'tar_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
