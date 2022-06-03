<?php

use modules\abac\src\entities\AbacPolicy;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\AbacPolicy */

$this->title = $model->ap_object . ' (' . $model->ap_id . ')';
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="abac-policy-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->ap_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->ap_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ap_id',
            'ap_hash_code',
            'ap_subject',
            //'ap_subject_json',
            //'ap_object',
            'ap_action',
            'ap_action_json',
            //'ap_effect',
            [
                'attribute' => 'ap_effect',
                'value' => static function (AbacPolicy $model) {
                    return $model->getEffectLabel();
                },
                'format' => 'raw'
            ],
            'ap_sort_order',
            'ap_title',
            'ap_rule_type',
            'ap_enabled:boolean',
            'ap_created_dt:byUserDateTime',
            'ap_updated_dt:byUserDateTime',
            'ap_created_user_id:username',
            'ap_updated_user_id:username',
        ],
    ]) ?>
    </div>
        <div class="col-md-6">
            <h2>Object</h2>
            <pre><b><?php echo Html::encode($model->ap_object); ?></b></pre>
            <h2>Subject</h2>
            <pre><?php echo Html::encode(str_replace('r.sub.', '', $model->ap_subject)); ?></pre>
            <p class="text-info"><i class="fa fa-info-circle"></i> Params w/o "r.sub." prefix</p>
            <h2>Subject JSON</h2>
            <pre><?php
                $subjectData = @json_decode($model->ap_subject_json, true);
                VarDumper::dump($subjectData, 10, true);
            ?></pre>
        </div>

</div>
