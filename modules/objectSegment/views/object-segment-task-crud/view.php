<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\objectSegment\src\entities\ObjectSegmentTask */

$this->title = $model->ostl_osl_id;
$this->params['breadcrumbs'][] = ['label' => 'Object Segment Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="object-segment-task-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ostl_osl_id' => $model->ostl_osl_id, 'ostl_tl_id' => $model->ostl_tl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ostl_osl_id' => $model->ostl_osl_id, 'ostl_tl_id' => $model->ostl_tl_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'ostl_osl_id',
                        'value' => $model->ostlOsl->osl_title
                    ],
                    [
                        'attribute' => 'ostl_tl_id',
                        'value' => $model->ostlTl->tl_title
                    ],
                    'ostl_created_dt',
                    'ostl_created_user_id:username',
                ],
            ]) ?>
        </div>
    </div>

</div>
