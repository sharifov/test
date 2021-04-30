<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\project\entity\projectRelation\search\ProjectRelationSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Project Relations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-relation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Project Relation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-project-relation']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'prl_project_id:projectName',
            'prl_related_project_id:projectName',
            'prl_created_user_id:userName',
            ['class' => DateTimeColumn::class, 'attribute' => 'prl_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
