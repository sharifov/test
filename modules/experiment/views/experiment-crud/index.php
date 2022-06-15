<?php

use modules\experiment\models\Experiment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel cmodules\experiment\models\search\ExperimentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Experiments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="experiment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Experiment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ex_id',
            'ex_code',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Experiment $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ex_id' => $model->ex_id]);
                }
            ],
        ],
    ]); ?>


</div>
