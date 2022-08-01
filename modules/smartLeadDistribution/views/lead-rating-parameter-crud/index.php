<?php

use kartik\grid\ActionColumn;
use modules\smartLeadDistribution\src\entities\LeadRatingParameter;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\smartLeadDistribution\src\entities\LeadRatingParameterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Rating Parameters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-rating-parameter-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Rating Parameter', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'lrp_id',
            [
                'attribute' => 'lrp_object',
                'value' => static function (LeadRatingParameter $model) {
                    $obj = SmartLeadDistributionService::getByName($model->lrp_object);

                    return $obj::OPTGROUP_CALL;
                },
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => SmartLeadDistribution::OBJ_LIST,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Select Object'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'lrp_attribute',
                'value' => static function (LeadRatingParameter $model) {
                    $obj = SmartLeadDistributionService::getDataForField($model->lrp_object, $model->lrp_attribute);

                    return $obj[0]['label'];
                },
                'group' => true,
                'groupedRow' => true,
                'groupOddCssClass' => 'bg-info',
                'groupEvenCssClass' => 'bg-info',
            ],
            [
                'attribute' => 'lrp_condition',
                'enableSorting' => false,
                'contentOptions' => [
                    'style' => [
                        'max-width' => '600px',
                        'white-space' => 'nowrap',
                        'overflow' => 'hidden',
                        'text-overflow' => 'ellipsis'
                    ],
                ],
            ],
            [
                'attribute' => 'lrp_point',
                'enableSorting' => false,
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, LeadRatingParameter $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lrp_id' => $model->lrp_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
