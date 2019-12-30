<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethod */

$this->title = $model->pm_name;
$this->params['breadcrumbs'][] = ['label' => 'Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-method-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pm_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pm_id], [
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
            'pm_id',
            'pm_name',
            'pm_short_name',
            'pm_enabled:boolean',
            [
                'attribute' => 'pm_category_id',
                'value' => static function (\common\models\PaymentMethod $model) {
                    return $model->getCategoryName();
                },
            ],
            'pm_updated_user_id:UserName',
            'pm_updated_dt:DateTimeByUserDt',
        ],
    ]) ?>

</div>
