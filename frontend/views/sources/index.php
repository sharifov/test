<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SourcesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sources';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sources-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sources', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'id',
                'value' => function (\common\models\Sources $model) {
                    return $model->id;
                },
                'options' => ['style' => 'width: 100px']
            ],

            //'project_id',
            [
                'attribute' => 'project_id',
                'value' => function (\common\models\Sources $model) {
                    return $model->project ? $model->project->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],
            'name',
            'cid',
            //'last_update',
            [
                'attribute' => 'last_update',
                'value' => function (\common\models\Sources $model) {
                    return $model->last_update ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->last_update)) : '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'phone_number',
                'value' => function (\common\models\Sources $model) {
                    return $model->phone_number ? '<i class="fa fa-phone"></i> ' . $model->phone_number : '-';
                },
                'format' => 'raw'
            ],
            //'phone_number',
            'default:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
