<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\MonthColumn;
use yii\grid\ActionColumn;
use src\model\user\entity\payroll\UserPayroll;
use src\model\user\entity\profit\search\UserProfitSearch;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use common\components\grid\UserSelect2Column;

/* @var $this yii\web\View */
/* @var $searchModel src\model\user\entity\payroll\search\UserPayrollSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Payrolls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payroll-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(['id' => 'payroll-calc-form']) ?>


                <div class="row">
                    <div class="col-md-6">
                        <?= Html::label('Date', 'payroll-calc-form-date', [
                            'class' => 'control-label'
                        ]) ?>
                        <?= \kartik\date\DatePicker::widget([
                            'name' => 'date',
                            'pluginOptions' => [
                                'format' => 'yyyy-mm',
                                'minViewMode' => 'months',
                                'autoclose' => true,
                                'todayHighlight' => true
                            ],
                            'id' => 'payroll-calc-form-date',
                            'value' => date('Y-m')
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= Html::label('User', 'payroll-calc-form-user', [
                            'class' => 'control-label'
                        ]) ?>
                        <?= Html::dropDownList('userId', null, \common\models\Employee::getList(), [
                            'class' => 'form-control',
                            'prompt' => '--',
                            'id' => 'payroll-calc-form-user'
                        ])?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <?= Html::a('Calculate Payroll', ['calculate-user-payroll'], ['class' => 'btn btn-warning calc', 'data-action' => 1, 'style' => 'margin-bottom: 10px;']) ?>
            <?= Html::a('Recalculate Payroll', ['calculate-user-payroll'], ['class' => 'btn btn-info calc', 'data-action' => 2, 'style' => 'margin-bottom: 10px;']) ?>
        </div>
    </div>

    <br>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Payroll', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'payroll-index-pjax', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => static function (UserPayroll $model) {
            return ['class' => $model->getRowClass()];
        },
        'columns' => [
            'ups_id',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ups_user_id',
                'relation' => 'upsUser',
                'placeholder' => 'Select User',
            ],

            [
                'label' => 'Profit Count',
                'value' => static function (UserPayroll $model) {
                    $count = count($model->userProfits);
                    $search = (new ReflectionClass(UserProfitSearch::class));
                    $route = Url::toRoute(['/user-profit-crud/index', $search->getShortName() . '[up_payroll_id]' => $model->ups_id]);
                    return ($count ? Html::a($count, $route, [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]) : $count);
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Payment Count',
                'value' => static function (UserPayroll $model) {
                    $count = count($model->userPayments);
                    $search = (new ReflectionClass(UserProfitSearch::class));
                    $route = Url::toRoute(['/user-payment-crud/index', $search->getShortName() . '[upt_payroll_id]' => $model->ups_id]);
                    return ($count ? Html::a($count, $route, [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]) : $count);
                },
                'format' => 'raw'
            ],
            [
                'class' => MonthColumn::class,
                'attribute' => 'ups_month',
            ],
//            'ups_month:MonthNameByMonthNumber',
            'ups_year',
            'ups_base_amount',
            'ups_profit_amount',
            'ups_tax_amount',
            'ups_payment_amount',
            'ups_total_amount',
            [
                'attribute' => 'ups_agent_status_id',
                'value' => static function (UserPayroll $model) {
                    return UserPayroll::asFormatAgent($model->ups_agent_status_id);
                },
                'filter' => UserPayroll::getAgentStatusList(),
                'format' => 'raw'
            ],
            [
                'attribute' => 'ups_status_id',
                'value' => static function (UserPayroll $model) {
                    return UserPayroll::asFormat($model->ups_status_id);
                },
                'filter' => UserPayroll::getStatusList(),
                'format' => 'raw'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ups_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ups_updated_dt',
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <?php
    $js = <<<JS
        $('.calc').on('click', function (e) {
            e.preventDefault();
            var btn = $(this);
            var textBtn = btn.html();
            var loading = textBtn + ' <i class="fa fa-spin fa-spinner"></i>';
            var url = btn.attr('href');
            var formData = new FormData($('#payroll-calc-form')[0]);
            var action = btn.data('action');
            formData.append('action', action);
            
            $.ajax({
                type: 'post',
                data: formData,
                dataType: 'json',
                url: url,
                cache: false,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    btn.html(loading).prop('disabled', true);
                },
                success: function (data) {
                    if (data.error) {
                        createNotifyByObject({
                            title: 'Attention!',
                            text: data.message,
                            type: 'warning'
                        });
                    } else {
                        createNotifyByObject({
                            title: 'Success',
                            text: data.message,
                            type: 'success'
                        });
                        
                        $.pjax.reload({container: '#payroll-index-pjax', timeout: 2000, pushState: false, replace: false});
                    }    
                },
                error: function (obj) {
                    var message = 'Internal Server Error.';
                    if (obj.status === 403) {
                        message = 'Permission Denied';
                    }
                    createNotifyByObject({
                        title: 'Error',
                        text: message,
                        type: 'error'                
                    });  
                },
                complete: function () {
                    btn.html(textBtn).removeAttr('disabled');
                }
            })
        });
JS;
    $this->registerJs($js);
    ?>

</div>
