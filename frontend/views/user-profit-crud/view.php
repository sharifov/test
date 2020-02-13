<?php

use sales\model\user\entity\profit\UserProfit;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\profit\UserProfit */

$this->title = $model->up_id;
$this->params['breadcrumbs'][] = ['label' => 'User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-profit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->up_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->up_id], [
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
            'up_id',
            'up_user_id:UserName',
            'up_lead_id',
            'up_order_id',
            'up_product_quote_id',
            'up_percent',
            'up_profit',
            'up_split_percent',
            'up_amount',
            [
                'attribute' => 'up_status_id',
                'value' => static function (UserProfit $model) {
                    return UserProfit::getStatusName($model->up_status_id);
                }
            ],
            'up_created_dt:byUserDateTime',
            'up_updated_dt:byUserDateTime',
            'up_payroll_id',
            [
                'attribute' => 'up_type_id',
                'value' => static function (UserProfit $model) {
                    return UserProfit::getTypeName($model->up_type_id);
                }
            ],
        ],
    ]) ?>

</div>
