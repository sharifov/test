<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\DateSensitive;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DateSensitiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Date Sensitive';
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
            'da_key',
            'da_name',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'da_updated_user_id',
                'relation' => 'daUpdatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'da_updated_dt'
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, DateSensitive $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->da_id]);
                },
                'template' => '{create-view} {drop-view} {view} {update} {delete} ',
                'buttons' => [
                    'create-view' => function ($url, DateSensitive $model, $key) {
                        return Html::a('<i class="fa fa-database" style="font-size: 100%"></i>', ['/date-sensitive/create-views', 'id' => $model->da_id], [
                            'title' => 'Create All Views',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to reinit views?'
                        ]);
                    },
                    'drop-view' => function ($url, DateSensitive $model, $key) {
                        return Html::a('<i class="fa fa-times" style="font-size: 120%"></i>', ['/date-sensitive/drop-views', 'id' => $model->da_id], [
                            'title' => 'Drop All Views',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to delete views?'
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
