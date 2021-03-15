<?php

use common\components\grid\DateTimeColumn;
use common\models\Sources;
use yii\grid\GridView;
use yii\helpers\Html;
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
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?php if ($searchModel->only_duplicate) : ?>
        <?php echo Html::a(
            'Show all',
            ['index', $searchModel->formName() . '[only_duplicate]' => 0],
            ['class' => 'btn btn-primary']
        ) ?>
    <?php else : ?>
        <?php echo Html::a(
            'Show only duplicate',
            ['index', $searchModel->formName() . '[only_duplicate]' => 1],
            ['class' => 'btn btn-success', 'title' => 'Identical Cid and Project']
        ) ?>
    <?php endif ?>

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

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'project_id',
                'relation' => 'project',
            ],

            'name',
            'cid',

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'last_update'
            ],

            /*[
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
            ],*/
            'default:boolean',
            'hidden:boolean',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {default}',
                'buttons' => [
                    'default' => function ($url, Sources $model) {
                        if ($model->isDefault()) {
                            return '';
                        }
                        return Html::a('Set Default', [
                            'sources/set-default'
                        ], [
                             'data-method' => 'POST',
                             'data-params' => [
                                     'id' => $model->id
                             ],
                             'class' => 'btn btn-info btn-xs',
                             'data-pjax' => 0,
                             'data' => [
                                'confirm' => 'Are you sure you want to set default this item?',
                                'method' => 'post',
                             ],
                        ]);
                    },
                ],
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
