<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use common\models\Quote;
use yii\helpers\Url;
use common\models\Airport;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'QA Sold Queue';

$userList = \common\models\Employee::getList();
$projectList = \common\models\Project::getList();

$this->params['breadcrumbs'][] = $this->title;

?>

<h1><i class="fa fa-flag"></i> <?= \yii\helpers\Html::encode($this->title) ?></h1>

<style>
    .dropdown-menu {
        z-index: 1010 !important;
    }
</style>
<div class="lead-sold">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>

    <?= $this->render('_search_sold', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        /*[
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => function ($model) {
                return Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],*/

        [
            'attribute' => 'id',
            'label' => 'Lead ID',
            'contentOptions' => [
                'style' => 'width:60px'
            ]
        ],
        [
            // 'attribute' => 'client_id',
            'header' => 'Client',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {

                if ($model->client) {
                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }
                } else {
                    $clientName = '-';
                }

                $emails = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                $phones = $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                return $clientName . '<br/>' . $emails . '<br/>' . $phones;
            },
            'contentOptions' => [
                'style' => 'width: 200px;'
            ]
            // 'filter' => \common\models\Employee::getList()
        ],
        [
            'header' => 'Agent',
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $userList,
        ],
        [
            'label' => 'Date of Issue',
            'attribute' => 'updated',
            'value' => function (\common\models\Lead $model) {
                return $model['updated'];
            },
            'format' => 'datetime',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy'
                ]
            ]),
            'contentOptions' => [
                'style' => 'width: 100px;text-align:center;'
            ]
        ],
        [
            'attribute' => 'project_id',
            'value' => function (\common\models\Lead $model) {
                return $model->project ? $model->project->name : '-';
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:120px'
            ],
            'filter' => $projectList,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, Lead $model, $key) {
                    return Html::a('<i class="fa fa-search"></i> view', [
                        'lead/view',
                        'gid' => $model->gid
                    ], [
                        'class' => 'btn btn-info btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View lead'
                    ]);
                }
            ]
        ]
    ];

    ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'toolbar' => false,
        'pjax' => false,
        'striped' => true,
        'condensed' => false,
        'responsive' => true,
        'hover' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => [
            'scrollingTop' => 20
        ],
        /*'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Sold</h3>'
        ]*/

    ]);

    ?>

    <?php Pjax::end(); ?>


</div>
