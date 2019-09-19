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
<?php Pjax::begin(['id' => 'sms', /*  'enablePushState' => false,'timeout' => false */]); ?>

<?php
    $gridColumns = [
        [
            'attribute' => 's_lead_id',
            'label' => 'Lead ID',
            'value' => function(\common\models\Sms $model) {
                return $model->s_lead_id ? Html::a($model->s_lead_id,[
                    'lead/view',
                    'gid' => $model->sLead->gid
                ]) : '-';
            },
            'contentOptions' => [
                'style' => 'width:60px'
            ],
            'format' => 'raw'
        ],
        [
            'attribute' => 's_sms_text',
            'label' => 'Mssage',
            'format' => 'raw',
        ],
        [
            'attribute' => 's_project_id',
            'label' => 'Project',
            'value' => function (\common\models\Sms $model) {
                return $model->sProject ? $model->sProject->name : '-';
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