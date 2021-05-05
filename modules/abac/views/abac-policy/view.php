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
            'ap_title',
            'ap_rule_type',
            'ap_subject',
            //'ap_subject_json',
            'ap_object',
            'ap_action',
            'ap_action_json',
            //'ap_effect',
            [
                'attribute' => 'ap_effect',
                'value' => static function (AbacPolicy $model) {
                    return $model->getEffectName();
                },
            ],

            'ap_sort_order',
            'ap_created_dt:byUserDateTime',
            'ap_updated_dt:byUserDateTime',
            //'ap_created_user_id:userName',
            [
                'class' => \common\components\grid\UserColumn::class,
                'attribute' => 'ap_created_user_id',
                'relation' => 'apCreatedUser',
            ],
            [
                'class' => \common\components\grid\UserColumn::class,
                'attribute' => 'ap_updated_user_id',
                'relation' => 'apUpdatedUser',
            ],
            //'ap_updated_user_id:userName',


        ],
    ]) ?>
    </div>
        <div class="col-md-6">
            <h2>Subject JSON</h2>
            <pre><?php
                $subjectData = @json_decode($model->ap_subject_json, true);
                VarDumper::dump($subjectData, 10 , true);
                ?></pre>
        </div>

</div>
