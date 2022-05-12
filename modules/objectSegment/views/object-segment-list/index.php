<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\components\grid\Select2Column;
use yii\web\JsExpression;
use modules\objectSegment\src\entities\ObjectSegmentList;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \modules\objectSegment\src\entities\search\ObjectSegmentListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $importCount int */
/* @var $objectTypeList array */

$this->title                   = 'Object Segment List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            '<i class="fa fa-plus"></i>
 Create Object Segment',
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>

    </p>

    <p>
        <?= Html::a('<i class="fa fa-remove"></i> Reset Cache', ['invalidate-cache'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions'   => static function (ObjectSegmentList $model) {
            if (!$model->osl_enabled) {
                return ['class' => 'danger'];
            }
        },
        'columns'      => [
            [
                'attribute' => 'osl_id',
                'options'   => [
                    'style' => 'width:80px'
                ],
            ],
            [
                'attribute' => 'osl_title',
            ],
            [
                'label'  => 'Status',
                'value'  => static function (ObjectSegmentList $model) use ($objectTypeList) {
                    $exist = in_array($model->osl_ost_id, array_flip($objectTypeList));
                    if (!$exist) {
                        return '<span class="badge badge-danger" title="Invalid object (not exists)">Error</span>';
                    }
                    return '';
                },
                'format' => 'raw',
            ],
            [
                'class'     => Select2Column::class,
                'label'     => 'Object Type',
                'attribute' => 'osl_ost_id',
                'value'     => static function (ObjectSegmentList $model) {
                    return $model->oslObjectSegmentType->ost_key;
                },
                'id'        => 'object-segment-list-filter',
                'data'      => $objectTypeList,
                'format'    => 'raw',
            ],
            'osl_enabled:boolean',
            [
                'class'       => UserSelect2Column::class,
                'attribute'   => 'osl_updated_user_id',
                'relation'    => 'oslUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class'     => DateTimeColumn::class,
                'attribute' => 'osl_updated_dt',
            ],
            [
                'class'    => ActionColumn::class,
                'template' => '{view} {update} {viewSegmentRules} {delete}',
                'buttons'  => [
                    'viewSegmentRules' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/object-segment/object-segment-rule/?ObjectSegmentRuleSearch[osr_osl_id]=' . $model->osl_id]);
                        return Html::a('<i class="fa fa-toggle-down"></i>', $url, ['title' => 'View Rules', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
