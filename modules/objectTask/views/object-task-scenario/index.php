<?php

use common\components\grid\DateTimeColumn;
use modules\objectTask\src\entities\ObjectTaskScenario;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\objectTask\src\entities\ObjectTaskScenarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Object Task Scenarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-task-scenario-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Object Task Scenario', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ots_id',
            'ots_key',
            ['class' => DateTimeColumn::class, 'attribute' => 'ots_updated_dt'],
            'ots_updated_user_id',
            [
                'attribute' => 'ots_enable',
                'format' => 'booleanByLabel',
                'filter' => [1 => 'Enable', 0 => 'Disable'],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update}',
                'urlCreator' => function ($action, ObjectTaskScenario $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ots_id' => $model->ots_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
