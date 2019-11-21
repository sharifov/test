<?php

use sales\access\EmployeeProjectAccess;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $title string */
/* @var $searchModel common\models\search\AgentActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id);

?>

<h1><i class="fa fa-flag"></i> <?= \yii\helpers\Html::encode($title) ?></h1>
<?php Pjax::begin(['id' => 'cloned', /*  'enablePushState' => false,'timeout' => false */]); ?>

<?php
    $gridColumns = [
        [
            'attribute' => 'lead_id',
            'label' => 'Lead ID',
            'value' => function(\common\models\LeadFlow $model) {
                return $model->lead_id ? Html::a($model->lead_id,[
                    'lead/view',
                    'gid' => $model->lead->gid
                ]) : '-';
            },
            'contentOptions' => [
                'style' => 'width:60px'
            ],
            'format' => 'raw'
        ],
        [
            'attribute' => 'project_id',
            'label' => 'Project',
            'value' => static function (\common\models\LeadFlow $model) {
                return $model->lead ? $model->lead->project->name : '-';
            },
            'filter' => $projectList,
            'format' => 'raw',
        ],
        [
            'label' => 'Reason',
            'value' => static function (\common\models\LeadFlow $model) {
            return $model->lead ? $model->lead->description : '-';
        },
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