<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use common\models\Department;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CaseCategorySearch */
/* @var $dataProvider yii\data\SqlDataProvider */

$this->title = 'Case Categories Report';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="case-category-report">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php  echo $this->render('partial/_report_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cc_id',
                'options' => ['style' => 'width:160px'],
            ],
            [
                'attribute' => 'cc_dep_id',
                'format' => 'raw',
                'filter' => Department::getList(),
                'value' => static function ($model) {
                    return $model['dep_name'] ? $model['dep_name'] : 'undefined';
                }
            ],
            'cc_name',
            'pending',
            'processing',
            'followup',
            'solved',
            'trash'
        ]
    ]); ?>
    <?php Pjax::end(); ?>
</div>
