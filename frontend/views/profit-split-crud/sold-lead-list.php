<?php

use common\components\grid\Select2Column;
use common\models\Lead;
use src\access\ListsAccess;
use src\auth\Auth;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProfitSplitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$user = Auth::user();
$lists = new ListsAccess($user->id);

$this->title = 'Sold Lead without Profit Split';
$this->params['breadcrumbs'][] = ['label' => 'Profit Split', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profit-split-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'id',
                'value' => static function (Lead $model) {
                    return Html::a($model->id, [
                        'lead/view', 'gid' => $model->gid
                    ], [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]);
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'attribute' => 'uid',
                'options' => [
                    'style' => 'width:100px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'project_id',
                'relation' => 'project',
            ],
            [
                'class' => \common\components\grid\department\DepartmentColumn::class,
                'label' => 'Department',
                'attribute' => 'l_dep_id',
                'relation' => 'lDep',
            ],
            [
                'attribute' => 'client_id',
                'value' => static function (Lead $model) {
                    return Yii::$app->formatter->asClient($model->client_id);
                },
                'format' => 'raw',
            ],
            [
                'header' => 'Client',
                'format' => 'raw',
                'value' => static function (Lead $lead) use ($user) {
                    if ($lead->client) {
                        $clientName = $lead->client->first_name . ' ' . $lead->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '- - - ';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                        }
                        if ($lead->client->isExcluded()) {
                            $clientName = ClientFormatter::formatExclude($lead->client)  . $clientName;
                        }
                    } else {
                        $clientName = '-';
                    }
                    return $clientName;
                },
                'options' => [
                    'style' => 'width:180px'
                ],
            ],
            [
                'attribute' => 'status',
                'value' => static function (Lead $lead) {
                    return $lead->getStatusName(true);
                },
                'format' => 'raw',
                'filter' => false,
                'options' => [
                    'style' => 'width:100px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'attribute' => 'cabin',
                'value' => static function (Lead $model) {
                    return $model->getCabinClassName();
                },
                'filter' => Lead::CABIN_LIST,
            ],
            [
                'label' => 'Pax',
                'value' => static function (Lead $model) {
                    $str = '<i class="fa fa-male"></i> <span title="adult">' . $model->adults . '</span> / <span title="child">' . $model->children . '</span> / <span title="infant">' . $model->infants . '</span>';
                    return $str;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'class' => Select2Column::class,
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => static function (Lead $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> ' . Html::encode($model->employee->username) : '-';
                },
                'data' => $lists->getEmployees(true) ?: [],
                'filter' => true,
                'id' => 'employee-filter',
                'options' => ['width' => '200px'],
                'pluginOptions' => ['allowClear' => true]
            ],
            [
                'attribute' => 'createdRangeTime',
                'value' => static function (Lead $model) {
                    return $model->created ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created)) : '-';
                },
                'format' => 'raw',
                'label' => 'Created Date From / To',
                'filter' => \kartik\daterange\DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'createdRangeTime',
                    'presetDropdown' => false,
                    'hideInput' => false,
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'd-M-Y',
                            'separator' => ' - '
                        ]
                    ]
                ])
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
