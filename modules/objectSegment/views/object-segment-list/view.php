<?php

use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;
use modules\objectSegment\src\contracts\ObjectSegmentListContract;

/* @var $this yii\web\View */
/* @var $model \modules\objectSegment\src\entities\ObjectSegmentList */

$this->title                   = $model->osl_title . ' (' . $model->osl_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Object Segment List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="abac-policy-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->osl_id], ['class' => 'btn btn-primary']) ?>

        <?php if (!$model->osl_is_system) : ?>
            <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->osl_id], [
                'class' => 'btn btn-danger',
                'data'  => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method'  => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model'      => $model,
            'attributes' => [
                'osl_id',
                'osl_title',
                'osl_enabled:boolean',
                'osl_created_dt:byUserDateTime',
                'osl_updated_dt:byUserDateTime',
                'osl_updated_user_id:username',
            ],
        ]) ?>
    </div>


</div>
