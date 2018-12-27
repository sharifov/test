<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApiLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getListByRole('agent');
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

$this->title = 'KPI';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'kh_user_id',
                'label' => 'Employee',
                'value' => function (\common\models\KpiHistory $model) {
                    return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->khUser->username);
                    },
                'format' => 'raw',
                'filter' => $userList,
                'visible' => !$isAgent
            ],
            [
                'attribute' => 'kh_date_dt',
                'label' => 'Month-Year',
                'value' => function (\common\models\KpiHistory $model) {
                    return (new DateTime($model->kh_date_dt))->format('M-Y');
                },
                'format' => 'raw',
            ],
            'kh_base_amount',
            'kh_commission_percent',
            [
                'attribute' => 'kh_bonus_active',
                'label' => 'Bonus active',
                'value' => function (\common\models\KpiHistory $model) {
                    return ($model->kh_bonus_active)?"Yes":"No";
                },
                'format' => 'raw',
                //'contentOptions' => ['class' => 'text-center'],
                'filter' => [0 => 'No', 1 => 'Yes']
            ],
            'kh_profit_bonus',
            'kh_manual_bonus',
            'kh_estimation_profit',
            [
                'label' => 'Salary',
                'value' => function (\common\models\KpiHistory $model) {
                    return $model->getSalary();
                },
                'format' => 'raw',
            ],
            'kh_agent_approved_dt',
            'kh_super_approved_dt',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{action}',
                'buttons' => [
                    'action' => function ($url, $model, $key) {
                            return Html::a('Details', Url::to([
                                'kpi/details',
                                'id' => $model['kh_id']
                            ]), [
                                'class' => 'btn btn-info btn-xs',
                                'target' => '_blank',
                                'data-pjax' => 0,
                                'title' => 'View details'
                            ]);
                    }
                ]
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
