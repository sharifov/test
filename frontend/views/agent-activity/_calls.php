<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $searchModel common\models\search\AgentActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $projectList = \common\models\Project::getList();
} else {
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

?>

<h1><i class="fa fa-flag"></i> <?= \yii\helpers\Html::encode($title) ?></h1>
<?php Pjax::begin(['id' => 'inbound-calls', /*  'enablePushState' => false,'timeout' => false */]); ?>

<?php
    $gridColumns = [
        [
            'attribute' => 'c_lead_id',
            'label' => 'Lead ID',
            'value' => function(\common\models\Call $model) {
                return $model->c_lead_id ? Html::a($model->c_lead_id,[
                    'lead/view',
                    'gid' => $model->cLead->gid
                ]) : '-';
            },
            'contentOptions' => [
                'style' => 'width:60px'
            ],
            'format' => 'raw'
        ],
        [
            'attribute' => 'c_project_id',
            'label' => 'Project',
            'value' => function (\common\models\Call $model) {
                return $model->cProject ? $model->cProject->name : '-';
            },
            'filter' => $projectList,
            'format' => 'raw',
            ],
    ];

    ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]);

    ?>

    <?php Pjax::end(); ?>