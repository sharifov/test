<?php

use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\objectSegment\src\entities\ObjectSegmentRule */

$this->title                   = $model->osr_title . ' (' . $model->osr_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Object Segment Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="abac-policy-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->osr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->osr_id], [
            'class' => 'btn btn-danger',
            'data'  => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method'  => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model'      => $model,
            'attributes' => [
                'osr_id',
                'osr_title',
                'osr_enabled:boolean',
                'osr_created_dt:byUserDateTime',
                'osr_updated_dt:byUserDateTime',
                'osr_updated_user_id:username',
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <h2>Object</h2>
        <pre><b><?php echo Html::encode($model->osrObjectSegmentList->oslObjectSegmentType->ost_key . ' => ' . $model->osrObjectSegmentList->osl_title); ?></b></pre>
        <h2>Rule Condition</h2>
        <pre><?php echo Html::encode(str_replace('r.sub.', '', $model->osr_rule_condition)); ?></pre>
        <p class="text-info"><i class="fa fa-info-circle"></i> Params w/o "r.sub." prefix</p>
        <h2>Rule Condition JSON</h2>
        <pre><?php
            $subjectData = @json_decode($model->osr_rule_condition_json, true);
            VarDumper::dump($subjectData, 10, true); ?></pre>
    </div>

</div>
