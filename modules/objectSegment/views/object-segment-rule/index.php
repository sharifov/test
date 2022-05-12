<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\components\grid\Select2Column;
use yii\web\JsExpression;
use modules\objectSegment\src\entities\ObjectSegmentRule;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \modules\objectSegment\src\entities\search\ObjectSegmentRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $importCount int */
/* @var $objectList array */
/* @var $objectSegmentList array */

$this->title                   = 'Object Segment Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Object Segment Rule', ['create'], ['class' => 'btn btn-success']) ?>

    </p>

    <p>
        <?= Html::a('<i class="fa fa-remove"></i> Reset Cache', ['invalidate-cache'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions'   => static function (ObjectSegmentRule $model) {
            if (!$model->osr_enabled) {
                return ['class' => 'danger'];
            }
        },
        'columns'      => [
            [
                'attribute' => 'osr_id',
                'options'   => [
                    'style' => 'width:80px'
                ],
            ],
            [
                'label'  => 'Status',
                'value'  => static function (ObjectSegmentRule $model) use ($objectList) {
                    $exist = in_array($model->osr_osl_id, array_flip($objectList));
                    if (!$exist) {
                        return '<span class="badge badge-danger" title="Invalid object (not exists)">Error</span>';
                    }
                    return '';
                },
                'format' => 'raw',
            ],
            [
                'class' =>  Select2Column::class,
                'label'     => 'Object Segment',
                'attribute' => 'osr_osl_id',
                'value'     => static function (ObjectSegmentRule $model) {
                    return $model->osrObjectSegmentList->osl_title;
                },
                'id' => 'object-segment-list-filter',
                'data' => $objectSegmentList,
                'format'    => 'raw',
                'pluginOptions' => ['allowClear' => true],
            ],
            [
                'attribute' => 'osr_rule_condition',
                'value'     => static function (ObjectSegmentRule $model) {
                    return '<small>' . \yii\helpers\StringHelper::
                        truncate(
                            str_replace(
                                'r.sub.',
                                '',
                                Html::encode($model->osr_rule_condition)
                            ),
                            200
                        ) . '</small>';
                },
                'format'    => 'raw',
            ],
            'osr_enabled:boolean',
            [
                'class'       => UserSelect2Column::class,
                'attribute'   => 'osr_updated_user_id',
                'relation'    => 'osrUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class'     => DateTimeColumn::class,
                'attribute' => 'osr_updated_dt',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {clone} {delete}',
                'buttons' => [
                    'clone' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/object-segment/object-segment-rule/create?osr_parent_id=' . $model->osr_id]);
                        return Html::a('<i class="fa fa-copy"></i>', $url, ['title' => 'Clone']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
