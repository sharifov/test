<?php

use common\models\Employee;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View
 * @var $searchModel common\models\search\KpiHistorySearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $isAgent bool
 * @var Employee $user
 */

$user = Yii::$app->user->identity;

if ($user->isAdmin()) {
    $userList = \common\models\Employee::getListByRole(Employee::ROLE_AGENT);
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
}

$this->title = 'KPI History';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php if (!$isAgent) :?>
        <div class="form-inline">
            <?php
            $form = ActiveForm::begin([
                'method' => 'post',
            ]);
            ?>
            <?= $form->field($model, 'date_dt', ['template' => "{label}{input}"])
                ->widget(
                    \dosamigos\datepicker\DatePicker::class,
                    [
                        'inline' => false,
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'M-yyyy',
                            'todayBtn' => false,
                            'minViewMode' => 1
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'placeholder' => 'Choose Date'
                        ],
                    ]
                )->label('Date');?>
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-search"></i> Calculate salary by month', ['class' => 'btn btn-info','style' => 'margin-bottom:10px;']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    <?php endif;?>

    <?= GridView::widget([
        'id' => 'kpi-gv',
        'dataProvider' => $dataProvider,
        'rowOptions' => function (\common\models\KpiHistory $model) {
            if (!empty($model->kh_super_approved_dt) && !empty($model->kh_agent_approved_dt)) {
                return ['class' => 'info'];
            } elseif (!empty($model->kh_super_approved_dt) || !empty($model->kh_agent_approved_dt)) {
                return ['class' => 'warning'];
            }
        },
        'filterModel' => $searchModel,
        'columns' => [
            /*[
                'attribute' => 'kh_user_id',
                'label' => 'Employee',
                'value' => static function (\common\models\KpiHistory $model) {
                    return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->khUser->username);
                },
                'format' => 'raw',
                'filter' => $userList,
                'visible' => !$isAgent
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'label' => 'Employee',
                'attribute' => 'kh_user_id',
                'relation' => 'khUser',
                'placeholder' => 'Select User',
                'visible' => !$isAgent
            ],

            [
                'attribute' => 'kh_date_dt',
                'label' => 'Month-Year',
                'value' => static function (\common\models\KpiHistory $model) {
                    return (new DateTime($model->kh_date_dt))->format('M-Y');
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'kh_date_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'M-yyyy',
                        'minViewMode' => 1
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
                'contentOptions' => [
                    'style' => 'width: 180px;text-align:center;'
                ]
            ],
            'kh_base_amount',
            'kh_commission_percent',
            [
                'attribute' => 'kh_bonus_active',
                'label' => 'Bonus active',
                'value' => static function (\common\models\KpiHistory $model) {
                    return ($model->kh_bonus_active) ? "Yes" : "No";
                },
                'format' => 'raw',
                //'contentOptions' => ['class' => 'text-center'],
                'filter' => [0 => 'No', 1 => 'Yes']
            ],
            'kh_profit_bonus',
            'kh_manual_bonus',
            [
                'attribute' => 'kh_estimation_profit',
                'visible' => !$isAgent,
            ],
            [
                'label' => 'Salary',
                'value' => static function (\common\models\KpiHistory $model) {
                    return $model->getSalary();
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Agent approved',
                'attribute' => 'kh_agent_approved_dt',
                'value' => static function ($model) {
                    return $model->kh_agent_approved_dt ? Yii::$app->formatter->asDatetime(strtotime($model->kh_agent_approved_dt)) : '-';
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'kh_agent_approved_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
                'contentOptions' => [
                    'style' => 'width: 180px;text-align:center;'
                ]
            ],
            [
                'label' => 'Super approved',
                'attribute' => 'kh_super_approved_dt',
                'value' => static function ($model) {
                    return $model->kh_super_approved_dt ? Yii::$app->formatter->asDatetime(strtotime($model->kh_super_approved_dt)) : '-';
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'kh_super_approved_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
                'contentOptions' => [
                    'style' => 'width: 180px;text-align:center;'
                ]
            ],
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
