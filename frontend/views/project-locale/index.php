<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use kartik\select2\Select2;
use sales\model\project\entity\projectLocale\ProjectLocale;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\project\entity\projectLocale\search\ProjectLocaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Locales';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-locale-index">

    <h1><i class="fa fa-language"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add Project Locale', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => static function (ProjectLocale $model) {
            if (!$model->pl_enabled) {
                return [
                    'class' => 'danger'
                ];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'pl_project_id',
//            [
//                'class' => \common\components\grid\project\ProjectColumn::class,
//                'attribute' => 'pl_project_id',
//                'relation' => 'plProject',
//            ],
            //'pl_language_id',

            [
                //'label' => 'Type Name',
                'attribute' => 'pl_project_id',
                'value' => static function (ProjectLocale $model) {
                    return $model->plProject ? '<span class="badge badge-info">' . Html::encode($model->plProject->name) . '</span>' : '-';
                },
                'format' => 'raw',
                'filter' => Select2::widget([
                    //'model' => \common\models\search\ProjectLocaleSearch::class,
                    //'attribute' => 'pl_language_id',

                    'name' => 'ProjectLocaleSearch[pl_project_id]',
                    'data' => \common\models\Project::getList(true),

                    //'theme' => Select2::THEME_DEFAULT,
                    'size' => Select2::SMALL,
                    'value' => empty(Yii::$app->request->get('ProjectLocaleSearch')['pl_project_id']) ? null : Yii::$app->request->get('ProjectLocaleSearch')['pl_project_id'],
                    //'hideSearch' => false,
                    'options' => [
                        'placeholder' => '',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],

            [
                //'label' => 'Type Name',
                'attribute' => 'pl_language_id',
                'value' => static function (ProjectLocale $model) {
                    return '<span class="badge badge-warning">' . Html::encode($model->pl_language_id) . '</span>';
                },
                'format' => 'raw',
                'filter' => Select2::widget([
                    //'model' => \common\models\search\ProjectLocaleSearch::class,
                    //'attribute' => 'pl_language_id',

                    'name' => 'ProjectLocaleSearch[pl_language_id]',
                    'data' => \common\models\Language::getLocaleList(true),

                    //'theme' => Select2::THEME_DEFAULT,
                    'size' => Select2::SMALL,
                    'value' => empty(Yii::$app->request->get('ProjectLocaleSearch')['pl_language_id']) ? null : Yii::$app->request->get('ProjectLocaleSearch')['pl_language_id'],
                    //'hideSearch' => false,
                    'options' => [
                        'placeholder' => '',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],

            [
                'label' => 'Locale Name',
                //'attribute' => 'pl_language_id',
                'value' => static function (ProjectLocale $model) {
                    return $model->plLanguage ? Html::encode($model->plLanguage->name_ascii) : '-';
                },
            ],

            [
                'label' => 'Language',
                //'attribute' => 'pl_language_id',
                'value' => static function (ProjectLocale $model) {
                    return $model->plLanguage ? Html::encode($model->plLanguage->language) : '-';
                },
            ],

            'pl_default:boolean',
            'pl_enabled:boolean',
            //'pl_params',

            [
                'label' => 'Params Length',
                'value' => static function (ProjectLocale $model) {
                    return mb_strlen($model->pl_params);
                },
                'filter' => false,
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pl_created_user_id',
                'relation' => 'plCreatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pl_updated_user_id',
                'relation' => 'plUpdatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            //'pl_created_user_id',
            //'pl_updated_user_id',
            //'pl_created_dt',
            //'pl_updated_dt',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pl_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pl_updated_dt',
                'format' => 'byUserDateTime'
            ],

            [
                    'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} &nbsp;&nbsp; {default}',
                'contentOptions' => ['style' => 'width: 110px'],
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
//                    'update' => static function ($model, $key, $index) use ($user) {
//                        return $user->isAdmin();
//                    },
//
//                    'delete' => static function ($model, $key, $index) use ($user) {
//                        return $user->isAdmin();
//                    },

                        'default' => static function (ProjectLocale $model, $key, $index) {
                            return !$model->pl_default;
                        },
                    ],
                    'buttons' => [
                        'default' => static function ($url, ProjectLocale $model) {
                            return Html::a('<i class="fa fa-check-square-o text-info"></i>', ['project-locale/default', 'pl_project_id' => $model->pl_project_id, 'pl_language_id' => $model->pl_language_id], [
                                //'class' => 'btn btn-primary btn-xs take-processing-btn',
                                'title' => 'set Default',
                                'data-pjax' => 0,
                                'data' => [
                                    'confirm' => 'Are you sure you want set Default this Locale?',
                                    'pl_project_id' => $model->pl_project_id,
                                    'pl_language_id' => $model->pl_language_id
                                    //'method' => 'post',
                                ],
                            ]);
                        },

                    ],
            ],


        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
