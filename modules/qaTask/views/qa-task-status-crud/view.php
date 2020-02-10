<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus */

$this->title = $model->ts_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qa-task-status-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ts_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ts_id], [
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
            'ts_id:qaTaskStatus',
            'ts_name',
            'ts_description',
            'ts_enabled:booleanByLabel',
            'ts_css_class',
            'createdUser:userName',
            'updatedUser:userName',
            'ts_created_dt:byUserDateTime',
            'ts_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
