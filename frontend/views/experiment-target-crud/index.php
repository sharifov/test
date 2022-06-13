<?php

use common\components\experimentManager\models\ExperimentTarget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ExperimentTargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Experiment Targets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-target-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Experiment Target', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ext_id',
            'ext_target_id',
            [
                'attribute' => 'ext_target_type',
                'value' => static function (ExperimentTarget $model) {
                    return ExperimentTarget::EXT_TYPE_LIST[$model->ext_target_type];
                },
                'filter' => ExperimentTarget::EXT_TYPE_LIST
            ],
            'ext_experiment_id',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ExperimentTarget $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ext_id' => $model->ext_id]);
                }
            ],
        ],
    ]); ?>


</div>
