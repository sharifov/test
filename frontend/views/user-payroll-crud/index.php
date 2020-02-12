<?php

use sales\model\user\payroll\UserPayroll;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use sales\yii\grid\UserColumn;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\payroll\search\UserPayrollSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Payrolls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payroll-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Payroll', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Calc User Payroll', ['create'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(); ?>
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

</div>
