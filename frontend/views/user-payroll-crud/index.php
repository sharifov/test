<?php

use sales\model\user\entity\payroll\UserPayroll;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use sales\yii\grid\UserColumn;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\payroll\search\UserPayrollSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Payrolls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payroll-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-2">
            <?php $form = ActiveForm::begin(['id' => 'payroll-calc-form']) ?>

                <?= \kartik\date\DatePicker::widget([
                    'name' => 'date',
					'pluginOptions' => [
						'format' => 'yyyy-mm',
						'minViewMode'=>'months',
						'autoclose' => true,
						'todayHighlight' => true
					]
				]) ?>

                <?= Html::dropDownList('userId', null, \common\models\Employee::getList(), [
                    'class' => 'form-control',
                    'prompt' => '--'
                ])?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <br>

    <p>
        <?= Html::a('Create User Payroll', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Calculate Payroll', ['calculate-user-payroll'], ['class' => 'btn btn-warning calc', 'data-action' => 1]) ?>
        <?= Html::a('Recalculate Payroll', ['calculate-user-payroll'], ['class' => 'btn btn-info calc', 'data-action' => 2]) ?>
    </p>

    <?php Pjax::begin(['id' => 'payroll-index-pjax']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ups_id',
			[
				'class' => UserColumn::class,
				'attribute' => 'ups_user_id',
				'relation' => 'upsUser'
			],
            'ups_month:MonthNameByMonthNumber',
            'ups_year',
            'ups_base_amount',
            'ups_profit_amount',
            'ups_tax_amount',
            'ups_payment_amount',
            'ups_total_amount',
            [
                'attribute' => 'ups_agent_status_id',
                'value' => static function (UserPayroll $model) {
                    return UserPayroll::getAgentStatusName($model->ups_agent_status_id);
                },
                'filter' => UserPayroll::getAgentStatusList()
            ],
            [
                'attribute' => 'ups_status_id',
                'value' => static function (UserPayroll $model) {
                    return UserPayroll::getStatusName($model->ups_status_id);
                },
                'filter' => UserPayroll::getStatusList()
            ],
            'ups_created_dt:ByUserDateTime',
            'ups_updated_dt:ByUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
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
                        new PNotify({
                            title: 'Error',
                            text: data.message,
                            type: 'error'
                        });
                    } else {
                        new PNotify({
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
                    new PNotify({
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
