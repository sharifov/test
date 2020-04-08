<?php

use common\models\LeadProfitType;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadProfitType */

$this->title = 'Lead Profit Type - ' . LeadProfitType::getProfitTypeName($model->lpt_profit_type_id);
$this->params['breadcrumbs'][] = ['label' => 'Lead Profit Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-profit-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->lpt_profit_type_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->lpt_profit_type_id], [
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
            [
                'attribute' => 'lpt_profit_type_id',
                'value' => static function (LeadProfitType $model) {
                    return LeadProfitType::getProfitTypeName($model->lpt_profit_type_id);
                }
            ],
            'lpt_diff_rule:percentInteger',
            'lpt_commission_min:percentInteger',
            'lpt_commission_max:percentInteger',
            'lpt_commission_fix:percentInteger',
            'lpt_created_user_id:userName',
            'lpt_updated_user_id:userName',
            'lpt_created_dt:byUserDateTime',
            'lpt_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
