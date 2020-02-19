<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory */

$this->title = $model->tc_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qa-task-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->tc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->tc_id], [
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
            'tc_id',
            'tc_key',
            'tc_object_type_id:qaTaskObjectType',
            'tc_name',
            'tc_description',
            'tc_enabled:booleanByLabel',
            'tc_default:booleanByLabel',
            'createdUser:userName',
            'updatedUser:userName',
            'tc_created_dt:byUserDateTime',
            'tc_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
