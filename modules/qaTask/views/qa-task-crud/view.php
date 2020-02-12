<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

$this->title = $model->t_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qa-task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->t_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->t_id], [
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
            't_id',
            't_gid',
            't_object_type_id:qaObjectType',
            't_object_id',
            'category.tc_name',
            't_status_id:qaTaskStatus',
            't_rating:qaTaskRating',
            't_create_type_id:qaTaskCreatedType',
            't_description:ntext',
            't_department_id:department',
            't_deadline_dt:byUserDateTime',
            'assignedUser:userName',
            'createdUser:userName',
            'updatedUser:userName',
            't_created_dt:byUserDateTime',
            't_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
