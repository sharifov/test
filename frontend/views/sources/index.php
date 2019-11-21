<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

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
        <?//= Html::a('Create Sources', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'rowOptions' => function (\common\models\Sources $model) {

            if ($model->hidden) {
                return [
                    'class' => 'danger'
                ];
            }

            if ($model->default) {
                return [
                    'class' => 'warning'
                ];
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'id',
                'value' => static function (\common\models\Sources $model) {
                    return $model->id;
                },
                'options' => ['style' => 'width: 100px']
            ],

            //'project_id',
            [
                'attribute' => 'project_id',
                'value' => static function (\common\models\Sources $model) {
                    return $model->project ? $model->project->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],
            'name',
            'cid',
            //'last_update',
            [
                'attribute' => 'last_update',
                'value' => static function (\common\models\Sources $model) {
                    return $model->last_update ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->last_update)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'last_update',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
            'default:boolean',
            'hidden:boolean',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
