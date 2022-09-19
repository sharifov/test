<?php

use common\components\grid\BooleanColumn;
use frontend\widgets\nestedSets\NestedSetsWidget;
use src\helpers\nestedSets\NestedSetsHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Department;
use src\entities\cases\CaseCategory;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\entities\cases\CaseCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Case Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    Pjax::begin(['scrollTo' => 0]); ?>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cc_id',
                'options' => ['style' => 'width:160px'],
            ],
            [
                'attribute' => 'cc_key',
                'options' => ['style' => 'width:160px'],
            ],
            'cc_name',
            [
                'attribute' => 'cc_dep_id',
                'format' => 'raw',
                'filter' => Department::getList(),
                'value' => static function ($model) {
                    return $model->dep ? $model->dep->dep_name : 'undefined';
                },
            ],
            [
                'attribute' => 'parentCategory',
                'format' => 'raw',
                'value' => static function ($model) {
                    $parents = $model->parents()->asArray()->all();
                    $parentsNamesString = null;
                    if ($parents) {
                        $parentsNames = array_column($parents, 'cc_name');
                        $parentsNamesString = NestedSetsHelper::formatHierarchyString($parentsNames);
                    }

                    return $parentsNamesString;
                },
                'filter' => NestedSetsWidget::widget([
                    'query' => CaseCategory::findNestedSets(),
                    'label' => '',
                    'model' => $searchModel,
                    'parentCategoryId' => $searchModel->parentCategoryId,
                ]),
            ],
            ['class' => BooleanColumn::class, 'attribute' => 'cc_system'],
            ['class' => BooleanColumn::class, 'attribute' => 'cc_enabled'],
            ['class' => BooleanColumn::class, 'attribute' => 'cc_allow_to_select'],
            ['class' => yii\grid\ActionColumn::class],
        ],
    ]);
?>

    <?php
    Pjax::end(); ?>


</div>
