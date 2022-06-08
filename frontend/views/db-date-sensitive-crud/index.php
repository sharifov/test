<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\DbDateSensitive;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DbDateSensitiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DB Date Sensitive';
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
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, DbDateSensitive $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->dda_id]);
                },
                'template' => '{create-view} {drop-view} {view} {update} {delete} ',
                'buttons' => [
                    'create-view' => function ($url, DbDateSensitive $model, $key) {
                        return Html::a('<i class="fa fa-database" style="font-size: 100%"></i>', ['/db-date-sensitive/create-views', 'id' => $model->dda_id], [
                            'title' => 'Create All Views',
                            'data-pjax' => 0,
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to reinit views?'
                        ]);
                    },
                    'drop-view' => function ($url, DbDateSensitive $model, $key) {
                        return Html::a('<i class="fa fa-times" style="font-size: 120%"></i>', ['/db-date-sensitive/drop-views', 'id' => $model->dda_id], [
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
