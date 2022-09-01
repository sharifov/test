<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\DbDataSensitive;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\BooleanColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DbDataSensitiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DB Data Sensitive';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="date-sensitive-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'dda_key',
            'dda_name',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'dda_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'dda_updated_dt'
            ],
            [
                'class' => BooleanColumn::class,
                'attribute' => 'db_is_system',
                'label' => 'Is System',
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, DbDataSensitive $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->dda_id]);
                },
                'template' => '{create-view} {drop-view} {view} {update} {delete} ',
                'buttons' => [
                    'create-view' => function ($url, DbDataSensitive $model, $key) {
                        return Html::a('<i class="fa fa-database" style="font-size: 100%"></i>', ['/db-data-sensitive/create-views', 'id' => $model->dda_id], [
                            'title' => 'Create All Views',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to reinit views?'
                        ]);
                    },
                    'drop-view' => function ($url, DbDataSensitive $model, $key) {
                        return Html::a('<i class="fa fa-times" style="font-size: 120%"></i>', ['/db-data-sensitive/drop-views', 'id' => $model->dda_id], [
                            'title' => 'Drop All Views',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to delete views?'
                        ]);
                    },
                    'update' => function ($url, DbDataSensitive $model, $key) {
                        if (!$model->isSystem()) {
                            return Html::a(
                                '<i class="glyphicon glyphicon-pencil"></i>',
                                $url,
                                [
                                    'title' => 'Update',
                                    'aria-label' => 'Update',
                                    'data-pjax' => 0,
                                ]
                            );
                        }
                    },
                    'delete' => function ($url, DbDataSensitive $model, $key) {
                        if (!$model->isSystem()) {
                            return Html::a(
                                '<i class="glyphicon glyphicon-trash"></i>',
                                $url,
                                [
                                    'title' => 'Delete',
                                    'aria-label' => 'Delete',
                                    'data' => [
                                        'pjax' => 0,
                                        'confirm' => 'Are you sure you want to delete this item?',
                                        'method' => 'post'
                                    ]
                                ]
                            );
                        }
                    }
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
