<?php

use sales\model\user\entity\payroll\UserPayroll;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\payroll\UserPayroll */

$this->title = $model->upsUser->username . ' - ' . UserPayroll::getAgentStatusName($model->ups_agent_status_id) . ' - ' . UserPayroll::getStatusName($model->ups_status_id);
$this->params['breadcrumbs'][] = ['label' => 'User Payrolls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-payroll-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ups_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ups_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ups_id',
            'ups_user_id:UserName',
            'ups_month:MonthNameByMonthNumber',
            'ups_year',
            'ups_base_amount',
            'ups_profit_amount',
            'ups_tax_amount',
            'ups_payment_amount',
            'ups_total_amount',
            'ups_agent_status_id:UserPayrollAgentStatusName',
            'ups_status_id:UserPayrollStatusName',
            'ups_created_dt:byUserDateTime',
            'ups_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
