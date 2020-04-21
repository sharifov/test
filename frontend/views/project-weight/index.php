<?php

use common\models\Project;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProjectWeightSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Weights';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-weight-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Project Weight', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            /*[
                'attribute' => 'pw_project_id',
                'value' => 'project.name',
                'filter' => Project::getList()
            ],*/

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'pw_project_id',
                'relation' => 'project',
            ],

            'pw_weight',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
