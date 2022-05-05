<?php

use dosamigos\datepicker\DatePicker;
use modules\product\src\entities\productType\ProductType;
use src\auth\Auth;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadCallExpertSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Call Experts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-call-expert-index">
    <h1><span class="fa fa-bell-o"></span> <?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Lead Call Expert', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <div class="col-md-3">
            <?php
            echo \kartik\daterange\DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'date_range',
                'useWithAddon' => true,
                'presetDropdown' => true,
                'hideInput' => true,
                'convertFormat' => true,
                'startAttribute' => 'datetime_start',
                'endAttribute' => 'datetime_end',
                'pluginOptions' => [
                    'timePicker' => true,
                    'timePickerIncrement' => 1,
                    'timePicker24Hour' => true,
                    'locale' => [
                        'format' => 'Y-m-d H:i',
                        'separator' => ' - '
                    ],
                    'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                ]
            ]);
            ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-success']) ?>
            <?= Html::submitButton('<i class="fa fa-close"></i> Reset', ['name' => 'reset', 'class' => 'btn btn-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'lce_id',
            //'lce_lead_id',
            [
                'label' => 'Lead UID',
                'attribute' => 'lce_lead_id',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    return Html::a($model->lce_lead_id, ['lead/view', 'gid' => $model->lceLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
            ],

            'lce_request_text:ntext',
            [
                'label' => 'Product',
                'attribute' => 'lce_product_id',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    return $model->product ? 'ID: ' . $model->product->pr_id . ' Name: ' . $model->product->pr_name : '-';
                },
            ],
            /*[
                'attribute' => 'lce_request_dt',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    return $model->lce_request_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lce_request_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'lce_request_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lce_request_dt'
            ],

            /*[
                'label' => 'Employee',
                'attribute' => 'lce_agent_user_id',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    return $model->lceAgentUser ? $model->lceAgentUser->username : '-';
                },
                'filter' => \common\models\Employee::getList()
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'label' => 'Employee',
                'attribute' => 'lce_agent_user_id',
                'relation' => 'lceAgentUser',
                'placeholder' => 'Select User',
            ],

            [
                'label' => 'User Role',
                'attribute' => 'employeeRole',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    $roles = $model->lceAgentUser->getRoles();
                    return $roles ? implode(', ', $roles) : '-';
                },
                //'format' => 'raw',
                'filter' => \common\models\Employee::getAllRoles(Auth::user())
            ],
            [
                'attribute' => 'lce_status_id',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    return $model->getStatusLabel();
                },
                'filter' => \common\models\LeadCallExpert::STATUS_LIST,
                'format' => 'raw',
            ],

            'lce_response_text:ntext',
            'lce_response_lead_quotes:ntext',
            //'lce_response_dt',
            /*[
                'attribute' => 'lce_response_dt',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    return $model->lce_response_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lce_response_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'lce_response_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lce_response_dt'
            ],

            //'lce_status_id',
            //'lce_agent_user_id',

            'lce_expert_user_id',
            'lce_expert_username',

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lce_updated_dt'
            ],

            /*[
                'attribute' => 'lce_updated_dt',
                'value' => static function (\common\models\LeadCallExpert $model) {
                    return $model->lce_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lce_updated_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'lce_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],*/

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
