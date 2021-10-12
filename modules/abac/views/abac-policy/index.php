<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\abac\src\entities\AbacPolicy;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\abac\src\entities\search\AbacPolicySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $importCount int */

$this->title = 'Abac Policies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Abac Policy', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-list"></i> Policy list content', ['list-content'], ['class' => 'btn btn-default']) ?>

        <?= Html::a('<i class="fa fa-upload"></i> Export File', ['export'], [
            'class' => 'btn btn-default',
            'data' => [
                'confirm' => 'Are you sure you want to Export all ABAC policy rules?',
            ],
        ]) ?>

        <?php if($importCount): ?>
            <?= Html::a('<i class="fa fa-download"></i> Continue Import (' . $importCount . ')', ['import'], ['class' => 'btn btn-warning']) ?>
        <?php else: ?>
            <?= Html::a('<i class="fa fa-download"></i> Import File', ['import'], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => static function (AbacPolicy $model) {
            if (!$model->ap_enabled) {
                return ['class' => 'danger'];
            }

//            if ($model->ap_effect === $model::EFFECT_DENY) {
//                return ['class' => 'danger'];
//            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'ap_id',
                'options' => [
                    'style' => 'width:80px'
                ],
            ],
            ['class' => 'yii\grid\ActionColumn'],

            //'ap_object',

            [
                'attribute' => 'ap_object',
                'value' => static function (AbacPolicy $model) {
                    return $model->ap_object ? '<span class="badge badge-primary">' . Html::encode($model->ap_object) . '</span>' : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'ap_sort_order',
                'options' => [
                    'style' => 'width:80px'
                ],
            ],

            //'ap_rule_type',
            'ap_subject',
            //'ap_subject_json',

            'ap_action',
            //'ap_action_json',
            //'ap_effect',
            [
                'attribute' => 'ap_effect',
                'value' => static function (AbacPolicy $model) {
                    return $model->getEffectLabel();
                },
                'format' => 'raw',
                'filter' => AbacPolicy::getEffectList()
            ],
            'ap_enabled:boolean',
            //'ap_title',

//            'ap_created_dt',
//            'ap_updated_dt',

//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'ap_created_user_id',
//                'relation' => 'apCreatedUser',
//                'placeholder' => 'Select User',
//            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ap_updated_user_id',
                'relation' => 'apUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ap_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ap_updated_dt',
            ],

            //'ap_created_user_id',
            //'ap_updated_user_id',


        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
